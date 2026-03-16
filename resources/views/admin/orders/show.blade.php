@extends('admin.layouts.default')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Order #{{ $order->order_number ?? $order->id }}</h1>
            <p class="text-xs text-gray-500">Placed {{ $order->created_at?->format('Y-m-d H:i') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-700' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                {{ ucfirst($order->status) }}
            </span>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Back to Orders</a>
        </div>
    </div>

    @php
        $defaultCurrency = \App\Models\Currency::getActive();
        $defaultSymbol = $defaultCurrency?->symbol ?? '$';
        $defaultCode = $defaultCurrency?->code ?? 'USD';
        $currencySymbol = $order->currency?->symbol ?? '';
        $currencyCode = $order->currency_code ?? '';
        $resolvedCode = $currencyCode ?: $defaultCode;
        $displaySymbol = $currencySymbol ?: ($resolvedCode ? $resolvedCode . ' ' : $defaultSymbol);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-3">Order Items</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 font-semibold text-gray-700">Product</th>
                                <th class="p-2 font-semibold text-gray-700">Variation</th>
                                <th class="p-2 font-semibold text-gray-700">Qty</th>
                                <th class="p-2 font-semibold text-gray-700">Price</th>
                                <th class="p-2 font-semibold text-gray-700">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->items as $item)
                                <tr class="border-t">
                                    <td class="p-2">
                                        <p class="font-medium text-gray-900">{{ $item->product?->name ?? 'Product deleted' }}</p>
                                        <p class="text-xs text-gray-500">#{{ $item->product_id }}</p>
                                    </td>
                                    <td class="p-2 text-xs text-gray-600">
                                        @if($item->attributes)
                                            @foreach($item->attributes as $attrKey => $attrValue)
                                                <div>{{ $attrKey }}: {{ $attrValue }}</div>
                                            @endforeach
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="p-2">{{ $item->quantity }}</td>
                                    <td class="p-2">{{ $displaySymbol }}{{ number_format($item->price, 2) }}</td>
                                    <td class="p-2">{{ $displaySymbol }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">No items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-3">Delivery Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Shipping Method</p>
                        <p class="text-gray-800">{{ $order->shippingMethod?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Payment Method</p>
                        <p class="text-gray-800">{{ $order->payment_method ?? '—' }}</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Shipping Address</p>
                        <p class="text-gray-800">
                            {{ $order->shippingAddress?->name ?? '—' }}<br>
                            {{ $order->shippingAddress?->address_line1 ?? '' }}<br>
                            {{ $order->shippingAddress?->city ?? '' }} {{ $order->shippingAddress?->state ?? '' }} {{ $order->shippingAddress?->postal_code ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Billing Address</p>
                        <p class="text-gray-800">
                            {{ $order->billingAddress?->name ?? '—' }}<br>
                            {{ $order->billingAddress?->address_line1 ?? '' }}<br>
                            {{ $order->billingAddress?->city ?? '' }} {{ $order->billingAddress?->state ?? '' }} {{ $order->billingAddress?->postal_code ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-3">Order Summary</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold text-gray-900">{{ $displaySymbol }}{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Discount</span>
                        <span class="font-semibold text-gray-900">- {{ $displaySymbol }}{{ number_format($order->discount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span class="font-semibold text-gray-900">{{ $displaySymbol }}{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between text-gray-900">
                        <span class="font-bold">Total</span>
                        <span class="font-bold">{{ $displaySymbol }}{{ number_format($order->total, 2) }}</span>
                    </div>
                    <div class="pt-2 text-xs text-gray-500">
                        Currency: {{ $resolvedCode ?: 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-3">Customer</h2>
                <div class="text-sm text-gray-700">
                    <p class="font-semibold text-gray-900">{{ $order->user?->name ?? 'Guest' }}</p>
                    <p>{{ $order->user?->email ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
