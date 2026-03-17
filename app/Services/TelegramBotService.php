<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramBotService
{
    public function sendMessage(string $botToken, string $chatId, string $text, ?int $threadId = null): bool
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];
        if ($threadId) {
            $payload['message_thread_id'] = $threadId;
        }

        $response = Http::asForm()->post("https://api.telegram.org/bot{$botToken}/sendMessage", $payload);
        if (! $response->ok()) {
            \Log::warning('Telegram sendMessage failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    public function createForumTopic(string $botToken, string $chatId, string $name): ?int
    {
        $response = Http::asForm()->post("https://api.telegram.org/bot{$botToken}/createForumTopic", [
            'chat_id' => $chatId,
            'name' => $name,
        ]);

        if (! $response->ok()) {
            \Log::warning('Telegram createForumTopic failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();
        $threadId = $data['result']['message_thread_id'] ?? null;

        return $threadId ? (int) $threadId : null;
    }
}
