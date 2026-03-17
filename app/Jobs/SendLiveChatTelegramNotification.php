<?php

namespace App\Jobs;

use App\Models\LiveChatMessage;
use App\Models\LiveChatSession;
use App\Models\Setting;
use App\Services\TelegramBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLiveChatTelegramNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $sessionId;
    public int $messageId;

    public function __construct(int $sessionId, int $messageId)
    {
        $this->sessionId = $sessionId;
        $this->messageId = $messageId;
    }

    public function handle(): void
    {
        $session = LiveChatSession::query()->find($this->sessionId);
        $message = LiveChatMessage::query()->find($this->messageId);
        if (! $session || ! $message) {
            return;
        }

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

        $threadId = $session->telegram_thread_id;
        if (! $threadId) {
            $topicName = $customerName.' - '.$session->session_token;
            $threadId = app(TelegramBotService::class)->createForumTopic($botToken, $chatId, $topicName);
            if ($threadId) {
                $session->update(['telegram_thread_id' => $threadId]);
            }
        }

        $sent = app(TelegramBotService::class)->sendMessage($botToken, $chatId, $text, $threadId);
        if (! $sent && $threadId) {
            app(TelegramBotService::class)->sendMessage($botToken, $chatId, $text);
        }
    }
}
