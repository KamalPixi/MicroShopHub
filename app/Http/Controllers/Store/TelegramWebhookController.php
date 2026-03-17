<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Events\LiveChatMessageCreated;
use App\Models\LiveChatMessage;
use App\Models\LiveChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('telegram webhook', [$request->all()]);

        $secret = config('services.telegram.webhook_secret');
        $provided = $request->header('X-Telegram-Bot-Api-Secret-Token');
        if ($secret && $provided !== $secret) {
            return response()->json(['ok' => false], 403);
        }

        $message = $request->input('message');
        if (! $message || empty($message['text'])) {
            return response()->json(['ok' => true]);
        }

        $text = trim((string) $message['text']);
        $sessionToken = '';
        $replyText = '';

        $threadId = $message['message_thread_id'] ?? null;
        if ($threadId) {
            $session = LiveChatSession::where('telegram_thread_id', $threadId)->first();
            if ($session) {
                $sessionToken = $session->session_token;
                $replyText = $text;
            }
        } elseif (str_starts_with($text, '/reply')) {
            // Format: /reply <session_token> <message>
            $parts = explode(' ', $text, 3);
            if (count($parts) < 3) {
                return response()->json(['ok' => true]);
            }
            $sessionToken = trim($parts[1]);
            $replyText = trim($parts[2]);
        } elseif (! empty($message['reply_to_message']['text'])) {
            // Replying directly to the bot message
            $replyText = $text;
            $sourceText = (string) $message['reply_to_message']['text'];
            if (preg_match('/Session:\\s*([A-Z0-9\\-]+)/', $sourceText, $matches)) {
                $sessionToken = $matches[1] ?? '';
            }
        }

        if (! $sessionToken || ! $replyText) {
            return response()->json(['ok' => true]);
        }

        $session = LiveChatSession::where('session_token', $sessionToken)->first();
        if (! $session) {
            return response()->json(['ok' => true]);
        }

        $created = LiveChatMessage::create([
            'session_id' => $session->id,
            'sender' => 'admin',
            'message' => $replyText,
            'delivery_status' => 'delivered',
            'delivered_at' => now(),
        ]);

        $session->update(['last_message_at' => now()]);
        event(new LiveChatMessageCreated($created));

        return response()->json(['ok' => true]);
    }
}
