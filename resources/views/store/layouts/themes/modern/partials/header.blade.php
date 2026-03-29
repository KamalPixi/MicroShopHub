<header class="bg-white/90 backdrop-blur border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-3">
            <a href="{{ route('store.index') }}" class="flex items-center gap-2.5">
                @if(!empty($storeLogo ?? ''))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($storeLogo) }}" alt="{{ $storeName ?? 'Store logo' }}" class="h-10 w-10 rounded-2xl object-cover border border-gray-200 bg-white">
                @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-primary text-white font-bold shadow-sm">
                        {{ strtoupper(substr(($storeName ?? 'S'), 0, 1)) }}
                    </div>
                @endif
                <div class="leading-tight">
                    <h1 class="text-lg font-bold text-gray-900">{{ $storeName ?? 'ShopHub' }}</h1>
                    @if(!empty($storeSlogan ?? ''))
                        <p class="mt-0.5 text-[9px] uppercase tracking-[0.16em] leading-none text-primary/70">{{ $storeSlogan }}</p>
                    @endif
                </div>
            </a>

            <div class="hidden flex-1 px-6 lg:block">
                <livewire:store.header-search />
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('customer.dashboard') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition hover:border-primary/25 hover:text-primary">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>
                @livewire('store.cart-counter')
            </div>
        </div>

        <div class="pb-3 lg:hidden">
            <livewire:store.header-search />
        </div>
    </div>
</header>
