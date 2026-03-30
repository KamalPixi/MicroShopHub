<?php

namespace App\Livewire\Admin;

use App\Services\Admin\HomepageSettingsService;
use Livewire\WithFileUploads;
use Livewire\Component;

class HomepageSettings extends Component
{
    use WithFileUploads;

    protected HomepageSettingsService $service;

    public array $settings = [];
    public array $bannerSlides = [];
    public array $bannerChips = [];

    protected array $rules = [
        'settings.storefront_theme' => 'required|in:default,modern',
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
        'settings.footer_policy_4_label' => 'nullable|string|max:100',
        'settings.footer_policy_4_url' => 'nullable|string|max:255',
        'settings.footer_copyright_text' => 'nullable|string|max:255',
    ];

    public function mount(HomepageSettingsService $service): void
    {
        $this->service = $service;
        $state = $service->loadState();
        $this->settings = $state['settings'];
        $this->bannerSlides = $state['bannerSlides'];
        $this->bannerChips = $state['bannerChips'];
    }

    public function save(): void
    {
        $this->saveStorefrontTheme();
    }

    public function saveStorefrontTheme(): void
    {
        $this->validate([
            'settings.storefront_theme' => $this->rules['settings.storefront_theme'],
        ]);

        $this->service->saveStorefrontTheme($this->settings);

        session()->flash('message', 'Storefront theme saved successfully.');
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

        $slides = $this->service->saveHeroBanner($this->settings, $this->bannerSlides, $this->bannerChips);

        if (($this->settings['home_hero_enabled'] ?? true) && in_array($this->settings['home_banner_type'] ?? 'split', ['split', 'slider_only'], true) && empty($slides)) {
            $this->addError('bannerSlides', 'Add at least one banner image.');
            return;
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

        $slides = $this->service->saveBannerSlides($this->settings, $this->bannerSlides);

        if (($this->settings['home_hero_enabled'] ?? true) && in_array($this->settings['home_banner_type'] ?? 'split', ['split', 'slider_only'], true) && empty($slides)) {
            $this->addError('bannerSlides', 'Add at least one banner image.');
            return;
        }

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

        $this->service->saveHomepageSections($this->settings);

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
            'settings.footer_policy_4_label' => $this->rules['settings.footer_policy_4_label'],
            'settings.footer_policy_4_url' => $this->rules['settings.footer_policy_4_url'],
            'settings.footer_copyright_text' => $this->rules['settings.footer_copyright_text'],
        ]);

        $this->service->saveFooter($this->settings);

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

    public function render()
    {
        return view('livewire.admin.homepage-settings');
    }
}
