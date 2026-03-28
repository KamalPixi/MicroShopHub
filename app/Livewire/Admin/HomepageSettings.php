<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class HomepageSettings extends Component
{
    public array $settings = [];

    public array $defaults = [
        'home_hero_enabled' => true,
        'home_hero_title' => 'Find what fits your life',
        'home_hero_subtitle' => 'Curated products, fast delivery, and a storefront built for easy browsing.',
        'home_hero_cta_label' => 'Shop Now',
        'home_hero_cta_url' => '/search',
        'home_shop_by_category_enabled' => true,
        'home_shop_by_category_title' => 'Shop by Category',
        'home_featured_products_enabled' => true,
        'home_featured_products_title' => 'Featured Products',
        'home_new_arrivals_enabled' => true,
        'home_new_arrivals_title' => 'New Arrivals',
        'home_newsletter_enabled' => true,
        'home_newsletter_title' => 'Stay Updated',
        'home_newsletter_subtitle' => 'Subscribe for new arrivals, exclusive offers, and restock alerts.',
    ];

    protected array $rules = [
        'settings.home_hero_enabled' => 'boolean',
        'settings.home_hero_title' => 'nullable|string|max:255',
        'settings.home_hero_subtitle' => 'nullable|string|max:500',
        'settings.home_hero_cta_label' => 'nullable|string|max:100',
        'settings.home_hero_cta_url' => 'nullable|string|max:255',
        'settings.home_shop_by_category_enabled' => 'boolean',
        'settings.home_shop_by_category_title' => 'nullable|string|max:255',
        'settings.home_featured_products_enabled' => 'boolean',
        'settings.home_featured_products_title' => 'nullable|string|max:255',
        'settings.home_new_arrivals_enabled' => 'boolean',
        'settings.home_new_arrivals_title' => 'nullable|string|max:255',
        'settings.home_newsletter_enabled' => 'boolean',
        'settings.home_newsletter_title' => 'nullable|string|max:255',
        'settings.home_newsletter_subtitle' => 'nullable|string|max:500',
    ];

    public function mount(): void
    {
        $stored = Setting::whereIn('key', array_keys($this->defaults))
            ->pluck('value', 'key')
            ->toArray();

        foreach ($this->defaults as $key => $value) {
            $current = $stored[$key] ?? $value;
            if (str_ends_with($key, '_enabled')) {
                $this->settings[$key] = filter_var($current, FILTER_VALIDATE_BOOLEAN);
            } else {
                $this->settings[$key] = $current;
            }
        }
    }

    public function save(): void
    {
        $this->validate();

        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : ($value ?? '')]
            );
        }

        session()->flash('message', 'Homepage settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.homepage-settings');
    }
}
