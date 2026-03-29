<div class="bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen font-sans rounded-xl">
    @php
        $fieldClass = 'mt-1 w-full h-11 rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary/10';
        $selectClass = 'mt-1 w-full h-11 appearance-none rounded-xl border border-gray-300 bg-white px-4 pr-10 text-sm text-gray-900 shadow-sm transition focus:border-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary/10';
        $disabledFieldClass = 'mt-1 w-full h-11 rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm text-gray-500 shadow-sm cursor-not-allowed';
    @endphp
    
    <div class="md:hidden bg-white border-b border-gray-200 p-4 sticky top-0 z-10">
        <h1 class="text-lg font-bold text-gray-900">{{ __('store.my_account') }}</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <div class="flex flex-col lg:flex-row gap-6 xl:gap-8">
            
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                    <div class="h-1 bg-gradient-to-r from-primary via-primary/80 to-primary/60"></div>
                    
                    <div class="p-6 border-b border-gray-100 flex flex-col items-center text-center bg-gradient-to-b from-white to-gray-50">
                        <div class="relative mb-4">
                            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 ring-4 ring-white shadow-sm">
                                @if($existingAvatar)
                                    <img src="{{ Storage::url($existingAvatar) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary text-white text-3xl font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <h2 class="font-bold text-gray-900 truncate w-full">{{ auth()->user()->name }}</h2>
                        <p class="text-xs text-gray-500 truncate w-full mb-2">{{ auth()->user()->email }}</p>
                        <div class="mb-2 inline-flex items-center rounded-full px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em]
                            {{ $user->hasVerifiedEmail() ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $user->hasVerifiedEmail() ? __('store.verified') : __('store.unverified') }}
                        </div>
                        @if(! $user->hasVerifiedEmail())
                            <button type="button"
                                    wire:click="sendVerificationEmail"
                                    wire:loading.attr="disabled"
                                    wire:target="sendVerificationEmail"
                                    class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1 text-[11px] font-semibold text-amber-700 transition hover:border-amber-300 hover:bg-amber-50 disabled:cursor-wait disabled:opacity-80">
                                <svg wire:loading.remove wire:target="sendVerificationEmail" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-18 0a2 2 0 012-2h16a2 2 0 012 2m-20 0v8a2 2 0 002 2h16a2 2 0 002-2V8"></path>
                                </svg>
                                <svg wire:loading wire:target="sendVerificationEmail" class="h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="sendVerificationEmail">{{ __('store.send_verification_link') }}</span>
                                <span wire:loading wire:target="sendVerificationEmail">{{ __('store.loading') }}</span>
                            </button>
                        @endif
                        <div class="mt-2 inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-[11px] font-semibold text-primary">
                            {{ __('store.member_since') }} {{ auth()->user()->created_at?->format('M Y') }}
                        </div>
                    </div>

                    <nav class="p-2 space-y-1">
                        @foreach([
                            'overview' => ['label' => __('store.overview'), 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                            'orders' => ['label' => __('store.my_orders'), 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                            'addresses' => ['label' => __('store.address_book'), 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                            'profile' => ['label' => __('store.profile_settings'), 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                        ] as $key => $item)
                            <button wire:click="switchTab('{{ $key }}')"
                                    class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm font-medium border
                                    {{ $activeTab === $key 
                                        ? 'bg-primary text-white shadow-sm border-primary/10' 
                                        : 'text-gray-600 border-transparent hover:bg-gray-50 hover:text-primary hover:border-gray-200' }}">
                                <svg class="w-5 h-5 shrink-0 {{ $activeTab === $key ? 'text-white' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                                </svg>
                                <span>{{ $item['label'] }}</span>
                            </button>
                        @endforeach
                        
                        <form method="POST" action="{{ route('logout') }}" class="mt-2 pt-2 border-t border-gray-100">
                            @csrf
                            <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition-colors border border-transparent hover:border-red-100">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span>{{ __('store.logout') }}</span>
                            </button>
                        </form>
                    </nav>
                </div>
            </aside>

            <main class="flex-1 min-w-0">
                @if(session('message'))
                    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 shadow-sm">
                        {{ session('message') }}
                    </div>
                @endif

                @if(! $user->hasVerifiedEmail())
                    <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/90 p-4 md:p-5 shadow-sm">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-600">{{ __('store.email_verification') }}</p>
                                <p class="mt-1 text-sm text-amber-900">{{ __('store.verification_sent') }}</p>
                            </div>
                            <button type="button"
                                    wire:click="sendVerificationEmail"
                                    wire:loading.attr="disabled"
                                    wire:target="sendVerificationEmail"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-amber-700 disabled:cursor-wait disabled:opacity-80">
                                <svg wire:loading.remove wire:target="sendVerificationEmail" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-18 0a2 2 0 012-2h16a2 2 0 012 2m-20 0v8a2 2 0 002 2h16a2 2 0 002-2V8"></path>
                                </svg>
                                <svg wire:loading wire:target="sendVerificationEmail" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="sendVerificationEmail">{{ __('store.send_verification_link') }}</span>
                                <span wire:loading wire:target="sendVerificationEmail">{{ __('store.loading') }}</span>
                            </button>
                        </div>
                    </div>
                @endif
                
                @if($activeTab === 'overview')
                    <div class="space-y-6 animate-fade-in">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                                <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('store.total_orders') }}</p>
                                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</div>
                                <p class="text-xs text-gray-400 mt-1">{{ __('store.all_time_orders') }}</p>
                            </div>
                            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                                <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('store.total_spend') }}</p>
                                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $currencyCode }} {{ number_format($stats['total_spend'], 2) }}</div>
                                <p class="text-xs text-gray-400 mt-1">{{ __('store.across_all_orders') }}</p>
                            </div>
                            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                                <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('store.last_order') }}</p>
                                <div class="mt-2 text-sm font-semibold text-gray-900">
                                    @if($stats['last_order'])
                                        #{{ $stats['last_order']->order_number }}
                                    @else
                                        {{ __('store.no_orders_yet') }}
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $stats['last_order']?->created_at?->format('M d, Y') ?? '—' }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-bold text-gray-900">{{ __('store.overview') }}</h3>
                                <button wire:click="switchTab('profile')" class="text-xs font-semibold text-primary hover:underline">{{ __('store.profile_settings') }}</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('store.name') }}</p>
                                    <p class="font-semibold text-gray-900 mt-1">{{ $user->name }}</p>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('store.email') }}</p>
                                    <p class="text-gray-700 mt-1">{{ $user->email }}</p>
                                    <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em]
                                        {{ $user->hasVerifiedEmail() ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $user->hasVerifiedEmail() ? __('store.verified') : __('store.unverified') }}
                                    </span>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('store.phone') }}</p>
                                    <p class="text-gray-700 mt-1">{{ $user->phone ?? '—' }}</p>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('store.saved_addresses') }}</p>
                                    <p class="font-semibold text-gray-900 mt-1">{{ $stats['address_count'] }}</p>
                                </div>
                                <div class="md:col-span-4 rounded-xl border border-gray-200 bg-gray-50/80 p-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('store.default_address') }}</p>
                                    <p class="text-gray-700 mt-1">
                                        {{ $user->defaultAddress?->address_line1 ?? __('store.default_address') }}
                                        @if($user->defaultAddress?->city)
                                            , {{ $user->defaultAddress?->city }}
                                        @endif
                                        @if($user->defaultAddress?->state)
                                            , {{ $user->defaultAddress?->state }}
                                        @endif
                                        @if($user->defaultAddress?->postal_code)
                                            , {{ $user->defaultAddress?->postal_code }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between gap-4 mb-5">
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ __('store.my_orders') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ __('store.my_orders') }}</p>
                                </div>
                                <button wire:click="switchTab('orders')" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:border-primary/30 hover:text-primary transition">
                                    {{ __('store.view_all_orders') }}
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                                <button wire:click="switchOrderTab('to_pay')" class="group rounded-2xl border border-gray-200 bg-gray-50/80 p-4 text-left transition hover:-translate-y-0.5 hover:border-primary/30 hover:bg-white hover:shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white border border-gray-200 shadow-sm">
                                            <svg class="h-5 w-5 text-gray-500 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        </div>
                                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-red-600">
                                            {{ $stats['pending_payment'] }}
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm font-bold text-gray-900">{{ __('store.to_pay') }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ __('store.to_pay') }}</p>
                                    </div>
                                </button>

                                <button wire:click="switchOrderTab('to_ship')" class="group rounded-2xl border border-gray-200 bg-gray-50/80 p-4 text-left transition hover:-translate-y-0.5 hover:border-primary/30 hover:bg-white hover:shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white border border-gray-200 shadow-sm">
                                            <svg class="h-5 w-5 text-gray-500 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        </div>
                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-blue-600">
                                            {{ $stats['to_ship'] }}
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm font-bold text-gray-900">{{ __('store.to_ship') }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ __('store.to_ship') }}</p>
                                    </div>
                                </button>

                                <button wire:click="switchOrderTab('to_receive')" class="group rounded-2xl border border-gray-200 bg-gray-50/80 p-4 text-left transition hover:-translate-y-0.5 hover:border-primary/30 hover:bg-white hover:shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white border border-gray-200 shadow-sm">
                                            <svg class="h-5 w-5 text-gray-500 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                        </div>
                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-amber-600">
                                            {{ $stats['to_receive'] }}
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm font-bold text-gray-900">{{ __('store.to_receive') }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ __('store.on_the_way') }}</p>
                                    </div>
                                </button>

                                <button wire:click="switchOrderTab('completed')" class="group rounded-2xl border border-gray-200 bg-gray-50/80 p-4 text-left transition hover:-translate-y-0.5 hover:border-primary/30 hover:bg-white hover:shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white border border-gray-200 shadow-sm">
                                            <svg class="h-5 w-5 text-gray-500 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-green-600">
                                            {{ $stats['completed'] }}
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm font-bold text-gray-900">{{ __('store.completed') }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ __('store.delivered_orders_only') }}</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="font-bold text-gray-900">{{ __('store.recent_orders') }}</h3>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @forelse($recentOrders as $order)
                                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">Order #{{ $order->order_number }}</p>
                                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @php
                                                $orderSymbol = $order->currency?->symbol ?: ($order->currency_code ? $order->currency_code . ' ' : $currencySymbol);
                                            @endphp
                                            <p class="text-sm font-bold text-primary">{{ $orderSymbol }}{{ number_format($order->total, 2) }}</p>
                                            <p class="text-xs font-medium capitalize {{ $order->status == 'delivered' ? 'text-green-600' : ($order->status == 'cancelled' ? 'text-red-500' : 'text-orange-500') }}">{{ __('store.' . $order->status) }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-500 text-sm">{{ __('store.no_recent_activity') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'orders')
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px]">
                        <div class="p-6 border-b border-gray-100 bg-gradient-to-b from-white to-gray-50/60">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-primary">{{ __('store.order_history') }}</p>
                                    <h3 class="mt-2 text-xl font-bold text-gray-900">{{ __('store.my_orders') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ __('store.track_orders_intro') }}</p>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.all') }}</p>
                                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $orderTabCounts['all'] }}</p>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.active') }}</p>
                                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $orderTabCounts['to_pay'] + $orderTabCounts['to_ship'] + $orderTabCounts['to_receive'] }}</p>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.delivered_short') }}</p>
                                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $orderTabCounts['completed'] }}</p>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.cancelled_short') }}</p>
                                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $orderTabCounts['cancelled'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex overflow-x-auto border-b border-gray-100 bg-white no-scrollbar px-2 pt-2">
                            @foreach([
                                'all' => __('store.all'),
                                'to_pay' => __('store.to_pay'),
                                'to_ship' => __('store.to_ship'),
                                'to_receive' => __('store.to_receive'),
                                'completed' => __('store.completed'),
                                'cancelled' => __('store.cancelled'),
                            ] as $key => $label)
                                <button wire:click="switchOrderTab('{{ $key }}')"
                                        class="relative flex items-center gap-2 px-5 py-4 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors
                                        {{ $activeOrderTab === $key ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                                    <span>{{ $label }}</span>
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">
                                        {{ $orderTabCounts[$key] ?? 0 }}
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <div class="p-4 md:p-6">
                            @forelse($orders as $order)
                                <div class="mb-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:border-primary/30 hover:shadow-md">
                                    <div class="border-b border-gray-100 bg-gray-50/60 px-5 py-4">
                                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white border border-gray-200 shadow-sm">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-gray-900">#{{ $order->order_number }}</span>
                                                        <span class="text-xs text-gray-400">•</span>
                                                        <span class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                                                    </div>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        {{ __('store.items_in_this_order', ['count' => $order->items->count()]) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-[0.18em]
                                                    {{ $order->status == 'delivered' ? 'bg-green-100 text-green-700' : 
                                                      ($order->status == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                                                    {{ __('store.' . $order->status) }}
                                                </span>
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-[0.18em] bg-gray-100 text-gray-600">
                                                    {{ $order->payment_status ? __('store.' . $order->payment_status) : __('store.pending') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-5 py-4">
                                        <div class="text-xs text-gray-500 mb-4">
                                            {{ __('store.payment_method') }}: <span class="font-semibold text-gray-700">{{ $order->payment_method ?? '—' }}</span>
                                        </div>

                                        <div class="space-y-3">
                                            @foreach($order->items->take(2) as $item)
                                                <div class="flex items-center gap-4 rounded-xl border border-gray-100 bg-gray-50/60 p-3">
                                                    <div class="w-14 h-14 bg-gray-100 rounded-xl border border-gray-200 overflow-hidden flex-shrink-0">
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $item->name }}</p>
                                                        <p class="text-xs text-gray-500">x{{ $item->quantity }}</p>
                                                    </div>
                                                    <div class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                                                        @php
                                                            $itemSymbol = $order->currency?->symbol ?: ($order->currency_code ? $order->currency_code . ' ' : $currencySymbol);
                                                        @endphp
                                                        {{ $itemSymbol }}{{ number_format($item->price, 2) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($order->items->count() > 2)
                                            <p class="mt-3 text-xs text-gray-500">{{ __('store.more_items', ['count' => $order->items->count() - 2]) }}</p>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-3 border-t border-dashed border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="text-sm">
                                            @php
                                                $totalSymbol = $order->currency?->symbol ?: ($order->currency_code ? $order->currency_code . ' ' : $currencySymbol);
                                            @endphp
                                            <span class="text-gray-500">{{ __('store.total') }}</span>
                                            <span class="ml-2 text-xl font-bold text-primary">{{ $totalSymbol }}{{ number_format($order->total, 2) }}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @if($order->status == 'pending')
                                                <button class="rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition hover:bg-primary/90">{{ __('store.pay_now') }}</button>
                                            @endif
                                            <button wire:click="viewOrder({{ $order->id }})" class="rounded-xl border border-gray-300 px-4 py-2 text-xs font-bold text-gray-700 transition hover:border-primary/30 hover:text-primary hover:bg-gray-50">{{ __('store.view_details') }}</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center py-20">
                                    <div class="bg-gray-50 p-4 rounded-full mb-4">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">{{ __('store.no_orders_found') }}</p>
                                </div>
                            @endforelse
                        </div>
                        
                        @if(method_exists($orders, 'links'))
                            <div class="p-4 border-t border-gray-100 bg-white">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </div>
                @endif

                @if($activeTab === 'addresses')
                    <div class="space-y-6 animate-fade-in">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-gray-900 text-lg">{{ __('store.saved_addresses') }}</h3>
                            <button wire:click="toggleAddressForm" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-primary transition">
                                {{ $showAddressForm ? __('store.close') : __('store.add_new') }}
                            </button>
                        </div>
                        @if(session('address_success'))
                            <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm">{{ session('address_success') }}</div>
                        @endif
                        @if($showAddressForm)
                            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 md:p-6">
                                <h4 class="text-sm font-bold text-gray-900 mb-4">{{ __('store.new_address') }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600">{{ __('store.address_type') }} <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <select wire:model="newAddress.type" class="{{ $selectClass }}">
                                                @foreach($addressTypeOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('newAddress.type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600">{{ __('store.full_name') }}</label>
                                        <input wire:model="newAddress.name" type="text" class="{{ $fieldClass }}">
                                        @error('newAddress.name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600">{{ __('store.phone') }}</label>
                                        <input wire:model="newAddress.phone" type="text" class="{{ $fieldClass }}">
                                        @error('newAddress.phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600">{{ __('store.city') }}</label>
                                        <input wire:model="newAddress.city" type="text" class="{{ $fieldClass }}">
                                        @error('newAddress.city') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600">{{ __('store.address_line_1') }}</label>
                                        <input wire:model="newAddress.address_line1" type="text" class="{{ $fieldClass }}">
                                        @error('newAddress.address_line1') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600">{{ __('store.address_line_2') }}</label>
                                        <input wire:model="newAddress.address_line2" type="text" class="{{ $fieldClass }}">
                                        @error('newAddress.address_line2') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600">{{ __('store.state') }}</label>
                                        <input wire:model="newAddress.state" type="text" class="{{ $fieldClass }}">
                                        @error('newAddress.state') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600">{{ __('store.postal_code') }}</label>
                                        <input wire:model="newAddress.postal_code" type="text" class="{{ $fieldClass }}">
                                        @error('newAddress.postal_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600">{{ __('store.country') }}</label>
                                        <div class="relative">
                                            <select wire:model="newAddress.country" class="{{ $selectClass }}">
                                                @foreach($supportedCountries as $country)
                                                    <option value="{{ $country['code'] }}">{{ $country['name'] }}</option>
                                                @endforeach
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('newAddress.country') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex items-center gap-2 mt-2 md:pt-4">
                                        <input wire:model="newAddress.is_default" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="text-xs text-gray-600">{{ __('store.set_as_default') }}</span>
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button wire:click="addAddress" class="rounded-xl bg-primary px-4 py-2 text-sm font-bold text-white transition hover:bg-primary/90">{{ __('store.save_address') }}</button>
                                </div>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($addresses as $addr)
                                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm relative group hover:border-primary/40 transition-colors">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-2 py-1 rounded">{{ $addr->type ?? 'Address' }}</span>
                                        <button wire:click="deleteAddress({{ $addr->id }})" wire:confirm="Delete?" class="text-gray-400 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </div>
                                    <p class="font-bold text-gray-900">{{ $addr->name }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $addr->address_line1 }}</p>
                                    @if($addr->address_line2)
                                        <p class="text-sm text-gray-600">{{ $addr->address_line2 }}</p>
                                    @endif
                                    <p class="text-sm text-gray-600">{{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}</p>
                                    @if($addr->country_label)
                                        <p class="text-sm text-gray-600">{{ $addr->country_label }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="col-span-2 text-center py-10 bg-white rounded-2xl border border-dashed border-gray-300"><p class="text-gray-500">{{ __('store.no_addresses_saved') }}</p></div>
                            @endforelse
                        </div>
                        @if(method_exists($addresses, 'links'))
                            <div class="pt-2">
                                {{ $addresses->links() }}
                            </div>
                        @endif
                    </div>
                @endif

                @if($activeTab === 'profile')
                    <div class="grid grid-cols-1 xl:grid-cols-[320px_minmax(0,1fr)] gap-6 animate-fade-in">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="h-1 bg-gradient-to-r from-primary via-primary/80 to-primary/60"></div>
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <div class="relative shrink-0">
                                        <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 ring-4 ring-gray-50 shadow-sm">
                                            @if($avatar)
                                                <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                                            @elseif($existingAvatar)
                                                <img src="{{ Storage::url($existingAvatar) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/15 to-primary/5 text-primary">
                                                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <label class="absolute -right-1 -bottom-1 rounded-full bg-white p-2 shadow-md border border-gray-200 cursor-pointer text-gray-500 hover:text-primary transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <input type="file" wire:model="avatar" class="hidden" accept="image/*">
                                        </label>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-gray-900 truncate">{{ auth()->user()->name }}</h4>
                                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                        <p class="text-[11px] text-gray-400 mt-1">{{ __('store.upload_clear_photo') }}</p>
                                    </div>
                                </div>

                                @error('avatar') <span class="mt-3 block text-red-500 text-xs">{{ $message }}</span> @enderror

                                <div class="mt-6 grid grid-cols-2 gap-3">
                                    <div class="rounded-xl bg-gray-50 border border-gray-200 p-3">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.orders') }}</p>
                                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                                    </div>
                                    <div class="rounded-xl bg-gray-50 border border-gray-200 p-3">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.addresses') }}</p>
                                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $stats['address_count'] }}</p>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-3 rounded-2xl border border-gray-200 bg-gray-50/70 p-4 text-sm">
                                    <div>
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.phone') }}</p>
                                        <p class="mt-1 font-medium text-gray-900">{{ $user->phone ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.member_since') }}</p>
                                        <p class="mt-1 font-medium text-gray-900">{{ $user->created_at?->format('M Y') }}</p>
                                    </div>
                                </div>

                                <div class="mt-6 flex gap-3">
                                    <button type="button" wire:click="switchTab('overview')" class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:border-primary/30 hover:text-primary transition">{{ __('store.overview') }}</button>
                                    <button type="button" wire:click="switchTab('addresses')" class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:border-primary/30 hover:text-primary transition">{{ __('store.addresses') }}</button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
                            <div class="flex items-center justify-between gap-4 pb-5 border-b border-gray-100">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-lg">{{ __('store.my_profile') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ __('store.update_contact_details') }}</p>
                                </div>
                                @if (session()->has('profile_success'))
                                    <div class="hidden md:block bg-green-50 text-green-700 px-3 py-2 rounded-xl text-sm">{{ session('profile_success') }}</div>
                                @endif
                            </div>

                            @if (session()->has('profile_success'))
                                <div class="md:hidden mt-4 bg-green-50 text-green-700 p-3 rounded-xl text-sm">{{ session('profile_success') }}</div>
                            @endif

                            <form wire:submit="updateProfile" class="mt-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.full_name') }}</label>
                                        <input wire:model="name" type="text" class="{{ $fieldClass }}">
                                        @error('name') <span class="mt-1 block text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.email') }}</label>
                                        <input value="{{ $email }}" type="email" disabled class="{{ $disabledFieldClass }}">
                                        <p class="mt-1 text-[11px] text-gray-400">{{ __('store.email_changes_not_supported_yet') }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.phone_number') }}</label>
                                        <input wire:model="phone" type="text" class="{{ $fieldClass }}">
                                        @error('phone') <span class="mt-1 block text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.gender') }}</label>
                                        <div class="relative">
                                            <select wire:model="gender" class="{{ $selectClass }}">
                                                <option value="">{{ __('store.select_gender') }}</option>
                                                <option value="1">{{ __('store.male') }}</option>
                                                <option value="2">{{ __('store.female') }}</option>
                                                <option value="3">{{ __('store.other') }}</option>
                                            </select>
                                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('store.date_of_birth') }}</label>
                                        <input wire:model="birthday" type="date" class="{{ $fieldClass }}">
                                    </div>
                                </div>

                                <div class="flex justify-end pt-4 border-t border-gray-100">
                                    <button type="submit" class="rounded-xl bg-primary px-8 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                                        {{ __('store.save_changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

            </main>
        </div>
    </div>

    @if($showOrderModal && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="$wire.set('showOrderModal', false)">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-bold text-gray-900">Order #{{ $selectedOrder->order_number }}</h3>
                    <button wire:click="$set('showOrderModal', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-gray-600">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="font-semibold text-gray-700">{{ __('store.status') }}</div>
                            <div class="mt-1 capitalize">{{ __('store.' . $selectedOrder->status) }}</div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="font-semibold text-gray-700">{{ __('store.payment') }}</div>
                            <div class="mt-1 capitalize">{{ __('store.' . $selectedOrder->payment_status) }}</div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="font-semibold text-gray-700">{{ __('store.placed') }}</div>
                            <div class="mt-1">{{ $selectedOrder->created_at?->format('M d, Y') }}</div>
                        </div>
                    </div>
                    @php
                        $modalSymbol = $selectedOrder->currency?->symbol ?: ($selectedOrder->currency_code ? $selectedOrder->currency_code . ' ' : $currencySymbol);
                    @endphp
                    <div class="space-y-4">
                        @foreach($selectedOrder->items as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg mr-4 overflow-hidden border border-gray-200"></div>
                                    <div><p class="text-sm font-bold text-gray-900">{{ $item->name }}</p><p class="text-xs text-gray-500">{{ __('store.qty') }}: {{ $item->quantity }}</p></div>
                                </div>
                                <p class="text-sm font-bold text-gray-900">{{ $modalSymbol }}{{ number_format($item->price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-100 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600"><span>{{ __('store.subtotal') }}</span><span>{{ $modalSymbol }}{{ number_format($selectedOrder->subtotal, 2) }}</span></div>
                        <div class="flex justify-between text-sm text-gray-600"><span>{{ __('store.shipping') }}</span><span>{{ $modalSymbol }}{{ number_format($selectedOrder->shipping_cost, 2) }}</span></div>
                        <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-dashed border-gray-200"><span>{{ __('store.total') }}</span><span class="text-primary">{{ $modalSymbol }}{{ number_format($selectedOrder->total, 2) }}</span></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-600">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="font-semibold text-gray-700">{{ __('store.shipping_address') }}</div>
                            <div class="mt-1">
                                {{ $selectedOrder->shippingAddress?->name ?? '—' }}<br>
                                {{ $selectedOrder->shippingAddress?->address_line1 ?? '' }}<br>
                                {{ $selectedOrder->shippingAddress?->city ?? '' }} {{ $selectedOrder->shippingAddress?->state ?? '' }} {{ $selectedOrder->shippingAddress?->postal_code ?? '' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="font-semibold text-gray-700">{{ __('store.billing_address') }}</div>
                            <div class="mt-1">
                                {{ $selectedOrder->billingAddress?->name ?? '—' }}<br>
                                {{ $selectedOrder->billingAddress?->address_line1 ?? '' }}<br>
                                {{ $selectedOrder->billingAddress?->city ?? '' }} {{ $selectedOrder->billingAddress?->state ?? '' }} {{ $selectedOrder->billingAddress?->postal_code ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
