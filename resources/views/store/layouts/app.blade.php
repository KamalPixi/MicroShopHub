<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ShopHub - Your One-Stop Shopping Destination')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#64748b',
                    }
                }
            }
        }
    </script>

    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    @include('store.partials.header')
    @include('store.partials.navbar')

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        @yield('content')
    </main>

    @include('store.partials.footer')

    @livewireScripts
</body>
</html>
