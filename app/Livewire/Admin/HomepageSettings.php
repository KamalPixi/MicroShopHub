<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Component;

class HomepageSettings extends Component
{
    use WithFileUploads;

    public array $settings = [];
    public array $bannerSlides = [];

    public array $defaults = [
        'home_hero_enabled' => true,
        'home_banner_type' => 'split',
        'home_hero_title' => 'Find what fits your life',
        'home_hero_subtitle' => 'Curated products, fast delivery, and a storefront built for easy browsing.',
        'home_hero_cta_label' => 'Shop Now',
        'home_hero_cta_url' => '/search',
        'home_banner_slides' => '[]',
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
        'settings.home_banner_type' => 'required|in:split,slider_only,text_only',
        'settings.home_hero_title' => 'nullable|string|max:255',
        'settings.home_hero_subtitle' => 'nullable|string|max:500',
        'settings.home_hero_cta_label' => 'nullable|string|max:100',
        'settings.home_hero_cta_url' => 'nullable|string|max:255',
        'bannerSlides' => 'array',
        'bannerSlides.*.image_file' => 'nullable|image|max:4096',
        'bannerSlides.*.link_url' => 'nullable|string|max:255',
        'bannerSlides.*.alt' => 'nullable|string|max:255',
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
            } elseif ($key === 'home_banner_slides') {
                $slides = is_string($current) ? json_decode($current, true) : $current;
                $this->bannerSlides = $this->normalizeSlides(is_array($slides) ? $slides : []);
            } else {
                $this->settings[$key] = $current;
            }
        }

        if (empty($this->bannerSlides)) {
            $this->bannerSlides = [
                [
                    'image' => '',
                    'image_file' => null,
                    'link_url' => '',
                    'alt' => '',
                ],
            ];
        }
    }

    public function save(): void
    {
        $this->validate();

        $slides = [];
        foreach ($this->bannerSlides as $index => $slide) {
            $slide = is_array($slide) ? $slide : [];
            $imagePath = trim((string) ($slide['image'] ?? ''));

            if (! empty($slide['image_file'])) {
                if ($imagePath !== '') {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $slide['image_file']->store('homepage/banners', 'public');
            }

            if ($imagePath === '') {
                continue;
            }

            $slides[] = [
                'image' => $imagePath,
                'link_url' => trim((string) ($slide['link_url'] ?? '')),
                'alt' => trim((string) ($slide['alt'] ?? '')),
            ];
        }

        if (($this->settings['home_hero_enabled'] ?? true) && in_array($this->settings['home_banner_type'] ?? 'split', ['split', 'slider_only'], true) && empty($slides)) {
            $this->addError('bannerSlides', 'Add at least one banner image.');
            return;
        }

        $this->settings['home_banner_slides'] = json_encode($slides);

        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : ($value ?? '')]
            );
        }

        session()->flash('message', 'Homepage settings saved successfully.');
    }

    public function addBannerSlide(): void
    {
        $this->bannerSlides[] = [
            'image' => '',
            'image_file' => null,
            'link_url' => '',
            'alt' => '',
        ];
    }

    public function removeBannerSlide(int $index): void
    {
        if (! isset($this->bannerSlides[$index])) {
            return;
        }

        array_splice($this->bannerSlides, $index, 1);

        if (empty($this->bannerSlides)) {
            $this->addBannerSlide();
        }
    }

    protected function normalizeSlides(array $slides): array
    {
        return collect($slides)->map(function ($slide) {
            $slide = is_array($slide) ? $slide : [];

            return [
                'image' => $slide['image'] ?? '',
                'image_file' => null,
                'link_url' => $slide['link_url'] ?? '',
                'alt' => $slide['alt'] ?? '',
            ];
        })->values()->all();
    }

    public function render()
    {
        return view('livewire.admin.homepage-settings');
    }
}
