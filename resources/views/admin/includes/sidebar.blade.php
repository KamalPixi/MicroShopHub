<aside id="sidebar" class="w-56 bg-slate-900 text-slate-100 p-4 flex flex-col fixed top-0 bottom-0 border-r border-slate-800">
@php
        $sidebarSettings = \App\Models\Setting::whereIn('key', ['shop_name', 'site_title', 'shop_logo'])->pluck('value', 'key');
        $sidebarStoreName = $sidebarSettings['shop_name'] ?: ($sidebarSettings['site_title'] ?: config('app.name', 'Store Name'));
        $sidebarStoreLogo = $sidebarSettings['shop_logo'] ?? null;
        $sidebarStoreLogoUrl = $sidebarStoreLogo
            ? (\Illuminate\Support\Str::startsWith($sidebarStoreLogo, ['http://', 'https://'])
                ? $sidebarStoreLogo
                : \Illuminate\Support\Facades\Storage::url($sidebarStoreLogo))
            : null;
    @endphp
    <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-800">
        <div class="flex items-center gap-2">
            <div class="h-9 w-9 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center overflow-hidden">
                @if($sidebarStoreLogoUrl)
                    <img src="{{ $sidebarStoreLogoUrl }}" alt="{{ $sidebarStoreName }} logo" class="h-full w-full object-contain p-1">
                @else
                    <svg class="w-4.5 h-4.5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3V3z"></path>
                    </svg>
                @endif
            </div>
            <div>
                <h1 class="text-sm font-semibold tracking-tight text-slate-100 leading-none">{{ $sidebarStoreName }}</h1>
                <p class="text-[11px] text-slate-400">Admin Panel</p>
            </div>
        </div>
        <button id="toggle-sidebar" class="text-slate-200 hover:bg-slate-800 p-1.5 rounded-md" aria-label="Collapse sidebar">
            <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 6h10M5 12h14M5 18h8"></path>
            </svg>
        </button>
    </div>
    <nav class="flex-1 overflow-y-auto">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center text-sm px-2.5 py-2 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 19"></path>
                    </svg>
                    Dashboard
                </a>
            </li>

            @php
                $isProductsOpen = request()->routeIs('admin.products*', 'admin.categories*', 'admin.discounts*');
            @endphp
            <li>
                <button 
                    class="flex items-center text-sm px-2.5 py-2 rounded-md w-full {{ $isProductsOpen ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}" 
                    onclick="document.getElementById('products-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-90')"
                >
                    <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Products
                    <svg class="chevron ml-auto w-4 h-4 transform transition-transform {{ $isProductsOpen ? 'rotate-90' : '' }}" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <ul id="products-submenu" class="ml-6 mt-1 space-y-1 {{ $isProductsOpen ? '' : 'hidden' }}">
                    <li>
                        <a href="{{ route('admin.products.index') }}" 
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.products*') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            All Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories') }}" 
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.categories*') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Categories
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.discounts.index') }}" 
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.discounts*') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Coupons
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center text-sm px-2.5 py-2 rounded-md {{ request()->routeIs('admin.orders*') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Orders
                </a>
            </li>

            <li>
                <a href="{{ route('admin.customers.index') }}" 
                   class="flex items-center text-sm px-2.5 py-2 rounded-md {{ request()->routeIs('admin.customers*') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Customers
                </a>
            </li>

            <li>
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center text-sm px-2.5 py-2 rounded-md {{ request()->routeIs('admin.users*') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                    <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a4 4 0 00-4-4h-1
                                 M9 20H4v-2a4 4 0 014-4h1
                                 M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users
                </a>
            </li>

            @php
                $isPagesOpen = request()->routeIs('admin.pages*');
            @endphp
            <li>
                <button
                    class="flex items-center text-sm px-2.5 py-2 rounded-md w-full {{ $isPagesOpen ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}"
                    onclick="document.getElementById('pages-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-90')"
                >
                    <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h6l4 4v14H7V3zM13 3v5h5"></path>
                    </svg>
                    Pages
                    <svg class="chevron ml-auto w-4 h-4 transform transition-transform {{ $isPagesOpen ? 'rotate-90' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <ul id="pages-submenu" class="ml-6 mt-1 space-y-1 {{ $isPagesOpen ? '' : 'hidden' }}">
                    <li>
                        <a href="{{ route('admin.pages') }}"
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.pages') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Page Overview
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pages.privacy') }}"
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.pages.privacy') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pages.terms') }}"
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.pages.terms') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Terms of Service
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pages.cookie') }}"
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.pages.cookie') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Cookie Policy
                        </a>
                    </li>
                </ul>
            </li>

            @php
                $isMarketingOpen = request()->routeIs('admin.marketing.*');
            @endphp
            <li>
                <button 
                    class="flex items-center text-sm px-2.5 py-2 rounded-md w-full {{ $isMarketingOpen ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}" 
                    onclick="document.getElementById('marketing-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-90')"
                >
                    <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h7M11 9h7M3 5h4v4H3V5zm0 6h4v4H3v-4zm0 6h4v4H3v-4z"></path>
                    </svg>
                    Marketing
                    <svg class="chevron ml-auto w-4 h-4 transform transition-transform {{ $isMarketingOpen ? 'rotate-90' : '' }}" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <ul id="marketing-submenu" class="ml-6 mt-1 space-y-1 {{ $isMarketingOpen ? '' : 'hidden' }}">
                    <li>
                        <a href="{{ route('admin.marketing.subscriptions') }}" 
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.marketing.subscriptions') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Subscriptions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.marketing.campaigns') }}" 
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.marketing.campaigns') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Campaigns
                        </a>
                    </li>
                </ul>
            </li>

            @php
                $isSettingsOpen = request()->routeIs('admin.settings*', 'admin.homepage-settings*', 'admin.shipping-methods*');
            @endphp
            <li>
                <button 
                    class="flex items-center text-sm px-2.5 py-2 rounded-md w-full {{ $isSettingsOpen ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}" 
                    onclick="document.getElementById('settings-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-90')"
                >
                    <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066
                                c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572
                                c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573
                                c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065
                                c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066
                                c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572
                                c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573
                                c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                    <svg class="chevron ml-auto w-4 h-4 transform transition-transform {{ $isSettingsOpen ? 'rotate-90' : '' }}" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <ul id="settings-submenu" class="ml-6 mt-1 space-y-1 {{ $isSettingsOpen ? '' : 'hidden' }}">
                    <li>
                        <a href="{{ route('admin.settings') }}" 
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.settings*') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Shop Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.homepage.settings') }}" 
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.homepage.settings*') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Homepage Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipping.methods') }}" 
                           class="block text-sm px-2.5 py-1.5 rounded-md {{ request()->routeIs('admin.shipping.methods*') ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800' }}">
                            Shipping Methods
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center text-sm px-2.5 py-2 rounded-md text-slate-200 hover:bg-slate-800">
                        <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                        Signout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>
