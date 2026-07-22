<div class="bg-gray-50 min-h-screen py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        @include('admin.includes.errors')

        @if(count($cart) > 0)
            <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('store.shopping_cart_checkout') }}</h1>
            <div class="lg:grid lg:grid-cols-12 lg:gap-8 lg:items-start">

                <div class="lg:col-span-8 space-y-6">

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                                    {{ __('store.items_in_cart') }}</h2>
                            </div>
                            <span
                                class="text-xs font-bold bg-white px-2 py-1 rounded-full border border-gray-200 text-gray-600 shadow-sm">{{ __('store.items_count', ['count' => count($cart)]) }}</span>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @foreach($cart as $key => $item)
                                <li class="p-5 flex items-center gap-x-6 group hover:bg-gray-50/50 transition-colors">
                                    <div
                                        class="relative flex-shrink-0 w-20 h-20 border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm group-hover:shadow-md transition-shadow mr-4">
                                        @php
                                            $img = !empty($item['thumbnail'])
                                                ? (Str::startsWith($item['thumbnail'], ['http']) ? $item['thumbnail'] : Storage::url($item['thumbnail']))
                                                : 'https://placehold.co/100';
                                        @endphp
                                        <img src="{{ $img }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <div class="max-w-[70%]">
                                                <h3
                                                    class="text-sm font-bold text-gray-900 line-clamp-2 hover:text-primary transition-colors cursor-pointer">
                                                    {{ $item['name'] }}</h3>
                                                @if(!empty($item['attributes']))
                                                    <div class="text-[10px] text-gray-500 mt-1.5 flex flex-wrap gap-1.5">
                                                        @foreach($item['attributes'] as $k => $v)
                                                            <span
                                                                class="bg-gray-100 px-2 py-0.5 rounded-md font-medium border border-gray-200/50">{{ $k }}:
                                                                {{ $v }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-black text-gray-900">
                                                    {{ $item['currency_symbol'] ?? $currencySymbol }}{{ number_format($item['price'], 2) }}
                                                </p>
                                                <p class="text-[10px] text-gray-400 mt-0.5">{{ __('store.per_unit') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center mt-3">
                                            <div
                                                class="flex items-center bg-gray-100/80 rounded-lg p-0.5 border border-gray-200 shadow-inner">
                                                <button wire:click="decrement('{{ $key }}')"
                                                    class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-primary hover:bg-white rounded-md transition-all text-sm font-bold shadow-sm">-</button>
                                                <input type="text" value="{{ $item['quantity'] }}" readonly
                                                    class="w-9 text-center border-none bg-transparent p-0 text-gray-900 font-black text-xs focus:ring-0">
                                                <button wire:click="increment('{{ $key }}')"
                                                    class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-primary hover:bg-white rounded-md transition-all text-sm font-bold shadow-sm">+</button>
                                            </div>
                                            <button wire:click="removeItem('{{ $key }}')"
                                                class="flex items-center gap-1.5 text-[11px] font-bold text-red-400 hover:text-red-600 transition-colors uppercase tracking-tight">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6">
                                                    </path>
                                                </svg>
                                                {{ __('store.remove') }}
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-2">
                            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                                {{ __('store.customer_details') }}</h2>
                            @if(auth()->check())
                                <span
                                    class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('store.logged_in') }}
                                </span>
                            @endif
                        </div>

                        <div class="mb-6 space-y-4">
                            @if(!auth()->check() && $authSettings['guest_checkout_enabled'])
                                <div class="rounded-lg border border-primary/20 bg-primary/10 p-4">
                                    <p class="text-sm font-semibold text-primary">{{ __('store.guest_checkout_intro') }}</p>
                                    <p class="text-xs text-primary mt-1">{{ __('store.guest_checkout_no_login_required') }}</p>
                                    @if($authSettings['email_password_enabled'] || $authSettings['email_otp_enabled'])
                                        <p class="text-xs text-primary mt-2">{{ __('store.guest_checkout_faster_checkout') }}</p>
                                        <button wire:click="toggleAuthSection" type="button"
                                            class="mt-3 inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-primary border border-primary/20 hover:bg-primary/10">
                                            {{ $showAuthSection ? __('store.hide_login_register') : __('store.show_login_register') }}
                                        </button>
                                    @endif
                                </div>
                            @endif

                            @if(
                                    !auth()->check()
                                    && ($authSettings['email_password_enabled'] || $authSettings['email_otp_enabled'])
                                    && (!$authSettings['guest_checkout_enabled'] || $showAuthSection)
                                )
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $authSettings['guest_checkout_enabled'] ? __('store.optional_account_access') : __('store.login_required') }}
                                        </p>
                                        @if($authSettings['guest_checkout_enabled'])
                                            <button wire:click="toggleAuthSection" type="button"
                                                class="text-[11px] px-2 py-1 rounded bg-primary/10 text-primary font-semibold hover:bg-primary/20">{{ __('store.hide') }}</button>
                                        @endif
                                    </div>

                                    @if(!$authSettings['guest_checkout_enabled'])
                                        <p class="text-xs text-gray-600">{{ __('store.login_or_register_to_place_order') }}</p>
                                    @endif

                                    @if(session('auth_success'))
                                        <p class="text-xs text-green-700 font-semibold">{{ session('auth_success') }}</p>
                                    @endif

                                    @if($authSettings['email_password_enabled'])
                                        <div class="grid grid-cols-2 gap-2 rounded-lg bg-gray-100 p-1">
                                            <button type="button" wire:click="setAuthPanel('login')"
                                                class="py-2 text-xs rounded-md font-semibold {{ $authPanel === 'login' ? 'bg-primary text-white shadow-sm' : 'text-gray-700' }}">
                                                {{ __('store.login') }}
                                            </button>
                                            <button type="button" wire:click="setAuthPanel('register')"
                                                class="py-2 text-xs rounded-md font-semibold {{ $authPanel === 'register' ? 'bg-primary text-white shadow-sm' : 'text-gray-700' }}">
                                                {{ __('store.register') }}
                                            </button>
                                        </div>
                                    @endif

                                    @if($authPanel === 'login')
                                        @if($authSettings['email_password_enabled'] && $authSettings['email_otp_enabled'])
                                            <div class="grid grid-cols-2 gap-2 rounded-lg bg-white p-1 border border-gray-200">
                                                <button type="button" wire:click="setAuthMethod('password')"
                                                    class="py-2 text-xs rounded-md font-semibold {{ $authMethod === 'password' ? 'bg-primary text-white' : 'text-gray-700' }}">
                                                    {{ __('store.email_password') }}
                                                </button>
                                                <button type="button" wire:click="setAuthMethod('otp')"
                                                    class="py-2 text-xs rounded-md font-semibold {{ $authMethod === 'otp' ? 'bg-primary text-white' : 'text-gray-700' }}">
                                                    {{ __('store.email_otp') }}
                                                </button>
                                            </div>
                                        @endif

                                        <input wire:model="loginEmail" type="email"
                                            class="w-full text-sm border border-gray-300 bg-white rounded-lg shadow-sm py-2 px-3"
                                            placeholder="{{ __('store.email_for_login') }}">
                                        @error('loginEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                                        @if($authMethod === 'password' && $authSettings['email_password_enabled'])
                                            <div class="space-y-2">
                                                <input wire:model="loginPassword" type="password"
                                                    class="w-full text-sm border border-gray-300 bg-white rounded-lg shadow-sm py-2 px-3"
                                                    placeholder="{{ __('store.password') }}">
                                                @error('loginPassword') <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror

                                                <div class="flex flex-col items-start gap-4 pt-2">
                                                    <label class="inline-flex items-center text-xs text-gray-700">
                                                        <input wire:model="loginRemember" type="checkbox"
                                                            class="rounded border-gray-300 text-primary focus:ring-primary mr-2">
                                                        {{ __('store.remember_me') }}
                                                    </label>
                                                    <button wire:click="loginWithPasswordInline" type="button"
                                                        class="bg-primary text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-primary transition">
                                                        {{ __('store.login') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        @if($authMethod === 'otp' && $authSettings['email_otp_enabled'])
                                            <div class="space-y-2">
                                                @if(!$loginOtpSent)
                                                    <div class="pt-2">
                                                        <button wire:click="sendLoginOtp" type="button"
                                                            class="bg-primary text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-primary transition">
                                                            {{ __('store.send_otp') }}
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="flex gap-2">
                                                        <input wire:model="loginOtp" type="text" maxlength="6"
                                                            class="w-full text-sm border border-gray-300 bg-white rounded-lg shadow-sm py-2 px-3 tracking-widest text-center font-mono"
                                                            placeholder="123456">
                                                        <button wire:click="verifyLoginOtp" type="button"
                                                            class="bg-primary text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-primary transition">
                                                            {{ __('store.verify') }}
                                                        </button>
                                                    </div>
                                                    @if(session('otp_message'))
                                                    <p class="text-xs text-primary">{{ session('otp_message') }}</p> @endif
                                                    @error('loginOtp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <div class="space-y-2">
                                            <input wire:model="registerName" type="text"
                                                class="w-full text-sm border border-gray-300 bg-white rounded-lg shadow-sm py-2 px-3"
                                                placeholder="{{ __('store.full_name') }}">
                                            @error('registerName') <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror

                                            <input wire:model="registerEmail" type="email"
                                                class="w-full text-sm border border-gray-300 bg-white rounded-lg shadow-sm py-2 px-3"
                                                placeholder="{{ __('store.email') }}">
                                            @error('registerEmail') <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror

                                            <input wire:model="registerPassword" type="password"
                                                class="w-full text-sm border border-gray-300 bg-white rounded-lg shadow-sm py-2 px-3"
                                                placeholder="{{ __('store.create_password') }}">
                                            @error('registerPassword') <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror

                                            <div class="pt-2">
                                                <button wire:click="registerInline" type="button"
                                                    class="bg-primary text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-primary transition">
                                                    {{ __('store.create_account_continue') }}
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if(auth()->check() || $authSettings['guest_checkout_enabled'])
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.email') }} <span
                                                class="text-red-500">*</span></label>
                                        <input wire:model="email" wire:change="saveGuestDraft" type="email" {{ auth()->check() ? 'disabled' : '' }}
                                            class="w-full text-sm border border-gray-300 {{ auth()->check() ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white' }} focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3 placeholder-gray-400"
                                            placeholder="you@example.com">
                                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @elseif(!auth()->check())
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <p class="text-xs text-gray-600">{{ __('store.login_or_register_to_place_order') }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4 pt-4 border-t border-gray-100 animate-fade-in">
                            @if(auth()->check() && $savedAddresses && $savedAddresses->count() > 0)
                                <div class="mb-5">
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                        {{ __('store.select_address') }}</h3>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

                                        @foreach($savedAddresses as $addr)
                                            <div wire:click="useSavedAddress({{ $addr->id }})"
                                                class="cursor-pointer relative p-2.5 rounded-lg border transition-all duration-200 group h-full flex flex-col justify-between
                                                                {{ $selectedAddressId == $addr->id ? 'border-primary bg-primary/10 ring-1 ring-primary shadow-sm' : 'border-gray-200 hover:border-primary/50 hover:bg-gray-50' }}">

                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span
                                                        class="text-[10px] font-bold text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded uppercase tracking-wider">{{ $addr->type ?? 'Home' }}</span>
                                                    @if($selectedAddressId == $addr->id)
                                                        <svg class="w-3.5 h-3.5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    @endif
                                                </div>

                                                <div class="text-xs text-gray-600 leading-snug">
                                                    <p class="font-bold text-gray-900 truncate mb-0.5">{{ $addr->name }}</p>
                                                    <p class="truncate text-gray-500">{{ $addr->address_line1 }}</p>
                                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $addr->city }},
                                                        {{ $addr->country_label }}</p>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div wire:click="clearAddressSelection"
                                            class="cursor-pointer relative p-2.5 rounded-lg border border-dashed border-gray-300 hover:border-primary hover:bg-gray-50 transition-all flex flex-col items-center justify-center text-center h-full min-h-[85px]
                                                        {{ $selectedAddressId === 'new' ? 'border-primary bg-primary/10 ring-1 ring-primary' : '' }}">
                                            <div
                                                class="bg-gray-100 rounded-full p-1.5 mb-1 group-hover:bg-white transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 group-hover:text-primary" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </div>
                                            <span class="text-xs font-bold text-gray-600 group-hover:text-primary">New
                                                {{ __('store.address') }}</span>
                                        </div>

                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">
                                        {{ __('store.country') }} <span class="text-red-500">*</span>
                                    </label>

                                    <div class="relative">
                                        <select wire:model.live="billing.country_code" wire:change="saveGuestDraft"
                                            class="appearance-none w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 pl-3 pr-10">
                                            @foreach($supportedCountries as $country)
                                                <option value="{{ $country['code'] }}">{{ $country['name'] }}</option>
                                            @endforeach
                                        </select>

                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                            </svg>
                                        </div>
                                    </div>

                                    @error('billing.country_code')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.full_name') }}
                                        <span class="text-red-500">*</span></label>
                                    <input wire:model="billing.name" wire:change="saveGuestDraft" type="text"
                                        class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3">
                                    @error('billing.name') <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.phone') }} <span
                                            class="text-gray-400">(Optional)</span></label>
                                    <input wire:model="phone" wire:change="saveGuestDraft" type="text"
                                        class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3 placeholder-gray-400"
                                        placeholder="+123456789">
                                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.address_line_1') }}
                                    <span class="text-red-500">*</span></label>
                                <input wire:model="billing.address_line1" wire:change="saveGuestDraft" type="text"
                                    class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3 placeholder-gray-400"
                                    placeholder="123 Main St, Apt 4B">
                                @error('billing.address_line1') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.city') }} <span
                                            class="text-red-500">*</span></label>
                                    <input wire:model="billing.city" wire:change="saveGuestDraft" type="text"
                                        class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3">
                                    @error('billing.city') <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.state_province') }}</label>
                                    <input wire:model="billing.state" wire:change="saveGuestDraft" type="text"
                                        class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3"
                                        placeholder="NY, CA, etc">
                                    @error('billing.state') <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.zip_postal_code') }}</label>
                                    <input wire:model="billing.postal_code" wire:change="saveGuestDraft" type="text"
                                        class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3">
                                    @error('billing.postal_code') <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="pt-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input wire:model.live="shipToDifferentAddress" wire:change="saveGuestDraft"
                                        type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span
                                        class="ml-2 text-sm text-gray-700 font-medium">{{ __('store.ship_to_different_address') }}</span>
                                </label>
                            </div>

                            @if($shipToDifferentAddress)
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-4 animate-fade-in-down">

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">
                                                {{ __('store.country') }} <span class="text-red-500">*</span>
                                            </label>

                                            <div class="relative">
                                                <select wire:model.live="shipping.country_code" wire:change="saveGuestDraft"
                                                    class="appearance-none w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 pl-3 pr-10">
                                                    @foreach($supportedCountries as $country)
                                                        <option value="{{ $country['code'] }}">{{ $country['name'] }}</option>
                                                    @endforeach
                                                </select>

                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                                    <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                                    </svg>
                                                </div>
                                            </div>

                                            @error('billing.country_code')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.recipient_name') }}</label>
                                            <input wire:model="shipping.name" wire:change="saveGuestDraft" type="text"
                                                class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3">
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.address') }}</label>
                                        <input wire:model="shipping.address_line1" wire:change="saveGuestDraft" type="text"
                                            class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3">
                                    </div>

                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.city') }}</label>
                                            <input wire:model="shipping.city" wire:change="saveGuestDraft" type="text"
                                                class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">State</label>
                                            <input wire:model="shipping.state" wire:change="saveGuestDraft" type="text"
                                                class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-600 mb-1">{{ __('store.zip_code') }}</label>
                                            <input wire:model="shipping.postal_code" wire:change="saveGuestDraft" type="text"
                                                class="w-full text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">
                            {{ __('store.delivery_method') }}</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($shippingMethods as $method)
                                <label class="relative cursor-pointer group">
                                    <input wire:model.live="selectedShippingMethod" type="radio" value="{{ $method->id }}"
                                        class="peer sr-only">

                                    <div
                                        class="p-3 rounded-lg border border-gray-200 hover:border-primary transition-all duration-200 peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:ring-1 peer-checked:ring-primary/20 flex items-center justify-between">

                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900">{{ $method->name }}</span>
                                            <span class="text-xs text-gray-500 flex items-center mt-0.5">
                                                <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $method->estimated_days }} {{ __('store.days') }}
                                            </span>
                                        </div>

                                        <div class="text-right">
                                            <span
                                                class="block text-sm font-bold text-gray-900">{{$currencySymbol}}{{ number_format($method->cost, 2) }}</span>

                                            <div class="hidden peer-checked:block absolute top-0 right-0 -mt-2 -mr-2">
                                                <span class="bg-primary text-white rounded-full p-0.5 shadow-sm block">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                            d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedShippingMethod') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="lg:col-span-4 mt-8 lg:mt-0">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 sticky top-24">
                        <h2 class="text-base font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">
                            {{ __('store.order_summary') }}</h2>

                        <div class="space-y-3 text-sm mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>{{ __('store.subtotal') }}</span>
                                <span
                                    class="font-bold text-gray-900">{{$currencySymbol}}{{ number_format($subtotal, 2) }}</span>
                            </div>
                            @if($appliedCoupon)
                                <div class="flex justify-between text-green-600">
                                    <span class="flex items-center">
                                        {{ __('store.coupon') }} <button wire:click="removeCoupon"
                                            class="ml-1 text-xs text-red-500 hover:underline">(x)</button>
                                    </span>
                                    <span class="font-bold">-{{$currencySymbol}}{{ number_format($discountAmount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-gray-600">
                                <span>{{ __('store.shipping') }}</span>
                                <span
                                    class="font-bold text-gray-900">{{$currencySymbol}}{{ number_format($shippingCost, 2) }}</span>
                            </div>
                            <div class="border-t border-dashed border-gray-300 pt-3 flex justify-between items-end">
                                <span class="text-base font-bold text-gray-900">{{ __('store.total') }}</span>
                                <span
                                    class="text-xl font-extrabold text-primary">{{$currencySymbol}}{{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <div class="mb-6 space-y-3">
                            @if($appliedCoupon)
                                <div class="rounded-xl border border-green-200 bg-green-50 p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-green-800">{{ $appliedCoupon->code }}</p>
                                            <p class="text-xs text-green-700">
                                                @if($appliedCoupon->type === 'free_shipping')
                                                    {{ __('store.free_shipping_applied') }}
                                                @elseif($appliedCoupon->type === 'percentage')
                                                    {{ $appliedCoupon->value }}% {{ __('store.discount_applied') }}
                                                @else
                                                    {{$currencySymbol}}{{ number_format($discountAmount, 2) }}
                                                    {{ __('store.discount_applied') }}
                                                @endif
                                            </p>
                                        </div>
                                        <button wire:click="removeCoupon" type="button"
                                            class="text-xs font-semibold text-red-600 hover:text-red-700">
                                            {{ __('store.remove') }}
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="flex space-x-2">
                                <input wire:model="couponCode" type="text" placeholder="{{ __('store.enter_coupon_code') }}"
                                    class="flex-1 text-sm border border-gray-300 bg-white focus:border-primary focus:ring-primary rounded-lg shadow-sm py-2 px-3 uppercase placeholder-gray-400">
                                <button wire:click="applyCoupon"
                                    class="bg-gray-800 text-white px-3 py-2 rounded-lg text-sm font-bold hover:bg-black transition border border-gray-800 shadow-sm">
                                    {{ __('store.apply') }}
                                </button>
                            </div>
                            @if(!$appliedCoupon)
                                <p class="text-[11px] text-gray-500">{{ __('store.enter_discount_code_apply') }}</p>
                            @endif
                            @error('coupon') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                            @if(session('coupon_success'))
                            <p class="text-green-600 text-xs mt-1 font-bold">{{ session('coupon_success') }}</p> @endif
                        </div>

                        <div class="space-y-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('store.select_payment_method') }}</h3>

                            <div class="grid grid-cols-1 gap-4">

                                @if($codEnabled)
                                    <button type="button" wire:click="$set('paymentMethod','cod')"
                                        class="group w-full flex items-center justify-between p-3 bg-white border rounded-xl shadow-sm hover:shadow-md transition-all duration-200 h-full
                                                           {{ $paymentMethod === 'cod' ? 'border-green-600 ring-1 ring-green-200' : 'border-gray-200 hover:border-green-600' }}">
                                        <div class="flex flex-col items-start text-left">
                                            <div
                                                class="h-7 w-7 mb-1 text-green-600 bg-green-50 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <span
                                                class="text-sm font-bold text-gray-800 group-hover:text-green-700 transition-colors">
                                                {{ $settings['cod_label'] ?? null ?: __('store.cash_on_delivery') }}
                                            </span>
                                            <span
                                                class="text-[10px] text-gray-500">{{ __('store.pay_when_you_receive') }}</span>
                                        </div>
                                        <div
                                            class="w-7 h-7 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-colors text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </button>
                                @endif

                                @if(!empty($settings['sslcommerz_store_id']))
                                    <button type="button" wire:click="$set('paymentMethod','sslcommerz')"
                                        class="group w-full flex items-center justify-between p-3 bg-white border rounded-xl shadow-sm hover:shadow-md transition-all duration-200 h-full
                                                           {{ $paymentMethod === 'sslcommerz' ? 'border-primary ring-1 ring-primary/20' : 'border-gray-200 hover:border-primary' }}">
                                        <div class="flex flex-col items-start text-left">
                                            <img src="https://securepay.sslcommerz.com/public/image/sslcommerz.png"
                                                alt="SSLCommerz"
                                                class="h-5 mb-1 opacity-90 group-hover:opacity-100 transition-opacity">
                                            <span
                                                class="text-sm font-bold text-gray-800 group-hover:text-primary transition-colors">
                                                {{ $settings['sslcommerz_label'] ?? null ?: __('store.pay_with_sslcommerz') }}
                                            </span>
                                            <span class="text-[10px] text-gray-500">bKash / Cards / Banking</span>
                                        </div>
                                        <div
                                            class="w-7 h-7 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </button>
                                @endif

                                @if(!empty($settings['stripe_api_key']))
                                    <button type="button" wire:click="$set('paymentMethod','stripe')"
                                        class="group w-full flex items-center justify-between p-3 bg-white border rounded-xl shadow-sm hover:shadow-md transition-all duration-200 h-full
                                                           {{ $paymentMethod === 'stripe' ? 'border-indigo-600 ring-1 ring-indigo-200' : 'border-gray-200 hover:border-indigo-600' }}">
                                        <div class="flex flex-col items-start text-left">
                                            <svg class="h-5 mb-1 text-primary" viewBox="0 0 40 17" fill="currentColor">
                                                <path
                                                    d="M4.64 16.56h-4.64v-16.56h4.64v16.56zm9.24-11.23c-2.5 0-4.32 1.95-4.32 4.67s1.82 4.67 4.32 4.67 4.32-1.95 4.32-4.67-1.82-4.67-4.32-4.67zm0 7.64c-1.57 0-2.67-1.32-2.67-2.97s1.1-2.97 2.67-2.97 2.67 1.32 2.67 2.97-1.1 2.97-2.67 2.97zm8.4-7.64h-1.6v11.23h1.6v-4.82c0-2.3.9-3.2 2.65-3.2v-1.63c-1.25 0-2.22.53-2.65 1.48v-3.06zm8.17 0c-2.5 0-4.32 1.95-4.32 4.67s1.82 4.67 4.32 4.67 4.32-1.95 4.32-4.67-1.82-4.67-4.32-4.67zm0 7.64c-1.57 0-2.67-1.32-2.67-2.97s1.1-2.97 2.67-2.97 2.67 1.32 2.67 2.97-1.1 2.97-2.67 2.97zm5.95 3.59h1.6v-15.17h-1.6v15.17zm6.75-12.87c.92 0 1.62-.7 1.62-1.62s-.7-1.62-1.62-1.62-1.62.7-1.62 1.62.7 1.62 1.62 1.62zm-.8 1.64h1.6v11.23h-1.6v-11.23z" />
                                            </svg>
                                            <span
                                                class="text-sm font-bold text-gray-800 group-hover:text-primary transition-colors">
                                                {{ $settings['stripe_label'] ?? null ?: __('store.credit_debit_card') }}
                                            </span>
                                            <span class="text-[10px] text-gray-500">{{ __('store.international') }}</span>
                                        </div>
                                        <div
                                            class="w-7 h-7 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </button>
                                @endif

                                @if(!empty($settings['bkash_app_key']) && !empty($settings['bkash_username']))
                                    <button type="button" wire:click="$set('paymentMethod','bkash')"
                                        class="group w-full flex items-center justify-between p-3 bg-white border rounded-xl shadow-sm hover:shadow-md transition-all duration-200 h-full
                                                           {{ $paymentMethod === 'bkash' ? 'border-pink-600 ring-1 ring-pink-200' : 'border-gray-200 hover:border-pink-600' }}">
                                        <div class="flex flex-col items-start text-left">
                                            <div
                                                class="h-7 w-7 mb-1 text-pink-600 bg-pink-50 rounded-lg flex items-center justify-center font-bold">
                                                bK</div>
                                            <span
                                                class="text-sm font-bold text-gray-800 group-hover:text-pink-700 transition-colors">
                                                bKash
                                            </span>
                                            <span class="text-[10px] text-gray-500">{{ __('store.bkash_wallet') }}</span>
                                        </div>
                                        <div
                                            class="w-7 h-7 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-pink-600 group-hover:text-white transition-colors text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </button>
                                @endif

                                @if(!empty($settings['portpos_app_key']) && !empty($settings['portpos_secret_key']))
                                    <button type="button" wire:click="$set('paymentMethod','portpos')"
                                        class="group w-full flex items-center justify-between p-3 bg-white border rounded-xl shadow-sm hover:shadow-md transition-all duration-200 h-full
                                                           {{ $paymentMethod === 'portpos' ? 'border-black ring-1 ring-black/10' : 'border-gray-200 hover:border-black' }}">
                                        <div class="flex flex-col items-start text-left">
                                            <div
                                                class="h-7 w-7 mb-1 text-white bg-black rounded-lg flex items-center justify-center font-bold text-[10px]">
                                                PP</div>
                                            <span
                                                class="text-sm font-bold text-gray-800 group-hover:text-black transition-colors">
                                                {{ $settings['portpos_label'] ?? null ?: __('store.pay_with_portpos') }}
                                            </span>
                                            <span
                                                class="text-[10px] text-gray-500">{{ __('store.portpos_invoice_checkout') }}</span>
                                        </div>
                                        <div
                                            class="w-7 h-7 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-black group-hover:text-white transition-colors text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </button>
                                @endif

                                @if(!empty($offlinePaymentMethods))
                                    <button type="button" wire:click="$set('paymentMethod','offline')"
                                        class="group w-full flex items-center justify-between p-3 bg-white border rounded-xl shadow-sm hover:shadow-md transition-all duration-200 h-full
                                                           {{ $paymentMethod === 'offline' ? 'border-primary ring-1 ring-primary/20' : 'border-gray-200 hover:border-primary' }}">
                                        <div class="flex flex-col items-start text-left">
                                            <div
                                                class="h-7 w-7 mb-1 text-primary bg-blue-50 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2M5 7v12a2 2 0 002 2h10a2 2 0 002-2V7" />
                                                </svg>
                                            </div>
                                            <span
                                                class="text-sm font-bold text-gray-800 group-hover:text-primary transition-colors">
                                                {{ __('store.offline_payment') }}
                                            </span>
                                            <span
                                                class="text-[10px] text-gray-500">{{ __('store.bank_transfer_wallets') }}</span>
                                        </div>
                                        <div
                                            class="w-7 h-7 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </button>
                                @endif

                            </div>
                        </div>

                        @if($paymentMethod === 'offline')
                            <div class="mb-4 space-y-3">
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600">{{ __('store.select_method') }}</label>
                                    <div class="relative mt-1">
                                        <select wire:model.live="offlinePaymentMethodId"
                                            class="block w-full appearance-none border border-gray-300 rounded-lg text-sm px-3 py-2 bg-white pr-8 focus:outline-none focus:ring-0 focus:border-gray-300">
                                            <option value="">{{ __('store.select_method') }}</option>
                                            @foreach($offlinePaymentMethods as $index => $method)
                                                <option value="{{ $index }}">{{ $method['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <span
                                            class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-gray-400">
                                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    @error('offlinePaymentMethodId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                @php
                                    $methodIndex = (int) $offlinePaymentMethodId;
                                    $selectedMethod = $offlinePaymentMethods[$methodIndex] ?? null;
                                @endphp
                                @if($selectedMethod)
                                    <div
                                        class="text-[11px] text-gray-600 bg-gray-50 border border-gray-200 rounded-lg p-3 whitespace-pre-line">
                                        {{ $selectedMethod['instructions'] ?? __('store.follow_payment_instructions') }}</div>
                                @endif
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600">{{ __('store.reference_optional') }}</label>
                                    <input type="text" wire:model="offlineReference"
                                        class="mt-1 block w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-0 focus:border-gray-300"
                                        placeholder="{{ __('store.transaction_id_note') }}">
                                    @error('offlineReference') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600 mb-1">{{ __('store.upload_payment_proof_optional') }}</label>

                                    <div x-data="{ isDragging: false }" class="relative group">
                                        <div class="flex flex-col items-center justify-center w-full min-h-[100px] border-2 border-dashed rounded-xl transition-all duration-200
                                                        {{ $offlineProof ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50 group-hover:bg-gray-100 group-hover:border-primary/50' }}"
                                            :class="isDragging ? 'border-primary bg-primary/5' : ''">
                                            <div class="flex flex-col items-center justify-center py-4 px-4 text-center">
                                                @if($offlineProof)
                                                    <div
                                                        class="flex items-center gap-2 text-green-700 bg-green-100 px-3 py-1.5 rounded-full mb-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <span
                                                            class="text-xs font-bold truncate max-w-[200px]">{{ __('store.file_selected') }}
                                                            {{ $offlineProof->getClientOriginalName() }}</span>
                                                    </div>
                                                    <p class="text-[10px] text-green-600 font-medium">
                                                        {{ __('store.click_to_replace') }}</p>
                                                @else
                                                    <div
                                                        class="h-10 w-10 mb-2 text-gray-400 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <p class="text-xs text-gray-600 font-bold mb-0.5">
                                                        <span class="text-primary">{{ __('store.click_to_upload') }}</span>
                                                        {{ __('store.or_drag_and_drop') }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-medium">
                                                        {{ __('store.file_formats_limit') }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <input id="offlineProof" type="file" wire:model="offlineProof"
                                            @dragenter="isDragging = true" @dragleave="isDragging = false"
                                            @drop="isDragging = false"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />

                                        @if($offlineProof)
                                            <button type="button" wire:click="$set('offlineProof', null)"
                                                class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1 hover:bg-red-200 transition-colors shadow-sm border border-red-200 z-20">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    @error('offlineProof') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                    @enderror

                                    <p
                                        class="text-[10px] text-amber-700 mt-3 flex items-center gap-1.5 bg-amber-50 border border-amber-100 rounded-lg px-2 py-1.5">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v4m0 4h.01M10.29 3.86l-7.1 12.29A1.5 1.5 0 004.5 18h15a1.5 1.5 0 001.31-2.25l-7.1-12.29a1.5 1.5 0 00-2.62 0z">
                                            </path>
                                        </svg>
                                        <span class="font-medium">{{ __('store.provide_reference_or_proof') }}</span>
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($paymentMethod === 'cod' || $paymentMethod === 'offline')
                            <button wire:click="placeOrder" wire:loading.attr="disabled"
                                class="w-full bg-primary text-white py-3 rounded-lg font-bold text-base shadow-md hover:bg-primary transition-all flex justify-center items-center
                                                   {{ (!auth()->check() && !$authSettings['guest_checkout_enabled']) ? 'opacity-70 cursor-not-allowed' : '' }}">
                                <span wire:loading.remove>
                                    {{ (!auth()->check() && !$authSettings['guest_checkout_enabled']) ? __('store.log_in_to_order') : __('store.complete_order') }}
                                </span>
                                <span wire:loading><svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg></span>
                            </button>
                        @else
                            <form action="{{ route('payment.pay') }}" method="POST" class="w-full">
                                @csrf
                                <input type="hidden" name="gateway" value="{{ $paymentMethod }}">
                                <input type="hidden" name="amount" value="{{ number_format($total, 2, '.', '') }}">
                                <input type="hidden" name="payer_reference" value="{{ $phone }}">
                                <input type="hidden" name="billing_name" value="{{ $billing['name'] }}">
                                <input type="hidden" name="billing_email" value="{{ $email }}">
                                <input type="hidden" name="billing_phone" value="{{ $phone }}">
                                <input type="hidden" name="billing_address" value="{{ $billing['address_line1'] }}">
                                <input type="hidden" name="billing_city" value="{{ $billing['city'] }}">
                                <input type="hidden" name="billing_state" value="{{ $billing['state'] }}">
                                <input type="hidden" name="billing_postal_code" value="{{ $billing['postal_code'] }}">
                                <input type="hidden" name="billing_country" value="{{ $billing['country_code'] }}">
                                <button type="submit"
                                    class="w-full bg-primary text-white py-3 rounded-lg font-bold text-base shadow-md hover:bg-primary transition-all flex justify-center items-center">
                                    {{ __('store.proceed_to_payment') }}
                                </button>
                            </form>
                        @endif
                        @error('auth') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror

                        <div class="mt-4 flex justify-center items-center text-xs text-gray-400 gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <span>{{ __('store.ssl_secure_checkout') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <div class="py-12 sm:py-16 px-4 flex flex-col items-center justify-center min-h-[50vh]">
                <h1 class="text-2xl font-bold text-gray-900 mb-8 text-center">{{ __('store.shopping_cart_checkout') }}</h1>
                <div class="max-w-md w-full bg-white rounded-3xl border border-gray-200/80 p-8 sm:p-10 text-center shadow-lg shadow-gray-100 flex flex-col items-center">
                    <div class="relative mb-6 inline-flex">
                        <div class="absolute inset-0 bg-primary/10 rounded-full animate-ping opacity-20"></div>
                        <div
                            class="relative z-10 flex items-center justify-center w-20 h-20 bg-gray-50 rounded-full text-gray-400 border border-gray-100 shadow-inner">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-900 mb-2 tracking-tight">{{ __('store.your_cart_is_empty') }}</h2>
                    <p class="text-gray-500 text-sm font-medium leading-relaxed mb-8 max-w-xs">Looks like you haven't added anything to
                        your cart yet. Let's find something amazing for you!</p>
                    <a href="{{ route('store.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center bg-gray-900 hover:bg-black text-white px-8 py-3.5 rounded-xl font-bold text-sm uppercase tracking-wider transition-all duration-200 shadow-md hover:shadow-lg group">
                        <span>{{ __('store.start_shopping') }}</span>
                        <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        @endif

        @if (session()->has('order_success'))
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" x-data>
                <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl border border-gray-100">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('store.order_placed_successfully') }}</h3>
                    <p class="text-gray-600 mb-6">{{ session('order_success') }}</p>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('store.index') }}"
                            class="block w-full bg-primary text-white py-2 rounded-lg font-bold hover:bg-primary">{{ __('store.continue_shopping') }}</a>
                        <a href="{{ route('customer.dashboard') }}"
                            class="block w-full border border-gray-200 text-gray-700 py-2 rounded-lg font-semibold hover:bg-gray-50">{{ __('store.visit_home') }}</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>