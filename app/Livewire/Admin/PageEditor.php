<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class PageEditor extends Component
{
    public string $page = 'about';
    public array $settings = [];

    protected array $pageMap = [
        'about' => [
            'title_key' => 'page_about_title',
            'content_key' => 'page_about_content',
            'default_title' => 'About Us',
            'default_content' => "About Us\n\nWe are focused on making shopping simple, fast, and trustworthy. Our store brings together curated products, clear pricing, and easy checkout so customers can shop with confidence.\n\nWhat we stand for:\n- Honest product information\n- Helpful customer support\n- Secure checkout and clear order updates\n- Fast responses when customers need help\n\nWhy customers choose us:\n- Easy browsing across categories\n- Multiple payment options\n- Delivery and support guidance when needed\n- A shopping experience built around convenience\n\nIf you have questions about our store, products, or services, our team is here to help.",
            'label' => 'About Us',
        ],
        'faq' => [
            'title_key' => 'page_faq_title',
            'content_key' => 'page_faq_content',
            'default_title' => 'FAQ',
            'default_content' => "FAQ\n\nHere are answers to common questions about shopping with us.\n\nOrders:\n- How do I place an order?\n  Browse the store, add items to cart, and complete checkout.\n- Can I change my order after placing it?\n  Contact us as soon as possible and we will check if the order can still be updated.\n\nPayments:\n- Which payment methods do you accept?\n  Available methods are shown at checkout and may include online and offline options.\n- Is offline payment supported?\n  Yes, where enabled, you can upload proof and wait for approval.\n\nShipping:\n- How long does delivery take?\n  Delivery time depends on location and shipping method.\n- Can I track my order?\n  Tracking depends on the selected delivery method and order status.\n\nSupport:\n- How can I contact support?\n  Use the contact page or live chat for help.\n- What should I do if I received the wrong item?\n  Contact support right away with your order details and proof if possible.\n\nIf you have another question, please reach out to us.",
            'label' => 'FAQ',
        ],
        'privacy' => [
            'title_key' => 'page_privacy_title',
            'content_key' => 'page_privacy_content',
            'default_title' => 'Privacy Policy',
            'default_content' => "Privacy Policy\n\nWe respect your privacy and handle your information carefully. We collect the details needed to process your orders, support your account, and improve your shopping experience.\n\nWhat we collect:\n- Name, email, phone number, and delivery address\n- Order history and payment details\n- Messages you send through support or live chat\n\nHow we use it:\n- To process orders and deliver products\n- To contact you about your order or support request\n- To improve the store and customer experience\n\nSharing:\n- We do not sell your personal information\n- We may share information with payment, delivery, or support providers only when needed to complete your order\n\nCookies and analytics:\n- We may use cookies to remember your session and improve browsing\n- You can disable cookies in your browser, but some features may stop working properly\n\nContact:\nIf you have questions about your privacy, contact our support team.",
            'label' => 'Privacy Policy',
        ],
        'terms' => [
            'title_key' => 'page_terms_title',
            'content_key' => 'page_terms_content',
            'default_title' => 'Terms of Service',
            'default_content' => "Terms of Service\n\nBy using our store, you agree to the terms below. Please read them before placing an order.\n\nOrders:\n- Product availability may change without notice\n- Orders are confirmed only after payment and verification where needed\n- We may cancel orders if there is a pricing, stock, or payment issue\n\nPricing and payments:\n- Prices may change at any time before checkout\n- Payment methods shown at checkout are the current available methods\n- Offline payment orders may require verification before confirmation\n\nShipping and delivery:\n- Delivery times are estimates and can vary by location and courier delays\n- Once an order is shipped, tracking or delivery updates depend on the shipping provider\n\nReturns and refunds:\n- Return and refund rules depend on the product type and store policy\n- Damaged or incorrect items should be reported as soon as possible\n\nAccount use:\n- You are responsible for keeping your account details accurate\n- You should not misuse the store, submit false details, or attempt unauthorized access\n\nWe may update these terms from time to time. Continued use of the store means you accept the updated terms.",
            'label' => 'Terms of Service',
        ],
        'refund' => [
            'title_key' => 'page_refund_title',
            'content_key' => 'page_refund_content',
            'default_title' => 'Refund Policy',
            'default_content' => "Refund Policy\n\nWe want every customer to feel comfortable shopping with us. If a refund is approved, it will be handled according to the rules below.\n\nWhen refunds may apply:\n- Damaged items received on delivery\n- Wrong items shipped by mistake\n- Orders cancelled before processing or shipping, where possible\n- Other cases approved by our support team\n\nWhen refunds may not apply:\n- Change of mind after an order has been processed\n- Items damaged due to customer handling\n- Requests made outside the allowed return window\n- Products that are not eligible for return or refund under store rules\n\nRefund process:\n- Contact support with your order number and proof\n- Our team will review the request\n- If approved, the refund or replacement will be arranged based on the payment method and order status\n\nTiming:\n- Refund timing depends on the payment method and processing time\n- Some payments may take additional business days to appear\n\nIf you need help with a refund request, contact our support team as soon as possible.",
            'label' => 'Refund Policy',
        ],
        'cookie' => [
            'title_key' => 'page_cookie_title',
            'content_key' => 'page_cookie_content',
            'default_title' => 'Cookie Policy',
            'default_content' => "Cookie Policy\n\nThis store uses cookies and similar tools to keep the website working smoothly and to improve your experience.\n\nWhy we use cookies:\n- To keep you signed in during your visit\n- To remember your cart and preferences\n- To understand how the store is being used\n- To improve page speed, browsing, and marketing performance\n\nTypes of cookies:\n- Essential cookies: required for basic store functions\n- Preference cookies: remember settings like language or location\n- Analytics cookies: help us understand store traffic and performance\n- Marketing cookies: may help us show relevant offers and campaigns\n\nManaging cookies:\n- You can clear or block cookies from your browser settings\n- Some features may not work correctly if essential cookies are disabled\n\nIf you continue using the store, you agree to the use of cookies as described here.",
            'label' => 'Cookie Policy',
        ],
    ];

    public function mount(string $page = 'about'): void
    {
        $this->page = array_key_exists($page, $this->pageMap) ? $page : 'about';

        $config = $this->currentConfig();
        $stored = Setting::whereIn('key', [$config['title_key'], $config['content_key']])
            ->pluck('value', 'key')
            ->toArray();

        $this->settings[$config['title_key']] = $stored[$config['title_key']] ?? $config['default_title'];
        $this->settings[$config['content_key']] = $stored[$config['content_key']] ?? $config['default_content'];
    }

    public function save(): void
    {
        $config = $this->currentConfig();

        $this->validate([
            'settings.' . $config['title_key'] => 'required|string|max:255',
            'settings.' . $config['content_key'] => 'required|string',
        ]);

        Setting::updateOrCreate(
            ['key' => $config['title_key']],
            ['value' => $this->settings[$config['title_key']] ?? '']
        );

        Setting::updateOrCreate(
            ['key' => $config['content_key']],
            ['value' => $this->settings[$config['content_key']] ?? '']
        );

        session()->flash('message', $config['label'] . ' saved successfully.');
    }

    protected function currentConfig(): array
    {
        return $this->pageMap[$this->page] ?? $this->pageMap['privacy'];
    }

    public function render()
    {
        return view('livewire.admin.page-editor', [
            'pageLabel' => $this->currentConfig()['label'],
            'titleKey' => $this->currentConfig()['title_key'],
            'contentKey' => $this->currentConfig()['content_key'],
        ]);
    }
}
