<div class="mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Order #{{ $order->order_number ?? $order->id }}</h1>
            <p class="text-xs text-gray-500">Placed {{ $order->created_at?->format('Y-m-d H:i') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-700' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-700' : ($order->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                {{ ucfirst($order->status) }}
            </span>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Back to Orders</a>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-100 px-4 py-3 text-sm text-green-700">
            {{ session('message') }}
        </div>
    @endif

    <script>
        document.addEventListener('click', function (event) {
            const button = event.target.closest('[data-copy]');
            if (!button) return;
            const value = button.getAttribute('data-copy') || '';
            if (!value) return;
            navigator.clipboard?.writeText(value).then(() => {
                if (typeof window.showAdminToast === 'function') {
                    window.showAdminToast('Copied to clipboard');
                }
            });
        });
    </script>

    @php
        $defaultCurrency = \App\Models\Currency::getActive();
        $defaultSymbol = $defaultCurrency?->symbol ?? '$';
        $defaultCode = $defaultCurrency?->code ?? 'BDT';
        $currencySymbol = $order->currency?->symbol ?? '';
        $currencyCode = $order->currency_code ?? '';
        $resolvedCode = $currencyCode ?: $defaultCode;
        $displaySymbol = $currencySymbol ?: ($resolvedCode ? $resolvedCode . ' ' : $defaultSymbol);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-gray-800">Order Actions</h2>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">
                        Current: {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Update Status</label>
                        <select wire:model.live="statusSelection" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-primary appearance-none bg-white">
                            <option value="">Select status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        @error('statusSelection') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-2 md:pt-6">
                        <input id="notifyCustomer" type="checkbox" wire:model="notifyCustomer" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <label for="notifyCustomer" class="text-xs text-gray-600">Notify customer</label>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="button" wire:click="saveOrderAction" wire:confirm="Save this order action?" class="w-full md:w-auto bg-primary text-white text-xs font-semibold rounded-lg px-4 py-2 hover:bg-primary">
                        Change Order Status
                    </button>
                </div>
            </div>

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
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Payment Status</p>
                        <p class="text-gray-800">{{ $order->payment_status ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Currency</p>
                        <p class="text-gray-800">{{ $resolvedCode ?: 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Shipping Address</p>
                            @php
                                $shippingAddressText = trim(collect([
                                    $order->shippingAddress?->name,
                                    $order->shippingAddress?->address_line1,
                                    trim(($order->shippingAddress?->city ?? '').' '.($order->shippingAddress?->state ?? '').' '.($order->shippingAddress?->postal_code ?? '')),
                                ])->filter()->implode(', '));
                            @endphp
                            <button type="button" class="text-xs text-gray-500 hover:text-gray-700" data-copy="{{ $shippingAddressText }}" title="Copy address">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h7a2 2 0 012 2v7"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 9h7a2 2 0 012 2v7H7a2 2 0 01-2-2V9z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-gray-800">
                            {{ $order->shippingAddress?->name ?? '—' }}<br>
                            {{ $order->shippingAddress?->address_line1 ?? '' }}<br>
                            {{ $order->shippingAddress?->city ?? '' }} {{ $order->shippingAddress?->state ?? '' }} {{ $order->shippingAddress?->postal_code ?? '' }}
                        </p>
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Billing Address</p>
                            @php
                                $billingAddressText = trim(collect([
                                    $order->billingAddress?->name,
                                    $order->billingAddress?->address_line1,
                                    trim(($order->billingAddress?->city ?? '').' '.($order->billingAddress?->state ?? '').' '.($order->billingAddress?->postal_code ?? '')),
                                ])->filter()->implode(', '));
                            @endphp
                            <button type="button" class="text-xs text-gray-500 hover:text-gray-700" data-copy="{{ $billingAddressText }}" title="Copy address">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h7a2 2 0 012 2v7"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 9h7a2 2 0 012 2v7H7a2 2 0 01-2-2V9z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-gray-800">
                            {{ $order->billingAddress?->name ?? '—' }}<br>
                            {{ $order->billingAddress?->address_line1 ?? '' }}<br>
                            {{ $order->billingAddress?->city ?? '' }} {{ $order->billingAddress?->state ?? '' }} {{ $order->billingAddress?->postal_code ?? '' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-3">Message Customer</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">To</label>
                        <p class="text-sm text-gray-800">
                            {{ $order->user?->email ?? ($order->billingAddress?->email ?? '—') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Subject</label>
                        <input type="text" wire:model.live="emailSubject" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-primary" placeholder="Order update">
                        @error('emailSubject') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Message</label>
                        <textarea rows="5" wire:model.live="emailMessage" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-primary" placeholder="Write your message..."></textarea>
                        @error('emailMessage') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="button" wire:click="sendCustomerEmail" wire:loading.attr="disabled" class="w-full bg-primary text-white text-sm font-semibold rounded-lg px-4 py-2 hover:bg-primary">
                        Send Email
                    </button>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-3">Email History</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 font-semibold text-gray-700">Sent At</th>
                                <th class="p-2 font-semibold text-gray-700">To</th>
                                <th class="p-2 font-semibold text-gray-700">Subject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->emailLogs->sortByDesc('sent_at') as $log)
                                <tr class="border-t">
                                    <td class="p-2">{{ $log->sent_at?->format('Y-m-d H:i') ?? $log->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="p-2">{{ $log->to_email }}</td>
                                    <td class="p-2">{{ $log->subject }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-3 text-gray-500">No emails sent yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="space-y-5">
            @if($order->offlinePayments->isNotEmpty())
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <h2 class="text-sm font-bold text-gray-800 mb-3">Offline Payment Proof</h2>
                    <div class="space-y-3 text-sm">
                        @foreach($order->offlinePayments as $payment)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $payment->method_name }}</div>
                                        <div class="text-xs text-gray-500">
                                            Status:
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                                {{ $payment->status === 'approved' ? 'bg-green-100 text-green-700' : ($payment->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($payment->attachment_path)
                                            <a href="{{ asset('storage/'.$payment->attachment_path) }}" target="_blank" class="text-xs text-primary hover:underline">View Proof</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-gray-600">
                                    <div>Amount: {{ $displaySymbol }}{{ number_format($payment->amount, 2) }}</div>
                                    @if($payment->reference)
                                        <div>Reference: {{ $payment->reference }}</div>
                                    @endif
                                    @if($payment->instructions)
                                        <div class="whitespace-pre-line">Instructions: {{ $payment->instructions }}</div>
                                    @endif
                                    @if($payment->reviewed_at)
                                        <div>Reviewed: {{ $payment->reviewed_at->format('Y-m-d H:i') }}</div>
                                    @endif
                                </div>
                                @if($payment->status === 'pending')
                                    <div class="mt-3 flex items-center gap-2">
                                        <button type="button" wire:click="approveOfflinePayment({{ $payment->id }})" wire:confirm="Approve this offline payment?" class="text-xs font-semibold text-green-600 border border-green-200 rounded-lg px-3 py-1.5 hover:bg-green-50">Approve</button>
                                        <button type="button" wire:click="rejectOfflinePayment({{ $payment->id }})" wire:confirm="Reject this offline payment?" class="text-xs font-semibold text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50">Reject</button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-3">Payment Summary</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Payment Method</span>
                        <span class="font-semibold text-gray-900">{{ $order->payment_method ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Payment Status</span>
                        <span class="font-semibold text-gray-900">{{ $order->payment_status ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold text-gray-900">{{ $displaySymbol }}{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Discount</span>
                        <span class="font-semibold text-gray-900">- {{ $displaySymbol }}{{ number_format($order->discount, 2) }}</span>
                    </div>
                    @if($order->discounts->isNotEmpty())
                        <div class="flex justify-between text-gray-600">
                            <span>Coupon</span>
                            <span class="font-semibold text-gray-900">{{ $order->discounts->pluck('code')->join(', ') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span class="font-semibold text-gray-900">{{ $displaySymbol }}{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between text-gray-900">
                        <span class="font-bold">Total Charged</span>
                        <span class="font-bold">{{ $displaySymbol }}{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

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
                    @if($order->discounts->isNotEmpty())
                        <div class="flex justify-between text-gray-600">
                            <span>Coupon</span>
                            <span class="font-semibold text-gray-900">{{ $order->discounts->pluck('code')->join(', ') }}</span>
                        </div>
                    @endif
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
                    <div class="flex items-center justify-between">
                        <p>{{ $order->user?->email ?? ($order->billingAddress?->email ?? '—') }}</p>
                        @php
                            $customerEmail = $order->user?->email ?? $order->billingAddress?->email ?? '';
                        @endphp
                        <button type="button" class="text-xs text-gray-500 hover:text-gray-700" data-copy="{{ $customerEmail }}" title="Copy email">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h7a2 2 0 012 2v7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 9h7a2 2 0 012 2v7H7a2 2 0 01-2-2V9z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Orders: {{ $customerOrderCount }}</p>
                    <p class="text-xs text-gray-500">Lifetime spend: {{ $displaySymbol }}{{ number_format($customerTotalSpend, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
