<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;
use App\Models\Currency;
use App\Models\Country;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

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

    protected $rules = [
        'logo' => 'nullable|image|max:2048',
        'settings.branding_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        'settings.secondary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        'settings.accent_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        'settings.shop_name' => 'nullable|string|max:255',
        'settings.site_title' => 'nullable|string|max:255',
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

        // Force Boolean for Checkboxes
        $this->settings['sslcommerz_sandbox'] = filter_var($this->settings['sslcommerz_sandbox'], FILTER_VALIDATE_BOOLEAN);
        $this->settings['cod_enabled'] = filter_var($this->settings['cod_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN); // [NEW]
        $this->settings['customer_auth_email_otp_enabled'] = filter_var($this->settings['customer_auth_email_otp_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->settings['customer_auth_email_password_enabled'] = filter_var($this->settings['customer_auth_email_password_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->settings['customer_auth_guest_checkout_enabled'] = filter_var($this->settings['customer_auth_guest_checkout_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

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
        $this->saveSettings([
            'shop_name',
            'site_title',
            'branding_color',
            'secondary_color',
            'accent_color',
            'shop_logo',
        ], [
            'logo' => $this->rules['logo'],
            'settings.shop_name' => $this->rules['settings.shop_name'],
            'settings.site_title' => $this->rules['settings.site_title'],
            'settings.branding_color' => $this->rules['settings.branding_color'],
            'settings.secondary_color' => $this->rules['settings.secondary_color'],
            'settings.accent_color' => $this->rules['settings.accent_color'],
        ]);
    }

    public function saveSeo()
    {
        $this->saveSettings([
            'meta_description',
            'meta_keywords',
        ], [
            'settings.meta_description' => $this->rules['settings.meta_description'],
            'settings.meta_keywords' => $this->rules['settings.meta_keywords'],
        ]);
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
        ], true);
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
        ]);
    }

    public function saveCodGateway()
    {
        $this->saveSettings([
            'cod_label',
            'cod_enabled',
        ], [
            'settings.cod_label' => $this->rules['settings.cod_label'],
            'settings.cod_enabled' => $this->rules['settings.cod_enabled'],
        ]);
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
        ]);
    }

    public function saveStripeGateway()
    {
        $this->saveSettings([
            'stripe_api_key',
            'stripe_label',
        ], [
            'settings.stripe_api_key' => $this->rules['settings.stripe_api_key'],
            'settings.stripe_label' => $this->rules['settings.stripe_label'],
        ]);
    }

    public function savePaypalGateway()
    {
        $this->saveSettings([
            'paypal_api_key',
            'paypal_label',
        ], [
            'settings.paypal_api_key' => $this->rules['settings.paypal_api_key'],
            'settings.paypal_label' => $this->rules['settings.paypal_label'],
        ]);
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
        ]);
    }

    public function saveOperations()
    {
        $this->saveSettings([
            'currency',
            'tax_rate',
        ], [
            'settings.currency' => $this->rules['settings.currency'],
            'settings.tax_rate' => $this->rules['settings.tax_rate'],
        ]);
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
        ]);
    }

    public function saveAll()
    {
        $this->saveSettings(array_keys($this->settings), $this->rules, true);
    }

    protected function saveSettings(array $keys, array $rules, bool $checkAuth = false): void
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

        session()->flash('message', 'Shop settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
