<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ShopHub - Your One-Stop Shopping Destination')</title>
    @php
        $brandSettings = \App\Models\Setting::whereIn('key', [
            'branding_color',
            'secondary_color',
            'accent_color',
        ])->pluck('value', 'key');
        $primaryColor = $brandSettings['branding_color'] ?? '#2563eb';
        $secondaryColor = $brandSettings['secondary_color'] ?? '#64748b';
        $accentColor = $brandSettings['accent_color'] ?? '#f59e0b';
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
    </style>

    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    @include('store.partials.header')
    @include('store.partials.navbar')

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        @yield('content')
    </main>

    @include('store.partials.footer')

    @livewire('store.live-chat-widget')

    @livewireScripts
</body>
</html>
