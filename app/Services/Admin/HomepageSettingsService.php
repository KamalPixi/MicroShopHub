<?php

namespace App\Services\Admin;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class HomepageSettingsService
{
    public function defaults(): array
    {
        return [
            'storefront_theme' => 'default',
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
            'footer_policy_4_label' => 'Refund Policy',
            'footer_policy_4_url' => '/refund-policy',
            'footer_copyright_text' => '© {year} ShopHub. All rights reserved.',
        ];
    }

    public function loadState(): array
    {
        $defaults = $this->defaults();
        $stored = Setting::whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->toArray();

        $settings = [];
        $bannerSlides = [];
        $bannerChips = [];

        foreach ($defaults as $key => $value) {
            $current = $stored[$key] ?? $value;

            if (str_ends_with($key, '_enabled')) {
                $settings[$key] = filter_var($current, FILTER_VALIDATE_BOOLEAN);
                continue;
            }

            if ($key === 'home_banner_slides') {
                $slides = is_string($current) ? json_decode($current, true) : $current;
                $bannerSlides = $this->normalizeSlides(is_array($slides) ? $slides : []);
                continue;
            }

            if ($key === 'home_banner_chips') {
                $chips = is_string($current) ? json_decode($current, true) : $current;
                $bannerChips = $this->normalizeChips(is_array($chips) ? $chips : []);
                continue;
            }

            $settings[$key] = $current;
        }

        if (empty($bannerSlides)) {
            $bannerSlides = [
                [
                    'image' => '',
                    'image_file' => null,
                    'link_url' => '',
                    'alt' => '',
                ],
            ];
        }

        return [
            'settings' => $settings,
            'bannerSlides' => $bannerSlides,
            'bannerChips' => $bannerChips,
        ];
    }

    public function saveStorefrontTheme(array $settings): void
    {
        $this->persistSettings($settings, ['storefront_theme']);
    }

    public function saveHeroBanner(array $settings, array $bannerSlides, array $bannerChips): array
    {
        $slides = $this->saveSlides($bannerSlides);
        $chips = $this->saveChips($bannerChips);

        if (($settings['home_hero_enabled'] ?? true) && in_array($settings['home_banner_type'] ?? 'split', ['split', 'slider_only'], true) && empty($slides)) {
            return [];
        }

        $settings['home_banner_slides'] = json_encode($slides);
        $settings['home_banner_chips'] = json_encode($chips);

        $this->persistSettings($settings, [
            'home_hero_enabled',
            'home_banner_type',
            'home_banner_autoplay_enabled',
            'home_hero_title',
            'home_hero_subtitle',
            'home_hero_cta_label',
            'home_hero_cta_url',
            'home_banner_slides',
            'home_banner_chips',
        ]);

        return $slides;
    }

    public function saveBannerSlides(array $settings, array $bannerSlides): array
    {
        $slides = $this->saveSlides($bannerSlides);

        if (($settings['home_hero_enabled'] ?? true) && in_array($settings['home_banner_type'] ?? 'split', ['split', 'slider_only'], true) && empty($slides)) {
            return [];
        }

        $settings['home_banner_slides'] = json_encode($slides);
        $this->persistSettings($settings, ['home_banner_slides']);

        return $slides;
    }

    public function saveHomepageSections(array $settings): void
    {
        $this->persistSettings($settings, [
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
    }

    public function saveFooter(array $settings): void
    {
        $this->persistSettings($settings, [
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
            'footer_policy_4_label',
            'footer_policy_4_url',
            'footer_copyright_text',
        ]);
    }

    protected function saveSlides(array $bannerSlides): array
    {
        $slides = [];

        foreach ($bannerSlides as $slide) {
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

        return $slides;
    }

    protected function saveChips(array $bannerChips): array
    {
        $chips = [];

        foreach ($bannerChips as $chip) {
            $chip = is_array($chip) ? $chip : [];
            $label = trim((string) ($chip['label'] ?? ''));

            if ($label === '') {
                continue;
            }

            $chips[] = ['label' => $label];
        }

        return $chips;
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

    protected function persistSettings(array $settings, array $keys): void
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $settings)) {
                continue;
            }

            $value = $settings[$key];
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : ($value ?? '')]
            );
        }
    }
}
