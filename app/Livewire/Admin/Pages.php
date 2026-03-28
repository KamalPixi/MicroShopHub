<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class Pages extends Component
{
    public array $settings = [];

    public array $defaults = [
        'page_privacy_title' => 'Privacy Policy',
        'page_privacy_content' => "Privacy Policy\n\nWe respect your privacy and handle your information carefully. We collect the details needed to process your orders, support your account, and improve your shopping experience.\n\nWhat we collect:\n- Name, email, phone number, and delivery address\n- Order history and payment details\n- Messages you send through support or live chat\n\nHow we use it:\n- To process orders and deliver products\n- To contact you about your order or support request\n- To improve the store and customer experience\n\nSharing:\n- We do not sell your personal information\n- We may share information with payment, delivery, or support providers only when needed to complete your order\n\nCookies and analytics:\n- We may use cookies to remember your session and improve browsing\n- You can disable cookies in your browser, but some features may stop working properly\n\nContact:\nIf you have questions about your privacy, contact our support team.",
        'page_terms_title' => 'Terms of Service',
        'page_terms_content' => "Terms of Service\n\nBy using our store, you agree to the terms below. Please read them before placing an order.\n\nOrders:\n- Product availability may change without notice\n- Orders are confirmed only after payment and verification where needed\n- We may cancel orders if there is a pricing, stock, or payment issue\n\nPricing and payments:\n- Prices may change at any time before checkout\n- Payment methods shown at checkout are the current available methods\n- Offline payment orders may require verification before confirmation\n\nShipping and delivery:\n- Delivery times are estimates and can vary by location and courier delays\n- Once an order is shipped, tracking or delivery updates depend on the shipping provider\n\nReturns and refunds:\n- Return and refund rules depend on the product type and store policy\n- Damaged or incorrect items should be reported as soon as possible\n\nAccount use:\n- You are responsible for keeping your account details accurate\n- You should not misuse the store, submit false details, or attempt unauthorized access\n\nWe may update these terms from time to time. Continued use of the store means you accept the updated terms.",
        'page_cookie_title' => 'Cookie Policy',
        'page_cookie_content' => "Cookie Policy\n\nThis store uses cookies and similar tools to keep the website working smoothly and to improve your experience.\n\nWhy we use cookies:\n- To keep you signed in during your visit\n- To remember your cart and preferences\n- To understand how the store is being used\n- To improve page speed, browsing, and marketing performance\n\nTypes of cookies:\n- Essential cookies: required for basic store functions\n- Preference cookies: remember settings like language or location\n- Analytics cookies: help us understand store traffic and performance\n- Marketing cookies: may help us show relevant offers and campaigns\n\nManaging cookies:\n- You can clear or block cookies from your browser settings\n- Some features may not work correctly if essential cookies are disabled\n\nIf you continue using the store, you agree to the use of cookies as described here.",
    ];

    protected array $rules = [
        'settings.page_privacy_title' => 'required|string|max:255',
        'settings.page_privacy_content' => 'required|string',
        'settings.page_terms_title' => 'required|string|max:255',
        'settings.page_terms_content' => 'required|string',
        'settings.page_cookie_title' => 'required|string|max:255',
        'settings.page_cookie_content' => 'required|string',
    ];

    public function mount(): void
    {
        $stored = Setting::whereIn('key', array_keys($this->defaults))
            ->pluck('value', 'key')
            ->toArray();

        foreach ($this->defaults as $key => $value) {
            $this->settings[$key] = $stored[$key] ?? $value;
        }
    }

    public function savePrivacy(): void
    {
        $this->validate([
            'settings.page_privacy_title' => $this->rules['settings.page_privacy_title'],
            'settings.page_privacy_content' => $this->rules['settings.page_privacy_content'],
        ]);

        $this->persistSettings(['page_privacy_title', 'page_privacy_content']);

        session()->flash('message', 'Privacy policy saved successfully.');
    }

    public function saveTerms(): void
    {
        $this->validate([
            'settings.page_terms_title' => $this->rules['settings.page_terms_title'],
            'settings.page_terms_content' => $this->rules['settings.page_terms_content'],
        ]);

        $this->persistSettings(['page_terms_title', 'page_terms_content']);

        session()->flash('message', 'Terms of service saved successfully.');
    }

    public function saveCookie(): void
    {
        $this->validate([
            'settings.page_cookie_title' => $this->rules['settings.page_cookie_title'],
            'settings.page_cookie_content' => $this->rules['settings.page_cookie_content'],
        ]);

        $this->persistSettings(['page_cookie_title', 'page_cookie_content']);

        session()->flash('message', 'Cookie policy saved successfully.');
    }

    protected function persistSettings(array $keys): void
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $this->settings)) {
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $this->settings[$key] ?? '']
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.pages');
    }
}
