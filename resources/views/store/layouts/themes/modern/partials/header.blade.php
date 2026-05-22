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
                    <h1 class="text-lg font-bold text-gray-900">{{ $storeName ?? __('store.shop_name_default') }}</h1>
                    <p class="mt-0.5 text-[9px] uppercase tracking-[0.16em] leading-none text-primary/70">{{ $storeSlogan ?: __('store.shop_slogan_default') }}</p>
                </div>
            </a>

            <div class="hidden flex-1 px-6 lg:block">
                <livewire:store.header-search />
            </div>

            <div class="flex items-center gap-2">
                @if(count($storeLocales ?? []) > 1)
                    <div class="hidden sm:flex items-center gap-1 rounded-full border border-gray-200 bg-white p-1">
                        @foreach($storeLocales as $locale => $meta)
                            <a
                                href="{{ route('store.language.switch', $locale) }}"
                                class="rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] transition {{ ($storeLocale ?? app()->getLocale()) === $locale ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' }}"
                            >
                                {{ $meta['native'] }}
                            </a>
                        @endforeach
                    </div>
                @endif

                @auth
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button
                            @click="open = !open"
                            class="inline-flex items-center justify-center h-10 px-3.5 rounded-full border border-blue-100 bg-blue-50/30 text-blue-600 hover:bg-blue-50/60 active:scale-95 transition-all text-xs font-bold gap-1.5 focus:outline-none"
                            title="Account Options"
                        >
                            <span class="h-5 w-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-[9px] uppercase">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>
                            <span class="hidden sm:inline line-clamp-1 max-w-[80px]">
                                {{ explode(' ', auth()->user()->name)[0] }}
                            </span>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 shadow-xl rounded-2xl py-2 z-50 animate-in fade-in slide-in-from-top-1 duration-150"
                        >
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Signed in as</p>
                                <p class="text-xs font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                            </div>
                            <a
                                href="/dashboard"
                                class="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors"
                            >
                                My Dashboard
                            </a>
                            <a
                                href="/checkout"
                                class="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors"
                            >
                                Checkout Portal
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-50 mt-1">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full text-left block px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50/50 transition-colors"
                                >
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="/login" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-600 transition hover:border-primary/25 hover:text-primary">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                @endauth
                @livewire('store.cart-counter')
            </div>
        </div>

        <div class="pb-3 lg:hidden">
            <livewire:store.header-search />
        </div>
    </div>
</header>
