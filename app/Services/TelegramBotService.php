<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramBotService
{
    public function sendMessage(string $botToken, string $chatId, string $text): void
    {
        Http::asForm()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }
}
