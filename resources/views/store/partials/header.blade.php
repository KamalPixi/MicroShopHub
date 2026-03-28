<header class="bg-white/95 backdrop-blur border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-3">
            <div class="flex items-center flex-shrink-0">
                <a href="{{ route('store.index') }}">
                    <div class="flex items-center gap-2">
                        @if(!empty($storeLogo ?? ''))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($storeLogo) }}" alt="{{ $storeName ?? 'Store logo' }}" class="h-9 w-9 rounded-lg object-cover border border-gray-200 bg-white">
                        @else
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-white font-bold">
                                {{ strtoupper(substr(($storeName ?? 'S'), 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 leading-none">{{ $storeName ?? 'ShopHub' }}</h1>
                            @if(!empty($storeSlogan ?? ''))
                                <p class="mt-0.5 text-[9px] font-medium uppercase tracking-[0.16em] text-primary/80">{{ $storeSlogan }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>

            <div class="flex-1 mx-4 lg:mx-8">
                <livewire:store.header-search />
            </div>

            <div class="flex items-center gap-3 flex-shrink-0">
                <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center justify-center h-10 w-10 rounded-full border border-gray-200 text-gray-600 hover:text-primary hover:border-primary/30 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>

                @livewire('store.cart-counter')
            </div>
        </div>
    </div>
</header>
