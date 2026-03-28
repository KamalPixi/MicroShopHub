<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopHub - Your One-Stop Shopping Destination')</title>
    @php
        $brandSettings = \App\Models\Setting::whereIn('key', [
            'branding_color',
            'secondary_color',
            'accent_color',
            'pusher_app_id',
            'pusher_app_key',
            'pusher_app_secret',
            'pusher_app_cluster',
        ])->pluck('value', 'key');
        $primaryColor = $brandSettings['branding_color'] ?? '#2563eb';
        $secondaryColor = $brandSettings['secondary_color'] ?? '#64748b';
        $accentColor = $brandSettings['accent_color'] ?? '#f59e0b';
        $liveChatEnabled = filter_var(\App\Models\Setting::where('key', 'live_chat_enabled')->value('value'), FILTER_VALIDATE_BOOLEAN);
        $pusherKey = $brandSettings['pusher_app_key'] ?? null;
        $pusherCluster = $brandSettings['pusher_app_cluster'] ?? 'mt1';
        $pusherEnabled = $liveChatEnabled && !empty($brandSettings['pusher_app_id']) && !empty($brandSettings['pusher_app_secret']) && $pusherKey;
    @endphp

    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="bg-gray-50 min-h-screen flex flex-col">
    @include('store.partials.header')
    @include('store.partials.navbar')

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-4 md:pt-6">
        @yield('content')
    </main>

    @include('store.partials.footer')

    @livewire('store.live-chat-widget')

    @livewireScripts
</body>
</html>
