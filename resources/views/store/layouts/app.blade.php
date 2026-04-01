<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $brandSettings = \App\Models\Setting::whereIn('key', [
            'branding_color',
            'secondary_color',
            'accent_color',
            'storefront_theme',
            'pusher_app_id',
            'pusher_app_key',
            'pusher_app_secret',
            'pusher_app_cluster',
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
        ])->pluck('value', 'key');
        $storeMeta = \App\Models\Setting::whereIn('key', ['shop_name', 'site_title', 'shop_logo'])->pluck('value', 'key');
        $storeName = trim((string) ($storeMeta['shop_name'] ?? '')) ?: config('app.name', 'Store Name');
        $storeSlogan = $storeMeta['site_title'] ?? '';
        $storeLogo = $storeMeta['shop_logo'] ?? '';
        $primaryColor = $brandSettings['branding_color'] ?? '#2563eb';
        $secondaryColor = $brandSettings['secondary_color'] ?? '#64748b';
        $accentColor = $brandSettings['accent_color'] ?? '#f59e0b';
        $storefrontTheme = \App\Support\StorefrontTheme::currentKey();
        $liveChatEnabled = filter_var(\App\Models\Setting::where('key', 'live_chat_enabled')->value('value'), FILTER_VALIDATE_BOOLEAN);
        $pusherKey = $brandSettings['pusher_app_key'] ?? null;
        $pusherCluster = $brandSettings['pusher_app_cluster'] ?? 'mt1';
        $pusherEnabled = $liveChatEnabled && !empty($brandSettings['pusher_app_id']) && !empty($brandSettings['pusher_app_secret']) && $pusherKey;
        $footerSettings = $brandSettings;
    @endphp

    <title>@yield('title', $storeName)</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --color-primary: {{ $primaryColor }};
            --color-secondary: {{ $secondaryColor }};
            --color-accent: {{ $accentColor }};
        }
        .bg-primary { background-color: var(--color-primary); }
        .text-primary { color: var(--color-primary); }
        .border-primary { border-color: var(--color-primary); }
        .hover\:bg-primary:hover { background-color: var(--color-primary); }
        .hover\:text-primary:hover { color: var(--color-primary); }
        .ring-primary { --tw-ring-color: var(--color-primary); }
        .bg-secondary { background-color: var(--color-secondary); }
        .text-secondary { color: var(--color-secondary); }
        .border-secondary { border-color: var(--color-secondary); }
        .bg-accent { background-color: var(--color-accent); }
        .text-accent { color: var(--color-accent); }
        .border-accent { border-color: var(--color-accent); }
        [x-cloak] { display: none !important; }
    </style>

    @livewireStyles

    @if($pusherEnabled)
        <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        <script>
            if (typeof Pusher !== 'undefined' && typeof Echo !== 'undefined') {
                window.Pusher = Pusher;
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: @js($pusherKey),
                    cluster: @js($pusherCluster ?: 'mt1'),
                    forceTLS: true,
                });
                window.LiveChatEcho = window.Echo;
            }
        </script>
    @endif
</head>
<body class="{{ $storefrontTheme === 'modern' ? 'bg-[#f4f7fb]' : 'bg-gray-50' }} min-h-screen flex flex-col theme-{{ $storefrontTheme }}">
    @php
        $storefrontHeaderView = \App\Support\StorefrontTheme::partial('header');
        $storefrontNavbarView = \App\Support\StorefrontTheme::partial('navbar');
        $storefrontFooterView = \App\Support\StorefrontTheme::partial('footer');
    @endphp

    @include($storefrontHeaderView, ['storeName' => $storeName, 'storeLogo' => $storeLogo, 'storeSlogan' => $storeSlogan])
    @include($storefrontNavbarView)

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full {{ $storefrontTheme === 'modern' ? (request()->routeIs('store.index') ? 'pt-1 md:pt-2' : (request()->routeIs('store.product.show') ? 'py-3 md:py-4' : 'pt-0 md:pt-1')) : (request()->routeIs('store.product.show') ? 'py-3 md:py-4' : 'pt-2 md:pt-3') }}">
        @yield('content')
    </main>

    @include($storefrontFooterView, ['footerSettings' => $footerSettings, 'storeName' => $storeName, 'storeLogo' => $storeLogo, 'storeSlogan' => $storeSlogan])

    @livewire('store.live-chat-widget')

    @livewireScripts
</body>
</html>
