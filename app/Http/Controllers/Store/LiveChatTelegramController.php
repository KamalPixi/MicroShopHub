<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Events\LiveChatMessageCreated;
use App\Models\LiveChatMessage;
use App\Models\LiveChatSession;
use App\Models\Setting;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;

class LiveChatTelegramController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->validate([
            'message_id' => 'required|integer',
            'session_token' => 'required|string',
        ]);

        $session = LiveChatSession::where('session_token', $data['session_token'])->first();
        if (! $session) {
            return response()->noContent();
        }

        $message = LiveChatMessage::where('id', $data['message_id'])
            ->where('session_id', $session->id)
            ->where('sender', 'customer')
            ->first();

        if (! $message) {
            return response()->noContent();
        }

        $settings = Setting::whereIn('key', [
            'admin_telegram_bot_token',
            'admin_telegram_chat_id',
        ])->pluck('value', 'key');

        $botToken = $settings['admin_telegram_bot_token'] ?? null;
        $chatId = $settings['admin_telegram_chat_id'] ?? null;
        if (! $botToken || ! $chatId) {
            return response()->noContent();
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
        if ($sent) {
            $message->update([
                'delivery_status' => 'delivered',
                'delivered_at' => now(),
            ]);
            event(new LiveChatMessageCreated($message->fresh()));
        }

        return response()->noContent();
    }
}
