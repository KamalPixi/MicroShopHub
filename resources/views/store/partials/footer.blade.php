@php
    $footerSettings = $footerSettings ?? [];
    $year = date('Y');
    $footerTitle = $footerSettings['footer_about_title'] ?? 'ShopHub';
    $footerDescription = $footerSettings['footer_about_description'] ?? 'Your trusted marketplace for clothing, health products, and unique handmade items.';
    $quickLinks = [
        ['label' => $footerSettings['footer_link_1_label'] ?? 'About Us', 'url' => $footerSettings['footer_link_1_url'] ?? '/about'],
        ['label' => $footerSettings['footer_link_2_label'] ?? 'Contact', 'url' => $footerSettings['footer_link_2_url'] ?? '/contact'],
        ['label' => $footerSettings['footer_link_3_label'] ?? 'FAQ', 'url' => $footerSettings['footer_link_3_url'] ?? '/faq'],
        ['label' => $footerSettings['footer_link_4_label'] ?? 'Shipping Info', 'url' => $footerSettings['footer_link_4_url'] ?? '/shipping'],
    ];
    $policyLinks = [
        ['label' => $footerSettings['footer_policy_1_label'] ?? 'Privacy Policy', 'url' => $footerSettings['footer_policy_1_url'] ?? '/privacy-policy'],
        ['label' => $footerSettings['footer_policy_2_label'] ?? 'Terms of Service', 'url' => $footerSettings['footer_policy_2_url'] ?? '/terms'],
        ['label' => $footerSettings['footer_policy_3_label'] ?? 'Cookie Policy', 'url' => $footerSettings['footer_policy_3_url'] ?? '/cookie-policy'],
    ];
    $copyrightText = str_replace('{year}', $year, $footerSettings['footer_copyright_text'] ?? '© {year} ShopHub. All rights reserved.');
@endphp

<footer class="bg-gray-900 text-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-lg font-bold mb-4 text-primary">{{ $footerTitle }}</h3>
                <p class="text-gray-400 text-sm mb-4">{{ $footerDescription }}</p>

                <div class="flex space-x-4">
                    @if(!empty($footerSettings['footer_social_facebook_url'] ?? ''))
                        <a href="{{ $footerSettings['footer_social_facebook_url'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary transition-colors" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.52 1.5-3.9 3.8-3.9 1.1 0 2.25.2 2.25.2v2.48H15.2c-1.25 0-1.63.78-1.63 1.57V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0022 12z"/></svg>
                        </a>
                    @endif
                    @if(!empty($footerSettings['footer_social_x_url'] ?? ''))
                        <a href="{{ $footerSettings['footer_social_x_url'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary transition-colors" aria-label="X">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.9 2H22l-6.93 7.92L23.2 22h-6.35l-4.97-6.43L6.23 22H3.1l7.46-8.52L0.8 2h6.52l4.47 5.8L18.9 2zm-1.12 18h1.72L6.42 3.96H4.57L17.78 20z"/></svg>
                        </a>
                    @endif
                    @if(!empty($footerSettings['footer_social_instagram_url'] ?? ''))
                        <a href="{{ $footerSettings['footer_social_instagram_url'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary transition-colors" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="5" ry="5"></rect>
                                <path d="M8 12a4 4 0 118 0 4 4 0 01-8 0z"></path>
                                <path d="M17.5 6.5h.01"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <h4 class="font-semibold mb-4">{{ $footerSettings['footer_links_title'] ?? 'Quick Links' }}</h4>
                <ul class="space-y-2 text-sm">
                    @foreach($quickLinks as $link)
                        @if(!empty($link['label']))
                            <li><a href="{{ url($link['url']) }}" class="text-gray-400 hover:text-primary">{{ $link['label'] }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">{{ $footerSettings['footer_support_title'] ?? 'Customer Support' }}</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    @if(!empty($footerSettings['footer_support_email'] ?? ''))
                        <li>📧 {{ $footerSettings['footer_support_email'] }}</li>
                    @endif
                    @if(!empty($footerSettings['footer_support_phone'] ?? ''))
                        <li>📞 {{ $footerSettings['footer_support_phone'] }}</li>
                    @endif
                    @if(!empty($footerSettings['footer_support_hours_1'] ?? ''))
                        <li>🕒 {{ $footerSettings['footer_support_hours_1'] }}</li>
                    @endif
                    @if(!empty($footerSettings['footer_support_hours_2'] ?? ''))
                        <li>🕒 {{ $footerSettings['footer_support_hours_2'] }}</li>
                    @endif
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">{{ $footerSettings['footer_policy_title'] ?? 'Policies' }}</h4>
                <ul class="space-y-2 text-sm">
                    @foreach($policyLinks as $link)
                        @if(!empty($link['label']))
                            <li><a href="{{ url($link['url']) }}" class="text-gray-400 hover:text-primary">{{ $link['label'] }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 text-center">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 text-sm">
                <p class="text-gray-400">{{ $copyrightText }}</p>
            </div>
        </div>
    </div>
</footer>
