<aside id="sidebar" class="w-60 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 p-4 flex flex-col fixed top-0 bottom-0 border-r border-white/10 shadow-[0_24px_60px_rgba(2,6,23,0.45)]">
@php
        $sidebarSettings = \App\Models\Setting::whereIn('key', ['shop_name', 'site_title', 'shop_logo'])->pluck('value', 'key');
        $sidebarStoreName = $sidebarSettings['shop_name'] ?: config('app.name', 'Store Name');
        $sidebarStoreLogo = $sidebarSettings['shop_logo'] ?? null;
        $sidebarStoreLogoUrl = $sidebarStoreLogo
            ? (\Illuminate\Support\Str::startsWith($sidebarStoreLogo, ['http://', 'https://'])
                ? $sidebarStoreLogo
                : \Illuminate\Support\Facades\Storage::url($sidebarStoreLogo))
            : null;
        $sidebarStoreSlogan = $sidebarSettings['site_title'] ?? '';
        $adminUser = auth('admin')->user();
    @endphp
    <div class="mb-5 rounded-2xl border border-white/10 bg-white/5 p-3 shadow-sm backdrop-blur-sm">
        <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="h-10 w-10 rounded-2xl bg-primary/20 border border-white/10 flex items-center justify-center overflow-hidden shadow-inner">
                @if($sidebarStoreLogoUrl)
                    <img src="{{ $sidebarStoreLogoUrl }}" alt="{{ $sidebarStoreName }} logo" class="h-full w-full object-contain p-1">
                @else
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3V3z"></path>
                    </svg>
                @endif
            </div>
            <div class="min-w-0">
                <h1 class="text-sm font-semibold tracking-tight text-white leading-none truncate">{{ $sidebarStoreName }}</h1>
                @if(!empty($sidebarStoreSlogan))
                    <p class="mt-1 text-[10px] uppercase tracking-[0.18em] text-slate-400 truncate">{{ $sidebarStoreSlogan }}</p>
                @else
                    <p class="mt-1 text-[10px] uppercase tracking-[0.18em] text-slate-400">Admin Panel</p>
                @endif
            </div>
        </div>
        <button id="toggle-sidebar" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-white/5 text-slate-200 transition hover:bg-white/10 hover:text-white" aria-label="Collapse sidebar">
            <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 6h10M5 12h14M5 18h8"></path>
            </svg>
        </button>
        </div>
    </div>
    <nav class="flex-1 overflow-y-auto pr-1">
        <ul class="space-y-2">
            <li class="px-2 pt-1">
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-500">Overview</p>
            </li>
            @if($adminUser?->hasPermission('dashboard.view'))
            <li>
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 19"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            @endif

            @php
                $isProductsOpen = request()->routeIs('admin.products*', 'admin.categories*', 'admin.discounts*');
            @endphp
            @if($adminUser?->hasPermission('products.manage') || $adminUser?->hasPermission('categories.manage') || $adminUser?->hasPermission('coupons.manage'))
                <li class="px-2 pt-2">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-500">Catalog</p>
                </li>
                <li>
                    <button 
                        class="flex items-center gap-3 text-sm px-3 py-2.5 rounded-xl w-full font-medium transition {{ $isProductsOpen ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}" 
                        onclick="document.getElementById('products-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-90')"
                    >
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="flex-1 text-left">Products</span>
                        <svg class="chevron ml-auto w-4 h-4 transform transition-transform {{ $isProductsOpen ? 'rotate-90' : '' }}" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <ul id="products-submenu" class="ml-4 mt-2 space-y-1.5 border-l border-white/10 pl-3 {{ $isProductsOpen ? '' : 'hidden' }}">
                        @if($adminUser?->hasPermission('products.manage'))
                            <li>
                                <a href="{{ route('admin.products.index') }}" 
                                   class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.products*') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                    All Products
                                </a>
                            </li>
                        @endif
                        @if($adminUser?->hasPermission('categories.manage'))
                            <li>
                                <a href="{{ route('admin.categories') }}" 
                                   class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.categories*') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                    Categories
                                </a>
                            </li>
                        @endif
                        @if($adminUser?->hasPermission('coupons.manage'))
                            <li>
                                <a href="{{ route('admin.discounts.index') }}" 
                                   class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.discounts*') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                    Coupons
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if($adminUser?->hasPermission('orders.manage') || $adminUser?->hasPermission('customers.manage') || $adminUser?->hasPermission('admins.manage') || $adminUser?->hasPermission('contact.manage'))
                <li class="px-2 pt-2">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-500">Operations</p>
                </li>
                @if($adminUser?->hasPermission('orders.manage'))
                    <li>
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.orders*') ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Orders
                        </a>
                    </li>
                @endif

                @if($adminUser?->hasPermission('customers.manage'))
                    <li>
                        <a href="{{ route('admin.customers.index') }}" 
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.customers*') ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Customers
                        </a>
                    </li>
                @endif

                @if($adminUser?->hasPermission('admins.manage'))
                    <li>
                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.users*') ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a4 4 0 00-4-4h-1
                                         M9 20H4v-2a4 4 0 014-4h1
                                         M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.roles') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.roles*') ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1M16 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Roles
                        </a>
                    </li>
                @endif

                @if($adminUser?->hasPermission('contact.manage'))
                    <li>
                        <a href="{{ route('admin.contact.messages') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.contact.messages*') ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8m-8 4h5m-9 5h14a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Contact
                        </a>
                    </li>
                @endif
            @endif

            @php
                $isPagesOpen = request()->routeIs('admin.pages*');
            @endphp
            @if($adminUser?->hasPermission('pages.manage'))
                <li class="px-2 pt-2">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-500">Content</p>
                </li>
                <li>
                    <button
                        class="flex items-center gap-3 text-sm px-3 py-2.5 rounded-xl w-full font-medium transition {{ $isPagesOpen ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}"
                        onclick="document.getElementById('pages-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-90')"
                    >
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h6l4 4v14H7V3zM13 3v5h5"></path>
                        </svg>
                        <span class="flex-1 text-left">Pages</span>
                        <svg class="chevron ml-auto w-4 h-4 transform transition-transform {{ $isPagesOpen ? 'rotate-90' : '' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <ul id="pages-submenu" class="ml-4 mt-2 space-y-1.5 border-l border-white/10 pl-3 {{ $isPagesOpen ? '' : 'hidden' }}">
                        <li>
                            <a href="{{ route('admin.pages') }}"
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Page Overview
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pages.about') }}"
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages.about') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                About Us
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pages.privacy') }}"
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages.privacy') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pages.faq') }}"
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages.faq') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                FAQ
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pages.terms') }}"
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages.terms') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Terms of Service
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pages.refund') }}"
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages.refund') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Refund Policy
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pages.shipping') }}"
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages.shipping') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Shipping Info
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pages.cookie') }}"
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pages.cookie') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Cookie Policy
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @php
                $isMarketingOpen = request()->routeIs('admin.marketing.*');
            @endphp
            @if($adminUser?->hasPermission('marketing.manage'))
                <li class="px-2 pt-2">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-500">Growth</p>
                </li>
                <li>
                    <button 
                        class="flex items-center gap-3 text-sm px-3 py-2.5 rounded-xl w-full font-medium transition {{ $isMarketingOpen ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}" 
                        onclick="document.getElementById('marketing-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-90')"
                    >
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h7M11 9h7M3 5h4v4H3V5zm0 6h4v4H3v-4zm0 6h4v4H3v-4z"></path>
                        </svg>
                        <span class="flex-1 text-left">Marketing</span>
                        <svg class="chevron ml-auto w-4 h-4 transform transition-transform {{ $isMarketingOpen ? 'rotate-90' : '' }}" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <ul id="marketing-submenu" class="ml-4 mt-2 space-y-1.5 border-l border-white/10 pl-3 {{ $isMarketingOpen ? '' : 'hidden' }}">
                        <li>
                            <a href="{{ route('admin.marketing.subscriptions') }}" 
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.marketing.subscriptions') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Subscriptions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.marketing.campaigns') }}" 
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.marketing.campaigns') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Campaigns
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.marketing.flash-sales') }}" 
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.marketing.flash-sales') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Flash Sales
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @php
                $isSettingsOpen = request()->routeIs('admin.settings*', 'admin.homepage-settings*', 'admin.shipping-methods*');
            @endphp
            <li>
                <a href="{{ route('admin.profile') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.profile') ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A10.97 10.97 0 0012 20c2.755 0 5.26-1.02 7.179-2.696M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Profile
                </a>
            </li>
            @if($adminUser?->hasPermission('settings.manage'))
                <li class="px-2 pt-2">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-500">System</p>
                </li>
                <li>
                    <button 
                        class="flex items-center gap-3 text-sm px-3 py-2.5 rounded-xl w-full font-medium transition {{ $isSettingsOpen ? 'bg-white text-slate-950 shadow-lg shadow-black/20 ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}" 
                        onclick="document.getElementById('settings-submenu').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-90')"
                    >
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <span class="flex-1 text-left">Settings</span>
                        <svg class="chevron ml-auto w-4 h-4 transform transition-transform {{ $isSettingsOpen ? 'rotate-90' : '' }}" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <ul id="settings-submenu" class="ml-4 mt-2 space-y-1.5 border-l border-white/10 pl-3 {{ $isSettingsOpen ? '' : 'hidden' }}">
                        <li>
                            <a href="{{ route('admin.settings') }}" 
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings*') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Shop Settings
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.homepage.settings') }}" 
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.homepage.settings*') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Homepage Settings
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.shipping.methods') }}" 
                               class="block text-sm px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.shipping.methods*') ? 'bg-white text-slate-950 shadow-md ring-1 ring-white/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                Shipping Methods
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <li>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-rose-500/10 hover:text-rose-200">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                        Sign out
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>
