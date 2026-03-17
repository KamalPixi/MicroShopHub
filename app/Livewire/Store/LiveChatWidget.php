<?php

namespace App\Livewire\Store;

use App\Events\LiveChatMessageCreated;
use App\Models\LiveChatMessage;
use App\Models\LiveChatSession;
use App\Models\Setting;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Component;

class LiveChatWidget extends Component
{
    public bool $enabled = false;
    public bool $open = false;
    public string $sessionToken = '';
    public string $message = '';
    public array $messages = [];
    public int $lastMessageId = 0;
    public ?array $currentProduct = null;
    public string $customerName = '';
    public bool $nameCaptured = false;
    public bool $newAdminMessage = false;
    protected $listeners = [
        'live-chat-receive' => 'receiveBroadcast',
    ];

    public function mount(?int $productId = null): void
    {
        $settings = Setting::whereIn('key', [
            'live_chat_enabled',
            'admin_telegram_bot_token',
            'admin_telegram_chat_id',
        ])->pluck('value', 'key');

        $this->enabled = filter_var($settings['live_chat_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (! $this->enabled) {
            return;
        }

        $this->sessionToken = (string) session('live_chat_token');
        if (! $this->sessionToken) {
            $this->sessionToken = Str::uuid()->toString();
            session(['live_chat_token' => $this->sessionToken]);
        }

        $session = LiveChatSession::firstOrCreate([
            'session_token' => $this->sessionToken,
        ], [
            'status' => 'open',
        ]);

        $this->customerName = (string) ($session->customer_name ?? '');
        $this->nameCaptured = $this->customerName !== '';

        if ($productId) {
            $product = Product::select('id', 'name', 'slug')->find($productId);
            if ($product) {
                $this->currentProduct = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                ];
            }
        }

        $this->loadMessages();
    }

    public function saveName(): void
    {
        $name = trim($this->customerName);
        if ($name === '') {
            return;
        }

        $session = LiveChatSession::where('session_token', $this->sessionToken)->first();
        if (! $session) {
            return;
        }

        $newToken = $this->generateShortToken($name);
        if ($newToken && $newToken !== $session->session_token) {
            $session->update([
                'customer_name' => $name,
                'session_token' => $newToken,
            ]);
            $this->sessionToken = $newToken;
            session(['live_chat_token' => $newToken]);
            $this->dispatch('live-chat-token', token: $this->sessionToken);
        } else {
            $session->update(['customer_name' => $name]);
        }
        $this->nameCaptured = true;
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function sendMessage(): void
    {
        if (! $this->enabled) {
            return;
        }

        if (! $this->nameCaptured) {
            return;
        }

        $text = trim($this->message);
        if (! is_string($text)) {
            return;
        }
        if (mb_strlen($text) > 500) {
            $this->message = mb_substr($text, 0, 500);
            return;
        }
        if ($text === '') {
            return;
        }

        $session = LiveChatSession::where('session_token', $this->sessionToken)->first();
        if (! $session) {
            return;
        }

        $msg = LiveChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'customer',
            'message' => $text,
            'product_id' => $this->currentProduct['id'] ?? null,
            'meta' => $this->currentProduct ? ['product' => $this->currentProduct] : null,
        ]);

        $session->update(['last_message_at' => now()]);

        $this->message = '';
        $this->loadMessages();
        $this->dispatch('live-chat-scroll');

        $this->dispatch('live-chat-telegram', messageId: $msg->id, sessionToken: $this->sessionToken);
        event(new LiveChatMessageCreated($msg));
    }

    public function shareProduct(): void
    {
        if (! $this->currentProduct) {
            return;
        }

        $productName = $this->currentProduct['name'];
        $productUrl = route('store.product.show', $this->currentProduct['slug']);
        $this->message = "Please share this product: {$productName} ({$productUrl})";
        $this->sendMessage();
    }

    protected function loadMessages(): void
    {
        $this->newAdminMessage = false;
        $session = LiveChatSession::where('session_token', $this->sessionToken)->first();
        if (! $session) {
            return;
        }

        $query = LiveChatMessage::where('session_id', $session->id)
            ->orderBy('id');

        if ($this->lastMessageId) {
            $query->where('id', '>', $this->lastMessageId);
        }

        $newMessages = $query->get();
        if ($newMessages->isEmpty()) {
            return;
        }

        foreach ($newMessages as $item) {
            $this->messages[] = [
                'id' => $item->id,
                'sender' => $item->sender,
                'message' => $item->message,
                'meta' => $item->meta,
                'created_at' => $item->created_at?->format('H:i'),
            ];
            if ($item->sender === 'admin') {
                $this->newAdminMessage = true;
            }
            $this->lastMessageId = $item->id;
        }
    }

    public function receiveBroadcast($payload = null): void
    {
        if (! is_array($payload) || ($payload['session_token'] ?? '') !== $this->sessionToken) {
            return;
        }

        $message = $payload['message'] ?? null;
        if (! is_array($message) || ! isset($message['id'])) {
            return;
        }

        foreach ($this->messages as $existing) {
            if (($existing['id'] ?? null) === $message['id']) {
                return;
            }
        }

        $this->messages[] = $message;
        $this->lastMessageId = max($this->lastMessageId, (int) $message['id']);

        if (($message['sender'] ?? '') === 'admin') {
            $this->open = true;
            $this->dispatch('live-chat-scroll');
        }
    }

    protected function generateShortToken(string $name): ?string
    {
        $prefix = strtoupper(preg_replace('/[^A-Z0-9]/', '', strtoupper($name)));
        $prefix = $prefix !== '' ? substr($prefix, 0, 4) : 'USER';

        for ($i = 0; $i < 5; $i++) {
            $suffix = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 4));
            $token = $prefix.'-'.$suffix;
            if (! LiveChatSession::where('session_token', $token)->exists()) {
                return $token;
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.store.live-chat-widget');
    }
}
