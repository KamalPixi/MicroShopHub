<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;
use App\Models\Currency;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Settings extends Component
{
    use WithFileUploads;

    public $settings = [
        // General
        'shop_logo' => '',
        'branding_color' => '#000000',
        'secondary_color' => '#6B7280',
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
        
        // Cash on Delivery
        'cod_label' => 'Cash on Delivery',
        'cod_enabled' => false,
        
        // Operations
        'currency' => 'BDT',
        'tax_rate' => 0,
        'email' => '',
        'phone' => '',
    ];

    public $logo;

    protected $rules = [
        'logo' => 'nullable|image|max:2048',
        'settings.branding_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        'settings.secondary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
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
        
        'settings.currency' => 'nullable|string|in:USD,BDT,EUR,GBP',
        'settings.tax_rate' => 'nullable|numeric|min:0|max:100',
        'settings.email' => 'nullable|numeric|min:0|max:100',
        'settings.phone' => 'nullable|numeric|min:0|max:100',
    ];

    public function mount()
    {
        $existingSettings = Setting::all()->pluck('value', 'key')->toArray();
        
        foreach ($this->settings as $key => $value) {
            $this->settings[$key] = $existingSettings[$key] ?? $value;
        }

        // Force Boolean for Checkboxes
        $this->settings['sslcommerz_sandbox'] = filter_var($this->settings['sslcommerz_sandbox'], FILTER_VALIDATE_BOOLEAN);
        $this->settings['cod_enabled'] = filter_var($this->settings['cod_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN); // [NEW]
    }

    public function save()
    {
        $this->validate();

        if ($this->logo) {
            if ($oldLogo = Setting::where('key', 'shop_logo')->value('value')) {
                Storage::disk('public')->delete($oldLogo);
            }
            $this->settings['shop_logo'] = $this->logo->store('logos', 'public');
        }

        foreach ($this->settings as $key => $value) {

            //Sync Currency Table when 'currency' setting changes
            if ($key === 'currency') {
                Currency::query()->update(['is_default' => false]);
                // Set selected to true
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

        $this->logo = null;
        session()->flash('message', 'Shop settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
