<?php

namespace App\Livewire\Store;

use App\Models\LiveChatMessage;
use App\Models\LiveChatSession;
use App\Models\Setting;
use App\Models\Product;
use App\Services\TelegramBotService;
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

        $session->update(['customer_name' => $name]);
        $this->nameCaptured = true;
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function pollMessages(): void
    {
        $this->loadMessages();
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

        $this->notifyTelegram($session, $msg);
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
            $this->lastMessageId = $item->id;
        }
    }

    protected function notifyTelegram(LiveChatSession $session, LiveChatMessage $message): void
    {
        $settings = Setting::whereIn('key', [
            'admin_telegram_bot_token',
            'admin_telegram_chat_id',
        ])->pluck('value', 'key');

        $botToken = $settings['admin_telegram_bot_token'] ?? null;
        $chatId = $settings['admin_telegram_chat_id'] ?? null;
        if (! $botToken || ! $chatId) {
            return;
        }

        $customerName = $session->customer_name ?: 'Anonymous';
        $text = "<b>New live chat message</b>\n".
            "Name: {$customerName}\n".
            "Session: <code>{$session->session_token}</code>\n".
            "Message: {$message->message}";

        if (! empty($message->meta['product'])) {
            $product = $message->meta['product'];
            $text .= "\nProduct: {$product['name']}";
            $text .= "\n".route('store.product.show', $product['slug']);
        }

        app(TelegramBotService::class)->sendMessage($botToken, $chatId, $text);
    }

    public function render()
    {
        return view('livewire.store.live-chat-widget');
    }
}
