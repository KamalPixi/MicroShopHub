<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Settings extends Component
{
    use WithFileUploads;

    public $settings = [
        'shop_logo' => '',
        'branding_color' => '#000000', // Primary Color
        'secondary_color' => '#6B7280', // New Secondary Color (Default Gray)
        'shop_name' => '',
        'site_title' => '',
        'meta_description' => '',
        'meta_keywords' => '',
        'social_facebook' => '',
        'social_twitter' => '',
        'social_instagram' => '',
        'social_linkedin' => '',
        'stripe_api_key' => '',
        'paypal_api_key' => '',
        'sslcommerz_store_id' => '',
        'sslcommerz_api_key' => '',
        'currency' => 'BDT',
        'tax_rate' => 0,
        'email' => '',
        'phone' => '',
    ];

    public $logo; // Temporary property for file upload

    protected $rules = [
        'logo' => 'nullable|image|max:2048', // Max 2MB
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
        'settings.stripe_api_key' => 'nullable|string|max:255',
        'settings.paypal_api_key' => 'nullable|string|max:255',
        'settings.sslcommerz_store_id' => 'nullable|string|max:255',
        'settings.sslcommerz_api_key' => 'nullable|string|max:255',
        'settings.currency' => 'nullable|string|in:USD,BDT,EUR,GBP',
        'settings.tax_rate' => 'nullable|numeric|min:0|max:100',
        'settings.email' => 'nullable|numeric|min:0|max:100',
        'settings.phone' => 'nullable|numeric|min:0|max:100',
    ];

    public function mount()
    {
        // Load existing settings from the database
        $existingSettings = Setting::all()->pluck('value', 'key')->toArray();
        foreach ($this->settings as $key => $value) {
            $this->settings[$key] = $existingSettings[$key] ?? $value;
        }
    }

    public function save()
    {
        $this->validate();

        // Handle logo upload
        if ($this->logo) {
            // Delete old logo if it exists
            if ($oldLogo = Setting::where('key', 'shop_logo')->value('value')) {
                Storage::disk('public')->delete($oldLogo);
            }
            // Store new logo
            $this->settings['shop_logo'] = $this->logo->store('logos', 'public');
        }

        // Save all settings
        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Reset logo upload field
        $this->logo = null;

        session()->flash('message', 'Shop settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
