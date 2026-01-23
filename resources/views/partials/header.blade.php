<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center flex-shrink-0">
                <a href="{{ route('store.index') }}">
                    <h1 class="text-2xl font-bold text-primary">ShopHub</h1>
                </a>
            </div>

            <div class="flex-1 mx-4 lg:mx-8">
                <livewire:header-search />
            </div>

            <div class="flex items-center space-x-4 flex-shrink-0">
                <a href="#" class="p-2 text-gray-600 hover:text-primary transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>

                @livewire('cart-counter')
            </div>
        </div>
    </div>
</header>
