<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Shopping Cart & Checkout</h1>

        @if(count($cart) > 0)
            <div class="lg:grid lg:grid-cols-12 lg:gap-8 lg:items-start">
                
                <div class="lg:col-span-8 space-y-6">
                    
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Items in Cart</h2>
                            <span class="text-xs font-medium text-gray-500">{{ count($cart) }} Items</span>
                        </div>
                        
                        <ul class="divide-y divide-gray-100">
                            @foreach($cart as $key => $item)
                                <li class="p-4 flex items-center">
                                    <div class="flex-shrink-0 w-16 h-16 border border-gray-200 rounded-md overflow-hidden bg-gray-100">
                                        @php
                                            $img = 'https://placehold.co/100';
                                            if (!empty($item['thumbnail'])) {
                                                $img = Str::startsWith($item['thumbnail'], ['http']) 
                                                    ? $item['thumbnail'] 
                                                    : Storage::url($item['thumbnail']);
                                            }
                                        @endphp
                                        <img src="{{ $img }}" class="w-full h-full object-cover">
                                    </div>

                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900 line-clamp-1 hover:text-primary cursor-pointer">
                                                    {{ $item['name'] }}
                                                </h3>
                                                @if(!empty($item['attributes']))
                                                    <div class="text-xs text-gray-500 mt-0.5 flex flex-wrap gap-2">
                                                        @foreach($item['attributes'] as $k => $v)
                                                            <span class="bg-gray-100 px-1.5 py-0.5 rounded">{{ $k }}: {{ $v }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-sm font-bold text-gray-900">${{ number_format($item['price'], 2) }}</p>
                                        </div>

                                        <div class="flex justify-between items-center mt-2">
                                            <div class="flex items-center border border-gray-300 rounded h-7">
                                                <button wire:click="decrement('{{ $key }}')" class="px-2 text-gray-500 hover:text-primary hover:bg-gray-50 h-full border-r border-gray-300 text-xs">-</button>
                                                <input type="text" value="{{ $item['quantity'] }}" readonly class="w-8 text-center border-none p-0 text-gray-900 font-bold text-xs h-full focus:ring-0">
                                                <button wire:click="increment('{{ $key }}')" class="px-2 text-gray-500 hover:text-primary hover:bg-gray-50 h-full border-l border-gray-300 text-xs">+</button>
                                            </div>
                                            
                                            <button wire:click="removeItem('{{ $key }}')" class="text-xs text-red-500 hover:underline">Remove</button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-4 border-b pb-2">Customer Details</h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Email</label>
                                <input wire:model="email" type="email" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3 placeholder-gray-400" placeholder="john@example.com">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Phone</label>
                                <input wire:model="phone" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3" placeholder="+123456789">
                                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Full Name</label>
                                <input wire:model="billing.name" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3">
                                @error('billing.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Street Address</label>
                                <input wire:model="billing.address_line1" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3" placeholder="123 Main St">
                                @error('billing.address_line1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">City</label>
                                    <input wire:model="billing.city" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3">
                                    @error('billing.city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Postal Code</label>
                                    <input wire:model="billing.postal_code" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <label class="inline-flex items-center cursor-pointer">
                                <input wire:model.live="shipToDifferentAddress" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Ship to a different address?</span>
                            </label>
                        </div>

                        @if($shipToDifferentAddress)
                            <div class="mt-4 space-y-4 p-4 bg-gray-50 rounded border border-gray-200 animate-fade-in-down">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Recipient Name</label>
                                    <input wire:model="shipping.name" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Address</label>
                                    <input wire:model="shipping.address_line1" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">City</label>
                                        <input wire:model="shipping.city" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Postal Code</label>
                                        <input wire:model="shipping.postal_code" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Delivery Method</h2>
                        <div class="space-y-2">
                            @foreach($shippingMethods as $method)
                                <label class="relative flex items-center justify-between p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ $selectedShippingMethod == $method->id ? 'border-primary bg-blue-50 ring-1 ring-primary' : 'border-gray-200' }}">
                                    <div class="flex items-center">
                                        <input wire:model.live="selectedShippingMethod" type="radio" value="{{ $method->id }}" class="h-4 w-4 text-primary focus:ring-primary border-gray-300">
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-900">{{ $method->name }}</span>
                                            <span class="block text-xs text-gray-500">{{ $method->estimated_days }} Days</span>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">${{ number_format($method->cost, 2) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedShippingMethod') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                </div>

                <div class="lg:col-span-4 mt-8 lg:mt-0">
                    <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-5 sticky top-24">
                        <h2 class="text-base font-bold text-gray-900 mb-4 border-b pb-2">Order Summary</h2>

                        <div class="space-y-3 text-sm mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-900">${{ number_format($subtotal, 2) }}</span>
                            </div>

                            @if($appliedCoupon)
                                <div class="flex justify-between text-green-600">
                                    <span class="flex items-center">
                                        Coupon <button wire:click="removeCoupon" class="ml-1 text-xs text-red-500 hover:underline">(x)</button>
                                    </span>
                                    <span class="font-bold">-${{ number_format($discountAmount, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="font-bold text-gray-900">${{ number_format($shippingCost, 2) }}</span>
                            </div>

                            <div class="border-t border-dashed border-gray-300 pt-3 flex justify-between items-end">
                                <span class="text-base font-bold text-gray-900">Total</span>
                                <span class="text-xl font-extrabold text-primary">${{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex space-x-2">
                                <input wire:model="couponCode" type="text" placeholder="Promo code" class="flex-1 text-sm border-gray-300 rounded focus:ring-primary focus:border-primary py-2 px-3 uppercase placeholder-gray-400">
                                <button wire:click="applyCoupon" class="bg-gray-800 text-white px-3 py-2 rounded text-sm font-bold hover:bg-black transition">Apply</button>
                            </div>
                            @error('coupon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            @if(session('coupon_success')) <p class="text-green-600 text-xs mt-1 font-bold">{{ session('coupon_success') }}</p> @endif
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center p-3 border border-primary bg-blue-50/50 rounded-lg">
                                <input type="radio" checked disabled class="h-4 w-4 text-primary">
                                <div class="ml-2">
                                    <span class="block text-sm font-bold text-gray-900">Cash on Delivery</span>
                                    <span class="block text-xs text-gray-500">Pay upon receipt.</span>
                                </div>
                            </label>
                        </div>

                        <button wire:click="placeOrder" 
                                wire:loading.attr="disabled"
                                class="w-full bg-primary text-white py-3 rounded-lg font-bold text-base shadow-md hover:bg-blue-700 transition-all flex justify-center items-center">
                            <span wire:loading.remove>Complete Order</span>
                            <span wire:loading><svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                        </button>

                        <div class="mt-4 flex justify-center items-center text-xs text-gray-400 gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>SSL Secure Checkout</span>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <div class="flex flex-col items-center justify-center py-16 bg-white rounded-lg border border-dashed border-gray-300 text-center">
                <div class="bg-gray-50 p-4 rounded-full mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
                <p class="text-sm text-gray-500 mb-6">Looks like you haven't added anything yet.</p>
                <a href="{{ route('store.index') }}" class="px-6 py-2 bg-primary text-white text-sm font-bold rounded hover:bg-blue-700 transition">
                    Start Shopping
                </a>
            </div>
        @endif

        @if (session()->has('order_success'))
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" x-data>
                <div class="bg-white rounded-lg p-8 max-w-sm w-full text-center shadow-2xl">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Order Placed!</h3>
                    <p class="text-gray-600 mb-6">{{ session('order_success') }}</p>
                    <a href="{{ route('store.index') }}" class="block w-full bg-primary text-white py-2 rounded font-bold hover:bg-blue-700">Continue Shopping</a>
                </div>
            </div>
        @endif

    </div>
</div>
