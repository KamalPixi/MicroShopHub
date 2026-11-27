<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendcloudWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $rawPayload = $request->getContent();
        $signature = (string) $request->header('Sendcloud-Signature', '');

        if ($signature === '') {
            return response()->json(['error' => 'Missing Sendcloud-Signature header'], 401);
        }

        $keys = Setting::whereIn('key', [
            'sendcloud_mode',
            'sendcloud_test_secret_key',
            'sendcloud_secret_key',
            'sendcloud_webhook_secret',
        ])->pluck('value', 'key')->toArray();

        $candidateSecrets = array_values(array_filter(array_unique([
            trim((string) ($keys['sendcloud_webhook_secret'] ?? '')),
            trim((string) ($keys['sendcloud_test_secret_key'] ?? '')),
            trim((string) ($keys['sendcloud_secret_key'] ?? '')),
        ])));

        if (empty($candidateSecrets)) {
            return response()->json(['error' => 'Sendcloud secret key is not configured'], 401);
        }

        $verified = false;
        foreach ($candidateSecrets as $secret) {
            $expected = hash_hmac('sha256', $rawPayload, $secret);
            if (hash_equals($expected, $signature)) {
                $verified = true;
                break;
            }
        }

        if (! $verified) {
            Log::warning('Sendcloud webhook signature mismatch');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = json_decode($rawPayload, true);
        if (! is_array($payload)) {
            return response()->json(['error' => 'Invalid JSON payload'], 400);
        }

        $action = (string) ($payload['action'] ?? '');
        $parcel = $payload['parcel'] ?? null;

        if (is_array($parcel)) {
            $this->syncOrderFromParcel($parcel, $action);
        }

        return response()->json(['received' => true]);
    }

    protected function syncOrderFromParcel(array $parcel, string $action): void
    {
        $parcelId = $parcel['id'] ?? null;
        $orderNumber = $parcel['order_number'] ?? null;

        $order = null;
        if ($parcelId) {
            $order = Order::query()->where('sendcloud_parcel_id', $parcelId)->first();
        }
        if (! $order && $orderNumber) {
            $order = Order::query()->where('order_number', $orderNumber)->first();
        }

        if (! $order) {
            Log::info('Sendcloud webhook received for unknown order', [
                'action' => $action,
                'parcel_id' => $parcelId,
                'order_number' => $orderNumber,
            ]);
            return;
        }

        $statusMessage = $parcel['status']['message'] ?? $order->shipping_status;
        $statusId = (int) ($parcel['status']['id'] ?? 0);
        $labelUrl = $parcel['label']['label_printer']
            ?? ($parcel['label']['normal_printer'][0] ?? $order->shipping_label_url);

        $updates = [
            'shipping_provider' => 'sendcloud',
            'sendcloud_parcel_id' => $parcelId ?? $order->sendcloud_parcel_id,
            'tracking_number' => $parcel['tracking_number'] ?? $order->tracking_number,
            'tracking_url' => $parcel['tracking_url'] ?? $order->tracking_url,
            'shipping_label_url' => $labelUrl,
            'shipping_status' => $statusMessage,
        ];

        $normalizedMessage = strtolower((string) $statusMessage);
        if ($statusId === 11 || str_contains($normalizedMessage, 'delivered')) {
            $updates['status'] = 'delivered';
        } elseif ($statusId === 2000 || str_contains($normalizedMessage, 'cancelled')) {
            $updates['status'] = 'cancelled';
        } elseif ($order->status === 'pending' && $statusMessage) {
            $updates['status'] = 'processing';
        }

        $order->update($updates);
    }
}
