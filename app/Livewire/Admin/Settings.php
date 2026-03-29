<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;
use App\Models\Currency;
use App\Models\Country;
use App\Jobs\TestQueueJob;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class Settings extends Component
{
    use WithFileUploads;

    public $currencies = [];
    public $countries = [];

    public $settings = [
        // General
        'shop_logo' => '',
        'branding_color' => '#000000',
        'secondary_color' => '#6B7280',
        'accent_color' => '#F59E0B',
        'shop_name' => '',
        'site_title' => '',
        'store_default_locale' => 'en',
        'store_language_en_enabled' => true,
        'store_language_bn_enabled' => true,
        
        // SEO
        'meta_description' => '',
        'meta_keywords' => '',
        
        // Social
        'social_facebook' => '',
        'social_twitter' => '',
        'social_instagram' => '',
        'social_linkedin' => '',
        
        // Payment Gateways
        'stripe_api_key' => '',
        'stripe_label' => '',
        
        'paypal_api_key' => '',
        'paypal_label' => '',
        
        'sslcommerz_store_id' => '',
        'sslcommerz_api_key' => '',
        'sslcommerz_label' => '',
        'sslcommerz_sandbox' => false,

        // bKash
        'bkash_base_url' => '',
        'bkash_app_key' => '',
        'bkash_app_secret' => '',
        'bkash_username' => '',
        'bkash_password' => '',
        
        // Cash on Delivery
        'cod_label' => 'Cash on Delivery',
        'cod_enabled' => false,
        
        // Operations
        'currency' => 'BDT',
        'tax_rate' => 0,
        'email' => '',
        'phone' => '',

        // Email (Campaigns)
        'mail_host' => '',
        'mail_port' => '',
        'mail_username' => '',
        'mail_password' => '',
        'mail_encryption' => 'tls',
        'mail_from_address' => '',
        'mail_from_name' => '',

        // Admin Notifications
        'admin_notify_email_enabled' => false,
        'admin_notify_email_address' => '',
        'admin_notify_telegram_enabled' => false,
        'admin_telegram_bot_token' => '',
        'admin_telegram_chat_id' => '',
        'live_chat_enabled' => false,
        'admin_telegram_webhook_set' => false,
        'pusher_app_id' => '',
        'pusher_app_key' => '',
        'pusher_app_secret' => '',
        'pusher_app_cluster' => 'mt1',

        // Customer Authentication
        'customer_auth_email_otp_enabled' => false,
        'customer_auth_email_password_enabled' => true,
        'customer_auth_guest_checkout_enabled' => false,
    ];

    public $newCurrency = [
        'code' => '',
        'name' => '',
        'symbol' => '',
        'exchange_rate' => 1,
        'active' => true,
    ];

    public $newCountry = [
        'code' => '',
        'name' => '',
    ];

    public $logo;
    public $savedSection = '';
    public $telegramChatOptions = [];
    public $telegramFetchMessage = '';
    public $telegramWebhookMessage = '';
    public bool $telegramWebhookSet = false;
    public string $queueTestMessage = '';
    public array $offlinePaymentMethods = [];

    protected $rules = [
        'logo' => 'nullable|image|max:2048',
        'settings.branding_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        'settings.secondary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        'settings.accent_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        'settings.shop_name' => 'nullable|string|max:255',
        'settings.site_title' => 'nullable|string|max:255',
        'settings.store_default_locale' => 'required|in:en,bn',
        'settings.store_language_en_enabled' => 'boolean',
        'settings.store_language_bn_enabled' => 'boolean',
        'settings.meta_description' => 'nullable|string|max:500',
        'settings.meta_keywords' => 'nullable|string|max:255',
        'settings.social_facebook' => 'nullable|url',
        'settings.social_twitter' => 'nullable|url',
        'settings.social_instagram' => 'nullable|url',
        'settings.social_linkedin' => 'nullable|url',
        
        // Payment Validation
        'settings.stripe_api_key' => 'nullable|string|max:255',
        'settings.stripe_label' => 'nullable|string|max:255',
        'settings.paypal_api_key' => 'nullable|string|max:255',
        'settings.paypal_label' => 'nullable|string|max:255',
        'settings.sslcommerz_store_id' => 'nullable|string|max:255',
        'settings.sslcommerz_api_key' => 'nullable|string|max:255',
        'settings.sslcommerz_label' => 'nullable|string|max:255',
        'settings.sslcommerz_sandbox' => 'boolean',
        'settings.cod_label' => 'nullable|string|max:255',
        'settings.cod_enabled' => 'boolean',
        'settings.bkash_base_url' => 'nullable|string|max:255',
        'settings.bkash_app_key' => 'nullable|string|max:255',
        'settings.bkash_app_secret' => 'nullable|string|max:255',
        'settings.bkash_username' => 'nullable|string|max:255',
        'settings.bkash_password' => 'nullable|string|max:255',
        
        'settings.currency' => 'nullable|string|exists:currencies,code',
        'settings.tax_rate' => 'nullable|numeric|min:0|max:100',
        'settings.email' => 'nullable|email|max:255',
        'settings.phone' => 'nullable|string|max:50',
        'settings.mail_host' => 'nullable|string|max:255',
        'settings.mail_port' => 'nullable|numeric|min:1|max:65535',
        'settings.mail_username' => 'nullable|string|max:255',
        'settings.mail_password' => 'nullable|string|max:255',
        'settings.mail_encryption' => 'nullable|string|in:tls,ssl,none',
        'settings.mail_from_address' => 'nullable|email|max:255',
        'settings.mail_from_name' => 'nullable|string|max:255',
        'settings.admin_notify_email_enabled' => 'boolean',
        'settings.admin_notify_email_address' => 'nullable|email|max:255',
        'settings.admin_notify_telegram_enabled' => 'boolean',
        'settings.admin_telegram_bot_token' => 'nullable|string|max:255',
        'settings.admin_telegram_chat_id' => 'nullable|string|max:255',
        'settings.live_chat_enabled' => 'boolean',
        'settings.admin_telegram_webhook_set' => 'boolean',
        'settings.pusher_app_id' => 'nullable|string|max:255',
        'settings.pusher_app_key' => 'nullable|string|max:255',
        'settings.pusher_app_secret' => 'nullable|string|max:255',
        'settings.pusher_app_cluster' => 'nullable|string|max:255',
        'settings.customer_auth_email_otp_enabled' => 'boolean',
        'settings.customer_auth_email_password_enabled' => 'boolean',
        'settings.customer_auth_guest_checkout_enabled' => 'boolean',
    ];

    public function mount()
    {
        $this->currencies = Currency::query()->orderBy('code')->get();
        $this->countries = Country::query()->orderBy('name')->get();
        $existingSettings = Setting::all()->pluck('value', 'key')->toArray();
        
        foreach ($this->settings as $key => $value) {
            $this->settings[$key] = $existingSettings[$key] ?? $value;
        }

        $rawOffline = $existingSettings['offline_payment_methods'] ?? '[]';
        $decoded = is_string($rawOffline) ? json_decode($rawOffline, true) : $rawOffline;
        $this->offlinePaymentMethods = is_array($decoded) ? $decoded : [];

        // Force Boolean for Checkboxes
        $this->settings['sslcommerz_sandbox'] = filter_var($this->settings['sslcommerz_sandbox'], FILTER_VALIDATE_BOOLEAN);
        $this->settings['cod_enabled'] = filter_var($this->settings['cod_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN); // [NEW]
        $this->settings['customer_auth_email_otp_enabled'] = filter_var($this->settings['customer_auth_email_otp_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->settings['customer_auth_email_password_enabled'] = filter_var($this->settings['customer_auth_email_password_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->settings['customer_auth_guest_checkout_enabled'] = filter_var($this->settings['customer_auth_guest_checkout_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->settings['store_default_locale'] = in_array($this->settings['store_default_locale'] ?? 'en', ['en', 'bn'], true) ? $this->settings['store_default_locale'] : 'en';
        $this->settings['store_language_en_enabled'] = filter_var($this->settings['store_language_en_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->settings['store_language_bn_enabled'] = filter_var($this->settings['store_language_bn_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        if (! $this->settings['store_language_en_enabled'] && ! $this->settings['store_language_bn_enabled']) {
            $this->settings['store_language_en_enabled'] = true;
        }
        if (
            ($this->settings['store_default_locale'] === 'en' && ! $this->settings['store_language_en_enabled']) ||
            ($this->settings['store_default_locale'] === 'bn' && ! $this->settings['store_language_bn_enabled'])
        ) {
            $this->settings['store_default_locale'] = $this->settings['store_language_en_enabled'] ? 'en' : 'bn';
        }
        $this->settings['admin_notify_email_enabled'] = filter_var($this->settings['admin_notify_email_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->settings['admin_notify_telegram_enabled'] = filter_var($this->settings['admin_notify_telegram_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->settings['live_chat_enabled'] = filter_var($this->settings['live_chat_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->settings['admin_telegram_webhook_set'] = filter_var($this->settings['admin_telegram_webhook_set'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (! $this->settings['currency'] && $this->currencies->isNotEmpty()) {
            $this->settings['currency'] = $this->currencies->first()->code;
        }
    }

    public function addCountry()
    {
        $this->newCountry['code'] = strtoupper(trim((string) $this->newCountry['code']));
        $this->newCountry['name'] = trim((string) $this->newCountry['name']);

        $validated = $this->validate([
            'newCountry.code' => 'required|string|size:2|alpha|unique:countries,code',
            'newCountry.name' => 'required|string|max:120',
        ]);

        Country::query()->create([
            'code' => $validated['newCountry']['code'],
            'name' => $validated['newCountry']['name'],
            'active' => true,
        ]);

        $this->countries = Country::query()->orderBy('name')->get();

        $this->newCountry = [
            'code' => '',
            'name' => '',
        ];

        session()->flash('message', 'Country added successfully.');
    }

    public function removeCountry(string $code)
    {
        Country::query()->where('code', strtoupper($code))->delete();
        $this->countries = Country::query()->orderBy('name')->get();
    }

    public function addCurrency()
    {
        $this->newCurrency['code'] = strtoupper(trim((string) $this->newCurrency['code']));

        $validated = $this->validate([
            'newCurrency.code' => 'required|string|size:3|alpha|unique:currencies,code',
            'newCurrency.name' => 'required|string|max:255',
            'newCurrency.symbol' => 'required|string|max:10',
            'newCurrency.exchange_rate' => 'required|numeric|min:0.0001',
            'newCurrency.active' => 'boolean',
        ]);

        Currency::query()->create([
            'code' => strtoupper($validated['newCurrency']['code']),
            'name' => $validated['newCurrency']['name'],
            'symbol' => $validated['newCurrency']['symbol'],
            'exchange_rate' => $validated['newCurrency']['exchange_rate'],
            'active' => (bool) $validated['newCurrency']['active'],
            'is_default' => false,
        ]);

        $this->currencies = Currency::query()->orderBy('code')->get();
        $this->settings['currency'] = strtoupper($validated['newCurrency']['code']);

        $this->newCurrency = [
            'code' => '',
            'name' => '',
            'symbol' => '',
            'exchange_rate' => 1,
            'active' => true,
        ];

        session()->flash('message', 'Currency added successfully.');
    }

    public function save()
    {
        $this->saveAll();
    }

    public function saveGeneral()
    {
        if (! $this->settings['store_language_en_enabled'] && ! $this->settings['store_language_bn_enabled']) {
            $this->addError('settings.store_language_en_enabled', 'Enable at least one storefront language.');
            $this->savedSection = 'general';
            return;
        }

        if (
            ($this->settings['store_default_locale'] === 'en' && ! $this->settings['store_language_en_enabled']) ||
            ($this->settings['store_default_locale'] === 'bn' && ! $this->settings['store_language_bn_enabled'])
        ) {
            $this->settings['store_default_locale'] = $this->settings['store_language_en_enabled'] ? 'en' : 'bn';
        }

        $this->saveSettings([
            'shop_name',
            'site_title',
            'store_default_locale',
            'store_language_en_enabled',
            'store_language_bn_enabled',
            'branding_color',
            'secondary_color',
            'accent_color',
            'shop_logo',
        ], [
            'logo' => $this->rules['logo'],
            'settings.shop_name' => $this->rules['settings.shop_name'],
            'settings.site_title' => $this->rules['settings.site_title'],
            'settings.store_default_locale' => $this->rules['settings.store_default_locale'],
            'settings.store_language_en_enabled' => $this->rules['settings.store_language_en_enabled'],
            'settings.store_language_bn_enabled' => $this->rules['settings.store_language_bn_enabled'],
            'settings.branding_color' => $this->rules['settings.branding_color'],
            'settings.secondary_color' => $this->rules['settings.secondary_color'],
            'settings.accent_color' => $this->rules['settings.accent_color'],
        ], false, 'general');
    }

    public function saveSeo()
    {
        $this->saveSettings([
            'meta_description',
            'meta_keywords',
        ], [
            'settings.meta_description' => $this->rules['settings.meta_description'],
            'settings.meta_keywords' => $this->rules['settings.meta_keywords'],
        ], false, 'seo');
    }

    public function saveAuth()
    {
        $this->saveSettings([
            'customer_auth_email_otp_enabled',
            'customer_auth_email_password_enabled',
            'customer_auth_guest_checkout_enabled',
        ], [
            'settings.customer_auth_email_otp_enabled' => $this->rules['settings.customer_auth_email_otp_enabled'],
            'settings.customer_auth_email_password_enabled' => $this->rules['settings.customer_auth_email_password_enabled'],
            'settings.customer_auth_guest_checkout_enabled' => $this->rules['settings.customer_auth_guest_checkout_enabled'],
        ], true, 'auth');
    }

    public function savePayments()
    {
        $this->saveSettings([
            'cod_label',
            'cod_enabled',
            'sslcommerz_store_id',
            'sslcommerz_api_key',
            'sslcommerz_label',
            'sslcommerz_sandbox',
            'stripe_api_key',
            'stripe_label',
            'paypal_api_key',
            'paypal_label',
            'bkash_base_url',
            'bkash_app_key',
            'bkash_app_secret',
            'bkash_username',
            'bkash_password',
        ], [
            'settings.cod_label' => $this->rules['settings.cod_label'],
            'settings.cod_enabled' => $this->rules['settings.cod_enabled'],
            'settings.sslcommerz_store_id' => $this->rules['settings.sslcommerz_store_id'],
            'settings.sslcommerz_api_key' => $this->rules['settings.sslcommerz_api_key'],
            'settings.sslcommerz_label' => $this->rules['settings.sslcommerz_label'],
            'settings.sslcommerz_sandbox' => $this->rules['settings.sslcommerz_sandbox'],
            'settings.stripe_api_key' => $this->rules['settings.stripe_api_key'],
            'settings.stripe_label' => $this->rules['settings.stripe_label'],
            'settings.paypal_api_key' => $this->rules['settings.paypal_api_key'],
            'settings.paypal_label' => $this->rules['settings.paypal_label'],
            'settings.bkash_base_url' => $this->rules['settings.bkash_base_url'],
            'settings.bkash_app_key' => $this->rules['settings.bkash_app_key'],
            'settings.bkash_app_secret' => $this->rules['settings.bkash_app_secret'],
            'settings.bkash_username' => $this->rules['settings.bkash_username'],
            'settings.bkash_password' => $this->rules['settings.bkash_password'],
        ], false, 'payments');
    }

    public function saveCodGateway()
    {
        $this->saveSettings([
            'cod_label',
            'cod_enabled',
        ], [
            'settings.cod_label' => $this->rules['settings.cod_label'],
            'settings.cod_enabled' => $this->rules['settings.cod_enabled'],
        ], false, 'cod');
    }

    public function saveSslCommerzGateway()
    {
        $this->saveSettings([
            'sslcommerz_store_id',
            'sslcommerz_api_key',
            'sslcommerz_label',
            'sslcommerz_sandbox',
        ], [
            'settings.sslcommerz_store_id' => $this->rules['settings.sslcommerz_store_id'],
            'settings.sslcommerz_api_key' => $this->rules['settings.sslcommerz_api_key'],
            'settings.sslcommerz_label' => $this->rules['settings.sslcommerz_label'],
            'settings.sslcommerz_sandbox' => $this->rules['settings.sslcommerz_sandbox'],
        ], false, 'sslcommerz');
    }

    public function saveStripeGateway()
    {
        $this->saveSettings([
            'stripe_api_key',
            'stripe_label',
        ], [
            'settings.stripe_api_key' => $this->rules['settings.stripe_api_key'],
            'settings.stripe_label' => $this->rules['settings.stripe_label'],
        ], false, 'stripe');
    }

    public function savePaypalGateway()
    {
        $this->saveSettings([
            'paypal_api_key',
            'paypal_label',
        ], [
            'settings.paypal_api_key' => $this->rules['settings.paypal_api_key'],
            'settings.paypal_label' => $this->rules['settings.paypal_label'],
        ], false, 'paypal');
    }

    public function saveBkashGateway()
    {
        $this->saveSettings([
            'bkash_base_url',
            'bkash_app_key',
            'bkash_app_secret',
            'bkash_username',
            'bkash_password',
        ], [
            'settings.bkash_base_url' => $this->rules['settings.bkash_base_url'],
            'settings.bkash_app_key' => $this->rules['settings.bkash_app_key'],
            'settings.bkash_app_secret' => $this->rules['settings.bkash_app_secret'],
            'settings.bkash_username' => $this->rules['settings.bkash_username'],
            'settings.bkash_password' => $this->rules['settings.bkash_password'],
        ], false, 'bkash');
    }

    public function saveOperations()
    {
        $this->saveSettings([
            'currency',
            'tax_rate',
        ], [
            'settings.currency' => $this->rules['settings.currency'],
            'settings.tax_rate' => $this->rules['settings.tax_rate'],
        ], false, 'operations');
    }

    public function saveSocial()
    {
        $this->saveSettings([
            'email',
            'phone',
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_linkedin',
        ], [
            'settings.email' => $this->rules['settings.email'],
            'settings.phone' => $this->rules['settings.phone'],
            'settings.social_facebook' => $this->rules['settings.social_facebook'],
            'settings.social_twitter' => $this->rules['settings.social_twitter'],
            'settings.social_instagram' => $this->rules['settings.social_instagram'],
            'settings.social_linkedin' => $this->rules['settings.social_linkedin'],
        ], false, 'social');
    }

    public function saveEmailSettings()
    {
        $this->saveSettings([
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
        ], [
            'settings.mail_host' => $this->rules['settings.mail_host'],
            'settings.mail_port' => $this->rules['settings.mail_port'],
            'settings.mail_username' => $this->rules['settings.mail_username'],
            'settings.mail_password' => $this->rules['settings.mail_password'],
            'settings.mail_encryption' => $this->rules['settings.mail_encryption'],
            'settings.mail_from_address' => $this->rules['settings.mail_from_address'],
            'settings.mail_from_name' => $this->rules['settings.mail_from_name'],
        ], false, 'email');
    }

    public function saveAdminNotifications()
    {
        $this->resetErrorBag();
        $this->telegramFetchMessage = '';
        if (! empty($this->settings['live_chat_enabled'])) {
            $botToken = trim((string) ($this->settings['admin_telegram_bot_token'] ?? ''));
            $chatId = trim((string) ($this->settings['admin_telegram_chat_id'] ?? ''));
            if ($botToken === '' || $chatId === '') {
                $this->addError('settings.live_chat_enabled', 'Set Telegram Bot Token and Chat ID before enabling Live Chat.');
                $this->savedSection = 'notifications';
                return;
            }

            $pusherId = trim((string) ($this->settings['pusher_app_id'] ?? ''));
            $pusherKey = trim((string) ($this->settings['pusher_app_key'] ?? ''));
            $pusherSecret = trim((string) ($this->settings['pusher_app_secret'] ?? ''));
            if ($pusherId === '' || $pusherKey === '' || $pusherSecret === '') {
                $this->addError('settings.pusher_app_key', 'Set Pusher credentials before enabling Live Chat.');
                $this->savedSection = 'notifications';
                return;
            }
        }

        $this->saveSettings([
            'admin_notify_email_enabled',
            'admin_notify_email_address',
            'admin_notify_telegram_enabled',
            'admin_telegram_bot_token',
            'admin_telegram_chat_id',
            'live_chat_enabled',
        ], [
            'settings.admin_notify_email_enabled' => $this->rules['settings.admin_notify_email_enabled'],
            'settings.admin_notify_email_address' => $this->rules['settings.admin_notify_email_address'],
            'settings.admin_notify_telegram_enabled' => $this->rules['settings.admin_notify_telegram_enabled'],
            'settings.admin_telegram_bot_token' => $this->rules['settings.admin_telegram_bot_token'],
            'settings.admin_telegram_chat_id' => $this->rules['settings.admin_telegram_chat_id'],
            'settings.live_chat_enabled' => $this->rules['settings.live_chat_enabled'],
        ], false, 'notifications');
    }

    public function saveRealtimeSettings()
    {
        $this->saveSettings([
            'pusher_app_id',
            'pusher_app_key',
            'pusher_app_secret',
            'pusher_app_cluster',
        ], [
            'settings.pusher_app_id' => $this->rules['settings.pusher_app_id'],
            'settings.pusher_app_key' => $this->rules['settings.pusher_app_key'],
            'settings.pusher_app_secret' => $this->rules['settings.pusher_app_secret'],
            'settings.pusher_app_cluster' => $this->rules['settings.pusher_app_cluster'],
        ], false, 'realtime');
    }

    public function fetchTelegramChatIds(): void
    {
        $this->telegramFetchMessage = '';
        $this->resetErrorBag('settings.admin_telegram_chat_id');
        $token = $this->settings['admin_telegram_bot_token'] ?? '';
        if (! $token) {
            $this->telegramFetchMessage = 'Enter a bot token first.';
            return;
        }

        if (! empty($this->settings['admin_telegram_webhook_set'])) {
            $this->telegramFetchMessage = 'Webhook is active. Clear webhook before fetching chat IDs.';
            return;
        }

        try {
            $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates", [
                'limit' => 50,
            ]);

            if (! $response->ok()) {
                $this->telegramFetchMessage = 'Unable to fetch updates. Check the token.';
                return;
            }

            $data = $response->json();
            $updates = $data['result'] ?? [];
            $seen = [];
            $options = [];

            foreach ($updates as $update) {
                $chat = $update['message']['chat'] ?? null;
                if (! $chat || ! isset($chat['id'])) {
                    continue;
                }

                $chatId = (string) $chat['id'];
                if (isset($seen[$chatId])) {
                    continue;
                }

                $label = $chat['title'] ?? $chat['username'] ?? $chat['first_name'] ?? 'Chat';
                $options[] = [
                    'id' => $chatId,
                    'label' => $label,
                ];
                $seen[$chatId] = true;
            }

            $this->telegramChatOptions = $options;
            $this->telegramFetchMessage = $options ? 'Select a Chat ID below.' : 'No chats found. Send a message to the bot or in the group, then try again.';
        } catch (\Throwable $e) {
            $this->telegramFetchMessage = 'Error fetching chats. Try again.';
        }
    }

    public function setTelegramWebhook(): void
    {
        $this->telegramWebhookMessage = '';
        $this->telegramWebhookSet = false;
        $token = $this->settings['admin_telegram_bot_token'] ?? '';
        if (! $token) {
            $this->telegramWebhookMessage = 'Enter a bot token first.';
            return;
        }

        $appUrl = rtrim(config('app.url'), '/');
        if (! str_starts_with($appUrl, 'https://')) {
            $this->telegramWebhookMessage = 'APP_URL must be HTTPS and publicly accessible.';
            return;
        }

        try {
            $secret = config('services.telegram.webhook_secret');
            $payload = ['url' => $appUrl.'/telegram/webhook'];
            if ($secret) {
                $payload['secret_token'] = $secret;
            }
            $response = Http::asForm()->post("https://api.telegram.org/bot{$token}/setWebhook", $payload);
            if (! $response->ok()) {
                $this->telegramWebhookMessage = 'Failed to set webhook. Check token and URL.';
                return;
            }
            $data = $response->json();
            $this->telegramWebhookSet = ! empty($data['ok']);
            $this->telegramWebhookMessage = $this->telegramWebhookSet ? 'Webhook set successfully.' : 'Failed to set webhook.';
            if ($this->telegramWebhookSet) {
                Setting::updateOrCreate(['key' => 'admin_telegram_webhook_set'], ['value' => '1']);
                $this->settings['admin_telegram_webhook_set'] = true;
            }
        } catch (\Throwable $e) {
            $this->telegramWebhookMessage = 'Error setting webhook. Try again.';
        }
    }

    public function clearTelegramWebhook(): void
    {
        $this->telegramWebhookMessage = '';
        $this->telegramWebhookSet = false;
        $token = $this->settings['admin_telegram_bot_token'] ?? '';
        if (! $token) {
            $this->telegramWebhookMessage = 'Enter a bot token first.';
            return;
        }

        try {
            $response = Http::asForm()->post("https://api.telegram.org/bot{$token}/deleteWebhook");
            if (! $response->ok()) {
                $this->telegramWebhookMessage = 'Failed to clear webhook.';
                return;
            }
            Setting::updateOrCreate(['key' => 'admin_telegram_webhook_set'], ['value' => '0']);
            $this->settings['admin_telegram_webhook_set'] = false;
            $this->telegramWebhookMessage = 'Webhook cleared. You can fetch chat IDs now.';
        } catch (\Throwable $e) {
            $this->telegramWebhookMessage = 'Error clearing webhook. Try again.';
        }
    }

    public function sendQueueTest(): void
    {
        $this->queueTestMessage = '';
        TestQueueJob::dispatch();
        $this->queueTestMessage = 'Test job queued. Wait 1–2 minutes and refresh to see updated status.';
    }

    public function saveAll()
    {
        $this->saveSettings(array_keys($this->settings), $this->rules, true, 'all');
    }

    public function addOfflinePaymentMethod(): void
    {
        $this->offlinePaymentMethods[] = [
            'name' => '',
            'instructions' => '',
            'active' => true,
        ];
    }

    public function removeOfflinePaymentMethod(int $index): void
    {
        unset($this->offlinePaymentMethods[$index]);
        $this->offlinePaymentMethods = array_values($this->offlinePaymentMethods);
    }

    public function saveOfflinePaymentMethods(): void
    {
        $clean = [];
        foreach ($this->offlinePaymentMethods as $method) {
            $name = trim((string) ($method['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $clean[] = [
                'name' => $name,
                'instructions' => trim((string) ($method['instructions'] ?? '')),
                'active' => ! empty($method['active']),
            ];
        }

        Setting::updateOrCreate(
            ['key' => 'offline_payment_methods'],
            ['value' => json_encode($clean)]
        );

        $this->offlinePaymentMethods = $clean;
        $this->savedSection = 'offline_payments';
        session()->flash('message', 'Offline payment methods updated.');
    }

    protected function saveSettings(array $keys, array $rules, bool $checkAuth = false, string $sectionKey = ''): void
    {
        $this->validate($rules);

        if ($checkAuth) {
            if (
                ! $this->settings['customer_auth_email_otp_enabled']
                && ! $this->settings['customer_auth_email_password_enabled']
                && ! $this->settings['customer_auth_guest_checkout_enabled']
            ) {
                $this->addError('settings.customer_auth_email_password_enabled', 'Enable at least one customer auth/checkout method.');
                return;
            }
        }

        if (in_array('shop_logo', $keys, true) && $this->logo) {
            if ($oldLogo = Setting::where('key', 'shop_logo')->value('value')) {
                Storage::disk('public')->delete($oldLogo);
            }
            $this->settings['shop_logo'] = $this->logo->store('logos', 'public');
            $this->logo = null;
        }

        // Country list is now sourced from the countries table only.
        Setting::where('key', 'supported_countries')->delete();

        foreach ($keys as $key) {
            $value = $this->settings[$key] ?? null;

            if ($key === 'currency') {
                Currency::query()->update(['is_default' => false]);
                Currency::where('code', $value)->update(['is_default' => true]);
            }

            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->savedSection = $sectionKey;
        session()->flash('message', 'Shop settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
