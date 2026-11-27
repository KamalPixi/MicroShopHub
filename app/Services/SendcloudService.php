<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Support\Facades\Http;

class SendcloudService
{
    protected string $baseUrl = 'https://panel.sendcloud.sc/api/v2';

    public function getConfig(): array
    {
        $settings = Setting::whereIn('key', [
            'sendcloud_enabled',
            'sendcloud_mode',
            'sendcloud_test_public_key',
            'sendcloud_test_secret_key',
            'sendcloud_public_key',
            'sendcloud_secret_key',
            'sendcloud_default_shipping_method_id',
            'sendcloud_request_label',
            'sendcloud_default_weight',
        ])->pluck('value', 'key')->toArray();

        $mode = ($settings['sendcloud_mode'] ?? 'test') === 'production' ? 'production' : 'test';
        $publicKey = $mode === 'test'
            ? trim((string) ($settings['sendcloud_test_public_key'] ?? ''))
            : trim((string) ($settings['sendcloud_public_key'] ?? ''));
        $secretKey = $mode === 'test'
            ? trim((string) ($settings['sendcloud_test_secret_key'] ?? ''))
            : trim((string) ($settings['sendcloud_secret_key'] ?? ''));

        return [
            'enabled' => filter_var($settings['sendcloud_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'mode' => $mode,
            'public_key' => $publicKey,
            'secret_key' => $secretKey,
            'default_shipping_method_id' => ! empty($settings['sendcloud_default_shipping_method_id'])
                ? (int) $settings['sendcloud_default_shipping_method_id']
                : null,
            'request_label' => filter_var($settings['sendcloud_request_label'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'default_weight' => is_numeric($settings['sendcloud_default_weight'] ?? null)
                ? number_format((float) $settings['sendcloud_default_weight'], 2, '.', '')
                : '1.00',
        ];
    }

    public function isEnabled(): bool
    {
        $config = $this->getConfig();

        return $config['enabled'] && $config['public_key'] !== '' && $config['secret_key'] !== '';
    }

    public function testConnection(?string $publicKey = null, ?string $secretKey = null): array
    {
        $config = $this->getConfig();
        $pub = trim((string) ($publicKey ?? $config['public_key']));
        $sec = trim((string) ($secretKey ?? $config['secret_key']));

        if ($pub === '' || $sec === '') {
            return [
                'ok' => false,
                'message' => 'Sendcloud Public Key and Secret Key are required.',
                'methods_count' => 0,
            ];
        }

        try {
            $response = Http::withBasicAuth($pub, $sec)
                ->acceptJson()
                ->timeout(15)
                ->get("{$this->baseUrl}/shipping_methods");

            if (! $response->successful()) {
                return [
                    'ok' => false,
                    'message' => 'Sendcloud API error (' . $response->status() . '): ' . ($response->json('error.message') ?? $response->body()),
                    'methods_count' => 0,
                ];
            }

            $methods = $response->json('shipping_methods', []);

            return [
                'ok' => true,
                'message' => 'Connected to Sendcloud. Found ' . count($methods) . ' shipping method(s).',
                'methods_count' => count($methods),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
                'methods_count' => 0,
            ];
        }
    }

    public function getShippingMethods(?string $publicKey = null, ?string $secretKey = null): array
    {
        $config = $this->getConfig();
        $pub = trim((string) ($publicKey ?? $config['public_key']));
        $sec = trim((string) ($secretKey ?? $config['secret_key']));

        if ($pub === '' || $sec === '') {
            throw new \RuntimeException('Sendcloud Public Key and Secret Key are not configured.');
        }

        $response = Http::withBasicAuth($pub, $sec)
            ->acceptJson()
            ->timeout(20)
            ->get("{$this->baseUrl}/shipping_methods");

        if (! $response->successful()) {
            throw new \RuntimeException('Sendcloud API error (' . $response->status() . '): ' . ($response->json('error.message') ?? $response->body()));
        }

        return $response->json('shipping_methods', []);
    }

    public function syncShippingMethods(?string $publicKey = null, ?string $secretKey = null): int
    {
        $methods = $this->getShippingMethods($publicKey, $secretKey);
        $defaultZone = ShippingZone::query()->firstOrCreate(['name' => 'Sendcloud']);
        $synced = 0;

        foreach ($methods as $method) {
            $externalId = (string) ($method['id'] ?? '');
            $name = trim((string) ($method['name'] ?? ''));
            if ($externalId === '' || $name === '') {
                continue;
            }

            $price = 0.0;
            if (! empty($method['countries']) && is_array($method['countries'])) {
                $firstCountry = $method['countries'][0] ?? [];
                if (isset($firstCountry['price'])) {
                    $price = (float) $firstCountry['price'];
                }
            }

            $existing = ShippingMethod::where('provider', 'sendcloud')
                ->where('external_id', $externalId)
                ->first();

            ShippingMethod::updateOrCreate(
                ['provider' => 'sendcloud', 'external_id' => $externalId],
                [
                    'shipping_zone_id' => $defaultZone->id,
                    'name' => $name,
                    'type' => 'flat_rate',
                    'cost' => $price,
                    'estimated_days' => 3,
                    'active' => $existing ? $existing->active : false,
                ]
            );
            $synced++;
        }

        return $synced;
    }

    public function createParcel(Order $order, ?int $sendcloudMethodId = null, ?bool $requestLabel = null): array
    {
        $config = $this->getConfig();

        if (! $config['enabled']) {
            throw new \RuntimeException('Sendcloud delivery gateway is currently disabled in Store Settings.');
        }
        if ($config['public_key'] === '' || $config['secret_key'] === '') {
            throw new \RuntimeException('Sendcloud Public Key and Secret Key are missing.');
        }

        $order->loadMissing(['shippingAddress', 'billingAddress', 'shippingMethod', 'user']);
        $address = $order->shippingAddress ?? $order->billingAddress;

        if (! $address) {
            throw new \RuntimeException('Order does not have a shipping or billing address.');
        }

        $methodId = $sendcloudMethodId;
        if (! $methodId && $order->shippingMethod && $order->shippingMethod->provider === 'sendcloud' && ! empty($order->shippingMethod->external_id)) {
            $methodId = (int) $order->shippingMethod->external_id;
        }
        if (! $methodId) {
            $methodId = $config['default_shipping_method_id'];
        }

        $shouldRequestLabel = $requestLabel ?? $config['request_label'];

        $parcelPayload = [
            'name' => $address->name ?: ($order->user?->name ?? 'Customer'),
            'address' => $address->address_line1 ?: '',
            'address_2' => $address->address_line2 ?: '',
            'city' => $address->city ?: '',
            'postal_code' => $address->postal_code ?: '',
            'country' => strtoupper((string) ($address->country_code ?: 'NL')),
            'email' => $address->email ?: ($order->user?->email ?? ''),
            'telephone' => $address->phone ?: '',
            'order_number' => (string) $order->order_number,
            'weight' => $config['default_weight'],
        ];

        if ($config['mode'] === 'test') {
            // Test Mode: never trigger paid carrier labels; use free Unstamped Letter or draft parcel.
            if ($shouldRequestLabel) {
                $parcelPayload['request_label'] = true;
                $parcelPayload['ship_with'] = [
                    'type' => 'shipping_option_code',
                    'properties' => [
                        'shipping_option_code' => 'sendcloud:letter',
                    ],
                ];
            } else {
                $parcelPayload['request_label'] = false;
            }
        } else {
            $parcelPayload['request_label'] = (bool) ($shouldRequestLabel && $methodId);
            if ($methodId) {
                $parcelPayload['shipment'] = ['id' => $methodId];
            }
        }

        if (! empty($order->sendcloud_service_point_id)) {
            $parcelPayload['to_service_point'] = (int) $order->sendcloud_service_point_id;
        }

        $response = Http::withBasicAuth($config['public_key'], $config['secret_key'])
            ->acceptJson()
            ->timeout(20)
            ->post("{$this->baseUrl}/parcels", ['parcel' => $parcelPayload]);

        if (! $response->successful()) {
            $errorMsg = $response->json('error.message') ?? $response->body();
            throw new \RuntimeException('Sendcloud parcel creation failed (' . $response->status() . '): ' . $errorMsg);
        }

        $parcel = $response->json('parcel', []);
        $this->updateOrderFromParcel($order, $parcel);

        return $parcel;
    }

    public function getServicePoints(string $countryCode, string $postalCode, ?string $carrier = null): array
    {
        $config = $this->getConfig();
        if ($config['public_key'] === '' || $config['secret_key'] === '') {
            throw new \RuntimeException('Sendcloud API credentials are not configured.');
        }

        $query = [
            'country' => strtoupper(trim($countryCode)),
            'postal_code' => trim($postalCode),
        ];

        if ($carrier) {
            $query['carrier'] = $carrier;
        }

        $response = Http::withBasicAuth($config['public_key'], $config['secret_key'])
            ->acceptJson()
            ->timeout(15)
            ->get('https://servicepoints.sendcloud.sc/api/v2/service-points/', $query);

        if (! $response->successful()) {
            throw new \RuntimeException('Sendcloud Service Points error (' . $response->status() . '): ' . ($response->json('error.message') ?? $response->body()));
        }

        $points = $response->json();
        if (! is_array($points)) {
            return [];
        }

        return collect($points)->take(12)->map(function ($point) {
            return [
                'id' => $point['id'] ?? null,
                'name' => $point['name'] ?? 'Pickup Point',
                'street' => trim(($point['street'] ?? '') . ' ' . ($point['house_number'] ?? '')),
                'postal_code' => $point['postal_code'] ?? '',
                'city' => $point['city'] ?? '',
                'carrier' => $point['carrier'] ?? '',
                'distance' => $point['distance'] ?? null,
            ];
        })->filter(fn ($p) => ! empty($p['id']))->values()->all();
    }

    public function refreshParcel(Order $order): array
    {
        $config = $this->getConfig();
        if ($config['public_key'] === '' || $config['secret_key'] === '') {
            throw new \RuntimeException('Sendcloud API credentials are missing.');
        }
        if (! $order->sendcloud_parcel_id) {
            throw new \RuntimeException('This order does not have a Sendcloud parcel ID yet.');
        }

        $response = Http::withBasicAuth($config['public_key'], $config['secret_key'])
            ->acceptJson()
            ->timeout(15)
            ->get("{$this->baseUrl}/parcels/{$order->sendcloud_parcel_id}");

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to fetch Sendcloud parcel (' . $response->status() . '): ' . ($response->json('error.message') ?? $response->body()));
        }

        $parcel = $response->json('parcel', []);
        $this->updateOrderFromParcel($order, $parcel);

        return $parcel;
    }

    protected function updateOrderFromParcel(Order $order, array $parcel): void
    {
        $labelUrl = $parcel['label']['label_printer'] ?? ($parcel['label']['normal_printer'][0] ?? $order->shipping_label_url);

        $order->update([
            'shipping_provider' => 'sendcloud',
            'sendcloud_parcel_id' => $parcel['id'] ?? $order->sendcloud_parcel_id,
            'tracking_number' => $parcel['tracking_number'] ?? $order->tracking_number,
            'tracking_url' => $parcel['tracking_url'] ?? $order->tracking_url,
            'shipping_label_url' => $labelUrl,
            'shipping_status' => $parcel['status']['message'] ?? $order->shipping_status,
        ]);
    }
}
