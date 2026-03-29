<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class InstallController extends Controller
{
    protected array $countryOptions = [
        'BD' => 'Bangladesh',
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'CA' => 'Canada',
        'MY' => 'Malaysia',
        'SG' => 'Singapore',
        'IN' => 'India',
        'AE' => 'United Arab Emirates',
        'SA' => 'Saudi Arabia',
        'AU' => 'Australia',
        'NZ' => 'New Zealand',
        'DE' => 'Germany',
        'FR' => 'France',
        'NL' => 'Netherlands',
        'JP' => 'Japan',
        'KR' => 'South Korea',
        'CN' => 'China',
        'TH' => 'Thailand',
        'PH' => 'Philippines',
        'ID' => 'Indonesia',
        'VN' => 'Vietnam',
        'PK' => 'Pakistan',
        'LK' => 'Sri Lanka',
        'NP' => 'Nepal',
    ];

    protected function currencyPresets(): array
    {
        return [
            'BDT' => ['name' => 'Bangladeshi Taka', 'symbol' => '৳', 'exchange_rate' => 1.0000],
            'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 120.0000],
            'EUR' => ['name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 130.0000],
            'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 150.0000],
            'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$', 'exchange_rate' => 88.0000],
            'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate' => 78.0000],
            'MYR' => ['name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'exchange_rate' => 27.0000],
            'SGD' => ['name' => 'Singapore Dollar', 'symbol' => 'S$', 'exchange_rate' => 90.0000],
            'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 1.4000],
            'AED' => ['name' => 'UAE Dirham', 'symbol' => 'د.إ', 'exchange_rate' => 32.0000],
            'SAR' => ['name' => 'Saudi Riyal', 'symbol' => '﷼', 'exchange_rate' => 32.0000],
            'PKR' => ['name' => 'Pakistani Rupee', 'symbol' => '₨', 'exchange_rate' => 0.4300],
            'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥', 'exchange_rate' => 17.0000],
            'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate' => 0.8500],
        ];
    }

    public function index()
    {
        return redirect()->route('install.requirements');
    }

    public function requirements()
    {
        if ($this->installed()) {
            return redirect()->route('store.index');
        }

        $checks = [
            ['label' => 'PHP 8.2+', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>=')],
            ['label' => 'PDO', 'ok' => extension_loaded('pdo')],
            ['label' => 'OpenSSL', 'ok' => extension_loaded('openssl')],
            ['label' => 'mbstring', 'ok' => extension_loaded('mbstring')],
            ['label' => 'Tokenizer', 'ok' => extension_loaded('tokenizer')],
            ['label' => 'XML', 'ok' => extension_loaded('xml')],
            ['label' => 'cURL', 'ok' => extension_loaded('curl')],
            ['label' => 'Fileinfo', 'ok' => extension_loaded('fileinfo')],
            ['label' => 'JSON', 'ok' => extension_loaded('json')],
            ['label' => 'Zip', 'ok' => extension_loaded('zip')],
            ['label' => 'Storage writable', 'ok' => is_writable(storage_path())],
            ['label' => 'Cache writable', 'ok' => is_writable(base_path('bootstrap/cache'))],
        ];

        return view('install.requirements', compact('checks'));
    }

    public function storeRequirements()
    {
        return redirect()->route('install.database');
    }

    public function database()
    {
        if ($this->installed()) {
            return redirect()->route('store.index');
        }

        return view('install.database', [
            'database' => session('installer.database', [
                'host' => config('database.connections.mysql.host', '127.0.0.1'),
                'port' => config('database.connections.mysql.port', '3306'),
                'database' => config('database.connections.mysql.database', ''),
                'username' => config('database.connections.mysql.username', ''),
                'password' => '',
                'prefix' => config('database.connections.mysql.prefix', ''),
            ]),
        ]);
    }

    public function storeDatabase(Request $request)
    {
        if ($this->installed()) {
            return redirect()->route('store.index');
        }

        $data = $request->validate([
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'numeric', 'min:1', 'max:65535'],
            'database' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'prefix' => ['nullable', 'string', 'max:20'],
        ]);

        $this->applyDatabaseConfig($data);

        try {
            DB::connection('mysql')->getPdo();
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['database' => 'Database connection failed: '.$e->getMessage()]);
        }

        session(['installer.database' => $data]);

        return redirect()->route('install.settings');
    }

    public function settings()
    {
        if ($this->installed()) {
            return redirect()->route('store.index');
        }

        if (! session()->has('installer.database')) {
            return redirect()->route('install.database');
        }

        $settings = session('installer.settings', $this->defaultSettings());
        $settings['app_url'] = $settings['app_url'] ?: $this->guessAppUrl(request());

        return view('install.settings', [
            'settings' => $settings,
            'countryOptions' => $this->countryOptions,
            'currencyPresets' => $this->currencyPresets(),
        ]);
    }

    public function storeSettings(Request $request)
    {
        if ($this->installed()) {
            return redirect()->route('store.index');
        }

        $currencyCodes = array_keys($this->currencyPresets());

        $data = $request->validate([
            'app_url' => ['nullable', 'url', 'max:255'],
            'shop_name' => ['nullable', 'string', 'max:255'],
            'slogan' => ['nullable', 'string', 'max:255'],
            'branding_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'store_default_locale' => ['required', 'in:en,bn'],
            'store_language_en_enabled' => ['sometimes', 'boolean'],
            'store_language_bn_enabled' => ['sometimes', 'boolean'],
            'currency' => ['required', 'string', 'in:'.implode(',', $currencyCodes)],
            'cod_enabled' => ['sometimes', 'boolean'],
            'home_hero_title' => ['nullable', 'string', 'max:255'],
            'home_hero_subtitle' => ['nullable', 'string', 'max:500'],
            'home_shop_by_category_title' => ['nullable', 'string', 'max:255'],
            'home_featured_products_title' => ['nullable', 'string', 'max:255'],
            'home_new_arrivals_title' => ['nullable', 'string', 'max:255'],
            'home_newsletter_title' => ['nullable', 'string', 'max:255'],
            'home_newsletter_subtitle' => ['nullable', 'string', 'max:500'],
            'footer_about_title' => ['nullable', 'string', 'max:255'],
            'footer_about_description' => ['nullable', 'string', 'max:500'],
            'footer_support_hours_1' => ['nullable', 'string', 'max:255'],
            'footer_support_hours_2' => ['nullable', 'string', 'max:255'],
            'custom_currencies' => ['nullable', 'array'],
            'custom_currencies.*.code' => ['nullable', 'string', 'size:3', 'alpha'],
            'custom_currencies.*.name' => ['nullable', 'string', 'max:120'],
            'custom_currencies.*.symbol' => ['nullable', 'string', 'max:10'],
            'custom_currencies.*.exchange_rate' => ['nullable', 'numeric', 'min:0.0001'],
            'custom_currencies.*.active' => ['sometimes', 'boolean'],
            'stripe_api_key' => ['nullable', 'string', 'max:255'],
            'stripe_label' => ['nullable', 'string', 'max:255'],
            'paypal_api_key' => ['nullable', 'string', 'max:255'],
            'paypal_label' => ['nullable', 'string', 'max:255'],
            'sslcommerz_store_id' => ['nullable', 'string', 'max:255'],
            'sslcommerz_api_key' => ['nullable', 'string', 'max:255'],
            'sslcommerz_label' => ['nullable', 'string', 'max:255'],
            'sslcommerz_sandbox' => ['sometimes', 'boolean'],
            'bkash_base_url' => ['nullable', 'string', 'max:255'],
            'bkash_app_key' => ['nullable', 'string', 'max:255'],
            'bkash_app_secret' => ['nullable', 'string', 'max:255'],
            'bkash_username' => ['nullable', 'string', 'max:255'],
            'bkash_password' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'numeric', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,none'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'country_codes' => ['nullable', 'array'],
            'country_codes.*' => ['string', 'size:2'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('branding', 'public');
        } else {
            $data['logo'] = session('installer.settings.logo');
        }

        $data['store_language_en_enabled'] = (bool) ($data['store_language_en_enabled'] ?? true);
        $data['store_language_bn_enabled'] = (bool) ($data['store_language_bn_enabled'] ?? true);
        $data['cod_enabled'] = (bool) ($data['cod_enabled'] ?? true);
        $data['sslcommerz_sandbox'] = (bool) ($data['sslcommerz_sandbox'] ?? false);
        $data['country_codes'] = array_values(array_intersect(array_keys($this->countryOptions), $data['country_codes'] ?? []));
        $data['currency'] = strtoupper((string) ($data['currency'] ?? 'BDT'));
        $data['custom_currencies'] = collect($data['custom_currencies'] ?? [])
            ->map(function (array $currency): ?array {
                $code = strtoupper(trim((string) ($currency['code'] ?? '')));
                $name = trim((string) ($currency['name'] ?? ''));
                $symbol = trim((string) ($currency['symbol'] ?? ''));
                $exchangeRate = (float) ($currency['exchange_rate'] ?? 0);

                if ($code === '' || $name === '' || $symbol === '' || $exchangeRate <= 0) {
                    return null;
                }

                return [
                    'code' => $code,
                    'name' => $name,
                    'symbol' => $symbol,
                    'exchange_rate' => $exchangeRate,
                    'active' => (bool) ($currency['active'] ?? true),
                ];
            })
            ->filter()
            ->values()
            ->all();

        if (! $data['country_codes']) {
            $data['country_codes'] = ['BD'];
        }

        session(['installer.settings' => $data]);

        return redirect()->route('install.finalize');
    }

    public function finalize()
    {
        if ($this->installed()) {
            return redirect()->route('store.index');
        }

        if (! session()->has('installer.database')) {
            return redirect()->route('install.database');
        }

        $database = session('installer.database');
        $settings = session('installer.settings', $this->defaultSettings());

        try {
            $this->applyDatabaseConfig($database);
        $this->writeEnvFile($database, $settings);

            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            if (empty(config('app.key'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }
            if (! is_link(public_path('storage')) && ! file_exists(public_path('storage'))) {
                Artisan::call('storage:link');
            }
        } catch (Throwable $e) {
            return redirect()->route('install.database')
                ->withInput()
                ->withErrors(['database' => 'Installation could not complete: '.$e->getMessage()]);
        }

        $this->persistSettings($settings);
        $this->persistCurrencies($settings['currency'] ?? 'BDT', $settings['custom_currencies'] ?? []);
        $this->syncCountries($settings['country_codes'] ?? ['BD']);
        $this->createLockFile($settings['app_url'] ?? config('app.url'));

        session()->forget(['installer.database', 'installer.settings']);
        session()->flash('installer.completed', true);

        return redirect()->route('install.complete');
    }

    public function complete()
    {
        if (! $this->installed()) {
            return redirect()->route('install.requirements');
        }

        if (! session()->has('installer.completed')) {
            return redirect()->route('store.index');
        }

        return view('install.complete', [
            'storeName' => Setting::query()->where('key', 'shop_name')->value('value') ?: config('app.name', 'ShopHub'),
        ]);
    }

    protected function persistSettings(array $settings): void
    {
        $rows = [
            'shop_name' => $settings['shop_name'] ?? '',
            'site_title' => $settings['slogan'] ?? '',
            'store_default_locale' => $settings['store_default_locale'] ?? 'en',
            'store_language_en_enabled' => ($settings['store_language_en_enabled'] ?? true) ? '1' : '0',
            'store_language_bn_enabled' => ($settings['store_language_bn_enabled'] ?? true) ? '1' : '0',
            'branding_color' => $settings['branding_color'] ?? '#111111',
            'secondary_color' => $settings['secondary_color'] ?? '#6B7280',
            'accent_color' => $settings['accent_color'] ?? '#F59E0B',
            'currency' => $settings['currency'] ?? 'BDT',
            'cod_enabled' => ($settings['cod_enabled'] ?? true) ? '1' : '0',
            'home_hero_title' => $settings['home_hero_title'] ?? '',
            'home_hero_subtitle' => $settings['home_hero_subtitle'] ?? '',
            'home_shop_by_category_title' => $settings['home_shop_by_category_title'] ?? '',
            'home_featured_products_title' => $settings['home_featured_products_title'] ?? '',
            'home_new_arrivals_title' => $settings['home_new_arrivals_title'] ?? '',
            'home_newsletter_title' => $settings['home_newsletter_title'] ?? '',
            'home_newsletter_subtitle' => $settings['home_newsletter_subtitle'] ?? '',
            'footer_about_title' => $settings['footer_about_title'] ?? '',
            'footer_about_description' => $settings['footer_about_description'] ?? '',
            'footer_support_hours_1' => $settings['footer_support_hours_1'] ?? '',
            'footer_support_hours_2' => $settings['footer_support_hours_2'] ?? '',
            'footer_link_1_label' => 'About Us',
            'footer_link_1_url' => '/about',
            'footer_link_2_label' => 'Contact',
            'footer_link_2_url' => '/contact',
            'footer_link_3_label' => 'FAQ',
            'footer_link_3_url' => '/faq',
            'footer_link_4_label' => 'Shipping Info',
            'footer_link_4_url' => '/shipping',
            'footer_policy_1_label' => 'Privacy Policy',
            'footer_policy_1_url' => '/privacy-policy',
            'footer_policy_2_label' => 'Terms of Service',
            'footer_policy_2_url' => '/terms',
            'footer_policy_3_label' => 'Cookie Policy',
            'footer_policy_3_url' => '/cookie-policy',
            'footer_policy_4_label' => 'Refund Policy',
            'footer_policy_4_url' => '/refund-policy',
            'stripe_api_key' => $settings['stripe_api_key'] ?? '',
            'stripe_label' => $settings['stripe_label'] ?? '',
            'paypal_api_key' => $settings['paypal_api_key'] ?? '',
            'paypal_label' => $settings['paypal_label'] ?? '',
            'sslcommerz_store_id' => $settings['sslcommerz_store_id'] ?? '',
            'sslcommerz_api_key' => $settings['sslcommerz_api_key'] ?? '',
            'sslcommerz_label' => $settings['sslcommerz_label'] ?? '',
            'sslcommerz_sandbox' => ($settings['sslcommerz_sandbox'] ?? false) ? '1' : '0',
            'bkash_base_url' => $settings['bkash_base_url'] ?? '',
            'bkash_app_key' => $settings['bkash_app_key'] ?? '',
            'bkash_app_secret' => $settings['bkash_app_secret'] ?? '',
            'bkash_username' => $settings['bkash_username'] ?? '',
            'bkash_password' => $settings['bkash_password'] ?? '',
            'email' => $settings['email'] ?? '',
            'phone' => $settings['phone'] ?? '',
            'mail_host' => $settings['mail_host'] ?? '',
            'mail_port' => $settings['mail_port'] ?? '',
            'mail_username' => $settings['mail_username'] ?? '',
            'mail_password' => $settings['mail_password'] ?? '',
            'mail_encryption' => $settings['mail_encryption'] ?? 'tls',
            'mail_from_address' => $settings['mail_from_address'] ?? '',
            'mail_from_name' => $settings['mail_from_name'] ?? '',
            'shop_logo' => $settings['logo'] ?? '',
            'footer_support_email' => $settings['email'] ?? '',
            'footer_support_phone' => $settings['phone'] ?? '',
            'admin_notify_email_enabled' => '0',
            'admin_notify_email_address' => '',
            'admin_notify_telegram_enabled' => '0',
            'admin_telegram_bot_token' => '',
            'admin_telegram_chat_id' => '',
            'live_chat_enabled' => '0',
            'admin_telegram_webhook_set' => '0',
            'customer_auth_email_otp_enabled' => '0',
            'customer_auth_email_password_enabled' => '1',
            'customer_auth_guest_checkout_enabled' => '0',
        ];

        foreach ($rows as $key => $value) {
            Setting::query()->updateOrInsert(['key' => $key], ['value' => (string) $value]);
        }
    }

    protected function persistCurrencies(string $defaultCode, array $customCurrencies): void
    {
        $defaultCode = strtoupper(trim($defaultCode)) ?: 'BDT';
        $rows = $this->currencyPresets();

        foreach ($customCurrencies as $currency) {
            $code = strtoupper(trim((string) ($currency['code'] ?? '')));
            $name = trim((string) ($currency['name'] ?? ''));
            $symbol = trim((string) ($currency['symbol'] ?? ''));
            $exchangeRate = (float) ($currency['exchange_rate'] ?? 0);

            if ($code === '' || $name === '' || $symbol === '' || $exchangeRate <= 0) {
                continue;
            }

            $rows[$code] = [
                'name' => $name,
                'symbol' => $symbol,
                'exchange_rate' => $exchangeRate,
            ];
        }

        Currency::query()->update(['is_default' => false]);

        foreach ($rows as $code => $currency) {
            Currency::query()->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $currency['name'],
                    'symbol' => $currency['symbol'],
                    'exchange_rate' => $currency['exchange_rate'],
                    'active' => true,
                    'is_default' => $code === $defaultCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        if (! Currency::query()->where('code', $defaultCode)->exists() && isset($rows[$defaultCode])) {
            Currency::query()->updateOrInsert(
                ['code' => $defaultCode],
                [
                    'name' => $rows[$defaultCode]['name'],
                    'symbol' => $rows[$defaultCode]['symbol'],
                    'exchange_rate' => $rows[$defaultCode]['exchange_rate'],
                    'active' => true,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    protected function syncCountries(array $codes): void
    {
        $codes = array_values(array_unique(array_map('strtoupper', $codes)));

        if (! in_array('BD', $codes, true)) {
            array_unshift($codes, 'BD');
        }

        Country::query()->update(['active' => false]);
        Country::query()->whereIn('code', $codes)->update(['active' => true]);
    }

    protected function createLockFile(?string $appUrl = null): void
    {
        Storage::disk('local')->put('installed.lock', json_encode([
            'installed_at' => now()->toDateTimeString(),
            'app_url' => $appUrl ?: config('app.url'),
        ], JSON_PRETTY_PRINT));
    }

    protected function installed(): bool
    {
        return Storage::disk('local')->exists('installed.lock');
    }

    protected function applyDatabaseConfig(array $database): void
    {
        config([
            'database.connections.mysql.host' => $database['host'],
            'database.connections.mysql.port' => $database['port'],
            'database.connections.mysql.database' => $database['database'],
            'database.connections.mysql.username' => $database['username'],
            'database.connections.mysql.password' => $database['password'] ?? '',
            'database.connections.mysql.prefix' => $database['prefix'] ?? '',
        ]);

        DB::purge('mysql');
    }

    protected function writeEnvFile(array $database, array $settings): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath) && file_exists(base_path('.env.example'))) {
            copy(base_path('.env.example'), $envPath);
        }

        if (! file_exists($envPath)) {
            return;
        }

        $replacements = [
            'APP_NAME' => $settings['shop_name'] ?: config('app.name', 'ShopHub'),
            'APP_URL' => $settings['app_url'] ?? config('app.url'),
            'DB_HOST' => $database['host'],
            'DB_PORT' => $database['port'],
            'DB_DATABASE' => $database['database'],
            'DB_USERNAME' => $database['username'],
            'DB_PASSWORD' => $database['password'] ?? '',
        ];

        $env = file_get_contents($envPath);

        foreach ($replacements as $key => $value) {
            $quoted = str_contains((string) $value, ' ') || str_contains((string) $value, '#')
                ? '"'.str_replace('"', '\"', (string) $value).'"'
                : (string) $value;

            if (preg_match("/^{$key}=.*$/m", $env)) {
                $env = preg_replace("/^{$key}=.*$/m", "{$key}={$quoted}", $env);
            } else {
                $env .= PHP_EOL."{$key}={$quoted}";
            }
        }

        file_put_contents($envPath, $env);
    }

    protected function defaultSettings(): array
    {
        return [
            'app_url' => $this->guessAppUrl(request()),
            'shop_name' => '',
            'slogan' => '',
            'branding_color' => '#111111',
            'secondary_color' => '#6B7280',
            'accent_color' => '#F59E0B',
            'store_default_locale' => 'en',
            'store_language_en_enabled' => true,
            'store_language_bn_enabled' => true,
            'currency' => 'BDT',
            'cod_enabled' => true,
            'home_hero_title' => 'Find what fits your life',
            'home_hero_subtitle' => 'Curated products, fast delivery, and a storefront built for easy browsing.',
            'home_shop_by_category_title' => 'Shop by Category',
            'home_featured_products_title' => 'Featured Products',
            'home_new_arrivals_title' => 'New Arrivals',
            'home_newsletter_title' => 'Stay Updated',
            'home_newsletter_subtitle' => 'Subscribe for new arrivals, exclusive offers, and restock alerts.',
            'footer_about_title' => 'ShopHub',
            'footer_about_description' => 'Your trusted marketplace for clothing, health products, and unique handmade items.',
            'footer_support_hours_1' => 'Mon-Fri: 9AM-6PM',
            'footer_support_hours_2' => 'Sat-Sun: 10AM-4PM',
            'custom_currencies' => [],
            'footer_link_1_label' => 'About Us',
            'footer_link_1_url' => '/about',
            'footer_link_2_label' => 'Contact',
            'footer_link_2_url' => '/contact',
            'footer_link_3_label' => 'FAQ',
            'footer_link_3_url' => '/faq',
            'footer_link_4_label' => 'Shipping Info',
            'footer_link_4_url' => '/shipping',
            'footer_policy_1_label' => 'Privacy Policy',
            'footer_policy_1_url' => '/privacy-policy',
            'footer_policy_2_label' => 'Terms of Service',
            'footer_policy_2_url' => '/terms',
            'footer_policy_3_label' => 'Cookie Policy',
            'footer_policy_3_url' => '/cookie-policy',
            'footer_policy_4_label' => 'Refund Policy',
            'footer_policy_4_url' => '/refund-policy',
            'stripe_api_key' => '',
            'stripe_label' => '',
            'paypal_api_key' => '',
            'paypal_label' => '',
            'sslcommerz_store_id' => '',
            'sslcommerz_api_key' => '',
            'sslcommerz_label' => '',
            'sslcommerz_sandbox' => false,
            'bkash_base_url' => '',
            'bkash_app_key' => '',
            'bkash_app_secret' => '',
            'bkash_username' => '',
            'bkash_password' => '',
            'email' => '',
            'phone' => '',
            'mail_host' => '',
            'mail_port' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => '',
            'mail_from_name' => '',
            'logo' => '',
            'country_codes' => ['BD'],
        ];
    }

    protected function guessAppUrl(Request $request): string
    {
        $scheme = $request->getScheme();
        $host = $request->getHost();
        $port = $request->getPort();

        $url = "{$scheme}://{$host}";

        if ($port && ! in_array($port, [80, 443], true)) {
            $url .= ":{$port}";
        }

        return $url;
    }
}
