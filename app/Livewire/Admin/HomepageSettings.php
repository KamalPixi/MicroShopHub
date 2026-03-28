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
    public array $bannerChips = [];

    public array $defaults = [
        'home_hero_enabled' => true,
        'home_banner_type' => 'split',
        'home_banner_autoplay_enabled' => true,
        'home_hero_title' => 'Find what fits your life',
        'home_hero_subtitle' => 'Curated products, fast delivery, and a storefront built for easy browsing.',
        'home_hero_cta_label' => 'Shop Now',
        'home_hero_cta_url' => '/search',
        'home_banner_chips' => '[]',
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
        'footer_about_title' => 'ShopHub',
        'footer_about_description' => 'Your trusted marketplace for clothing, health products, and unique handmade items.',
        'footer_social_facebook_url' => '',
        'footer_social_x_url' => '',
        'footer_social_instagram_url' => '',
        'footer_links_title' => 'Quick Links',
        'footer_link_1_label' => 'About Us',
        'footer_link_1_url' => '/about',
        'footer_link_2_label' => 'Contact',
        'footer_link_2_url' => '/contact',
        'footer_link_3_label' => 'FAQ',
        'footer_link_3_url' => '/faq',
        'footer_link_4_label' => 'Shipping Info',
        'footer_link_4_url' => '/shipping',
        'footer_support_title' => 'Customer Support',
        'footer_support_email' => 'support@shophub.com',
        'footer_support_phone' => '+1 (555) 123-4567',
        'footer_support_hours_1' => 'Mon-Fri: 9AM-6PM',
        'footer_support_hours_2' => 'Sat-Sun: 10AM-4PM',
        'footer_policy_title' => 'Policies',
        'footer_policy_1_label' => 'Privacy Policy',
        'footer_policy_1_url' => '/privacy-policy',
        'footer_policy_2_label' => 'Terms of Service',
        'footer_policy_2_url' => '/terms',
        'footer_policy_3_label' => 'Cookie Policy',
        'footer_policy_3_url' => '/cookie-policy',
        'footer_copyright_text' => '© {year} ShopHub. All rights reserved.',
    ];

    protected array $rules = [
        'settings.home_hero_enabled' => 'boolean',
        'settings.home_banner_type' => 'required|in:split,slider_only,text_only',
        'settings.home_banner_autoplay_enabled' => 'boolean',
        'settings.home_hero_title' => 'nullable|string|max:255',
        'settings.home_hero_subtitle' => 'nullable|string|max:500',
        'settings.home_hero_cta_label' => 'nullable|string|max:100',
        'settings.home_hero_cta_url' => 'nullable|string|max:255',
        'bannerChips' => 'array',
        'bannerChips.*.label' => 'nullable|string|max:100',
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
        'settings.footer_about_title' => 'nullable|string|max:255',
        'settings.footer_about_description' => 'nullable|string|max:500',
        'settings.footer_social_facebook_url' => 'nullable|string|max:255',
        'settings.footer_social_x_url' => 'nullable|string|max:255',
        'settings.footer_social_instagram_url' => 'nullable|string|max:255',
        'settings.footer_links_title' => 'nullable|string|max:255',
        'settings.footer_link_1_label' => 'nullable|string|max:100',
        'settings.footer_link_1_url' => 'nullable|string|max:255',
        'settings.footer_link_2_label' => 'nullable|string|max:100',
        'settings.footer_link_2_url' => 'nullable|string|max:255',
        'settings.footer_link_3_label' => 'nullable|string|max:100',
        'settings.footer_link_3_url' => 'nullable|string|max:255',
        'settings.footer_link_4_label' => 'nullable|string|max:100',
        'settings.footer_link_4_url' => 'nullable|string|max:255',
        'settings.footer_support_title' => 'nullable|string|max:255',
        'settings.footer_support_email' => 'nullable|string|max:255',
        'settings.footer_support_phone' => 'nullable|string|max:100',
        'settings.footer_support_hours_1' => 'nullable|string|max:100',
        'settings.footer_support_hours_2' => 'nullable|string|max:100',
        'settings.footer_policy_title' => 'nullable|string|max:255',
        'settings.footer_policy_1_label' => 'nullable|string|max:100',
        'settings.footer_policy_1_url' => 'nullable|string|max:255',
        'settings.footer_policy_2_label' => 'nullable|string|max:100',
        'settings.footer_policy_2_url' => 'nullable|string|max:255',
        'settings.footer_policy_3_label' => 'nullable|string|max:100',
        'settings.footer_policy_3_url' => 'nullable|string|max:255',
        'settings.footer_copyright_text' => 'nullable|string|max:255',
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
            } elseif ($key === 'home_banner_chips') {
                $chips = is_string($current) ? json_decode($current, true) : $current;
                $this->bannerChips = $this->normalizeChips(is_array($chips) ? $chips : []);
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

        if (empty($this->bannerChips)) {
            $this->bannerChips = [
                ['label' => 'Primary brand color'],
                ['label' => 'Fast checkout'],
                ['label' => 'Live support'],
            ];
        }
    }

    public function save(): void
    {
        $this->saveHeroBanner();
    }

    public function saveHeroBanner(): void
    {
        $this->validate([
            'settings.home_hero_enabled' => $this->rules['settings.home_hero_enabled'],
            'settings.home_banner_type' => $this->rules['settings.home_banner_type'],
            'settings.home_banner_autoplay_enabled' => $this->rules['settings.home_banner_autoplay_enabled'],
            'settings.home_hero_title' => $this->rules['settings.home_hero_title'],
            'settings.home_hero_subtitle' => $this->rules['settings.home_hero_subtitle'],
            'settings.home_hero_cta_label' => $this->rules['settings.home_hero_cta_label'],
            'settings.home_hero_cta_url' => $this->rules['settings.home_hero_cta_url'],
            'bannerChips' => $this->rules['bannerChips'],
            'bannerChips.*.label' => $this->rules['bannerChips.*.label'],
        ]);

        $chips = [];
        foreach ($this->bannerChips as $chip) {
            $chip = is_array($chip) ? $chip : [];
            $label = trim((string) ($chip['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $chips[] = ['label' => $label];
        }

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
        $this->settings['home_banner_chips'] = json_encode($chips);

        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : ($value ?? '')]
            );
        }

        session()->flash('message', 'Homepage settings saved successfully.');
    }

    public function saveBannerSlides(): void
    {
        $this->validate([
            'bannerSlides' => $this->rules['bannerSlides'],
            'bannerSlides.*.image_file' => $this->rules['bannerSlides.*.image_file'],
            'bannerSlides.*.link_url' => $this->rules['bannerSlides.*.link_url'],
            'bannerSlides.*.alt' => $this->rules['bannerSlides.*.alt'],
        ]);

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

        Setting::updateOrCreate(
            ['key' => 'home_banner_slides'],
            ['value' => $this->settings['home_banner_slides']]
        );

        session()->flash('message', 'Banner slides saved successfully.');
    }

    public function saveHomepageSections(): void
    {
        $this->validate([
            'settings.home_shop_by_category_enabled' => $this->rules['settings.home_shop_by_category_enabled'],
            'settings.home_shop_by_category_title' => $this->rules['settings.home_shop_by_category_title'],
            'settings.home_featured_products_enabled' => $this->rules['settings.home_featured_products_enabled'],
            'settings.home_featured_products_title' => $this->rules['settings.home_featured_products_title'],
            'settings.home_new_arrivals_enabled' => $this->rules['settings.home_new_arrivals_enabled'],
            'settings.home_new_arrivals_title' => $this->rules['settings.home_new_arrivals_title'],
            'settings.home_newsletter_enabled' => $this->rules['settings.home_newsletter_enabled'],
            'settings.home_newsletter_title' => $this->rules['settings.home_newsletter_title'],
            'settings.home_newsletter_subtitle' => $this->rules['settings.home_newsletter_subtitle'],
        ]);

        $this->persistSettings([
            'home_shop_by_category_enabled',
            'home_shop_by_category_title',
            'home_featured_products_enabled',
            'home_featured_products_title',
            'home_new_arrivals_enabled',
            'home_new_arrivals_title',
            'home_newsletter_enabled',
            'home_newsletter_title',
            'home_newsletter_subtitle',
        ]);

        session()->flash('message', 'Homepage section settings saved successfully.');
    }

    public function saveFooter(): void
    {
        $this->validate([
            'settings.footer_about_title' => $this->rules['settings.footer_about_title'],
            'settings.footer_about_description' => $this->rules['settings.footer_about_description'],
            'settings.footer_social_facebook_url' => $this->rules['settings.footer_social_facebook_url'],
            'settings.footer_social_x_url' => $this->rules['settings.footer_social_x_url'],
            'settings.footer_social_instagram_url' => $this->rules['settings.footer_social_instagram_url'],
            'settings.footer_links_title' => $this->rules['settings.footer_links_title'],
            'settings.footer_link_1_label' => $this->rules['settings.footer_link_1_label'],
            'settings.footer_link_1_url' => $this->rules['settings.footer_link_1_url'],
            'settings.footer_link_2_label' => $this->rules['settings.footer_link_2_label'],
            'settings.footer_link_2_url' => $this->rules['settings.footer_link_2_url'],
            'settings.footer_link_3_label' => $this->rules['settings.footer_link_3_label'],
            'settings.footer_link_3_url' => $this->rules['settings.footer_link_3_url'],
            'settings.footer_link_4_label' => $this->rules['settings.footer_link_4_label'],
            'settings.footer_link_4_url' => $this->rules['settings.footer_link_4_url'],
            'settings.footer_support_title' => $this->rules['settings.footer_support_title'],
            'settings.footer_support_email' => $this->rules['settings.footer_support_email'],
            'settings.footer_support_phone' => $this->rules['settings.footer_support_phone'],
            'settings.footer_support_hours_1' => $this->rules['settings.footer_support_hours_1'],
            'settings.footer_support_hours_2' => $this->rules['settings.footer_support_hours_2'],
            'settings.footer_policy_title' => $this->rules['settings.footer_policy_title'],
            'settings.footer_policy_1_label' => $this->rules['settings.footer_policy_1_label'],
            'settings.footer_policy_1_url' => $this->rules['settings.footer_policy_1_url'],
            'settings.footer_policy_2_label' => $this->rules['settings.footer_policy_2_label'],
            'settings.footer_policy_2_url' => $this->rules['settings.footer_policy_2_url'],
            'settings.footer_policy_3_label' => $this->rules['settings.footer_policy_3_label'],
            'settings.footer_policy_3_url' => $this->rules['settings.footer_policy_3_url'],
            'settings.footer_copyright_text' => $this->rules['settings.footer_copyright_text'],
        ]);

        $this->persistSettings([
            'footer_about_title',
            'footer_about_description',
            'footer_social_facebook_url',
            'footer_social_x_url',
            'footer_social_instagram_url',
            'footer_links_title',
            'footer_link_1_label',
            'footer_link_1_url',
            'footer_link_2_label',
            'footer_link_2_url',
            'footer_link_3_label',
            'footer_link_3_url',
            'footer_link_4_label',
            'footer_link_4_url',
            'footer_support_title',
            'footer_support_email',
            'footer_support_phone',
            'footer_support_hours_1',
            'footer_support_hours_2',
            'footer_policy_title',
            'footer_policy_1_label',
            'footer_policy_1_url',
            'footer_policy_2_label',
            'footer_policy_2_url',
            'footer_policy_3_label',
            'footer_policy_3_url',
            'footer_copyright_text',
        ]);

        session()->flash('message', 'Footer settings saved successfully.');
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

    public function addBannerChip(): void
    {
        $this->bannerChips[] = [
            'label' => '',
        ];
    }

    public function removeBannerChip(int $index): void
    {
        if (! isset($this->bannerChips[$index])) {
            return;
        }

        array_splice($this->bannerChips, $index, 1);
    }

    public function clearBannerChips(): void
    {
        $this->bannerChips = [];
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

    protected function normalizeChips(array $chips): array
    {
        return collect($chips)->map(function ($chip) {
            $chip = is_array($chip) ? $chip : [];

            return [
                'label' => $chip['label'] ?? '',
            ];
        })->values()->all();
    }

    protected function persistSettings(array $keys): void
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $this->settings)) {
                continue;
            }

            $value = $this->settings[$key];
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : ($value ?? '')]
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.homepage-settings');
    }
}
