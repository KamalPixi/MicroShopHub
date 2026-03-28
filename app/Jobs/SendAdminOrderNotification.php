<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Setting;
use App\Services\TelegramBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAdminOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(): void
    {
        $order = Order::find($this->orderId);
        if (! $order) {
            return;
        }

        $settings = Setting::whereIn('key', [
            'admin_notify_email_enabled',
            'admin_notify_email_address',
            'admin_notify_telegram_enabled',
            'admin_telegram_bot_token',
            'admin_telegram_chat_id',
            'mail_from_address',
            'mail_from_name',
        ])->pluck('value', 'key');

        $orderTotal = number_format((float) $order->total, 2);
        $currencyCode = $order->currency_code ?? 'BDT';
        $messageText = "New order received\n".
            "Order: {$order->order_number}\n".
            "Total: {$currencyCode} {$orderTotal}\n".
            "Payment: ".($order->payment_method ?? 'N/A');

        if (filter_var($settings['admin_notify_email_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $toEmail = $settings['admin_notify_email_address'] ?? null;
            if ($toEmail) {
                $fromAddress = trim((string) ($settings['mail_from_address'] ?? ''));
                $fromName = trim((string) ($settings['mail_from_name'] ?? ''));
                Mail::raw($messageText, function ($message) use ($toEmail, $fromAddress, $fromName) {
                    if ($fromAddress) {
                        $message->from($fromAddress, $fromName ?: null);
                    }
                    $message->to($toEmail);
                    $message->subject('New order received');
                });
            }
        }

        if (filter_var($settings['admin_notify_telegram_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $botToken = $settings['admin_telegram_bot_token'] ?? null;
            $chatId = $settings['admin_telegram_chat_id'] ?? null;
            if ($botToken && $chatId) {
                app(TelegramBotService::class)->sendMessage($botToken, $chatId, $messageText);
            }
        }
    }
}
