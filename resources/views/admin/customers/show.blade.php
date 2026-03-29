@extends('admin.layouts.default')

@section('content')
    @include('admin.includes.breadcrumb')

    <div class="space-y-5">
        @include('admin.includes.message')

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $customer->name ?? 'Customer' }}</h2>
                <p class="text-xs text-gray-500">Customer ID: #{{ $customer->id }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="admin-action-btn admin-action-edit" title="Edit Customer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </a>
                <form method="POST" action="{{ route('admin.customers.destroy', $customer->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-action-btn admin-action-delete" title="Delete Customer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <h3 class="text-sm font-semibold text-gray-800">Profile</h3>
                <div class="mt-3 space-y-2 text-xs text-gray-600">
                    <div><span class="font-semibold text-gray-700">Name:</span> {{ $customer->name ?? 'N/A' }}</div>
                    <div><span class="font-semibold text-gray-700">Email:</span> {{ $customer->email ?? 'N/A' }}</div>
                    <div><span class="font-semibold text-gray-700">Phone:</span> {{ $customer->phone ?? 'N/A' }}</div>
                    <div><span class="font-semibold text-gray-700">Gender:</span> {{ $customer->gender_label }}</div>
                    <div><span class="font-semibold text-gray-700">Birthday:</span> {{ $customer->birthday?->format('M d, Y') ?? 'N/A' }}</div>
                    <div><span class="font-semibold text-gray-700">Joined:</span> {{ $customer->created_at?->format('M d, Y') }}</div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 lg:col-span-2">
                <h3 class="text-sm font-semibold text-gray-800">Addresses</h3>
                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @forelse($customer->addresses as $address)
                        <div class="border border-gray-200 rounded-lg p-3 text-xs text-gray-600">
                            <div class="font-semibold text-gray-700">{{ ucfirst($address->type ?? 'Address') }}</div>
                            <div class="mt-1">
                                {{ $address->address_line1 ?? '' }}
                                {{ $address->address_line2 ? ', '.$address->address_line2 : '' }}
                                {{ $address->city ? ', '.$address->city : '' }}
                                {{ $address->state ? ', '.$address->state : '' }}
                                {{ $address->postal_code ? ', '.$address->postal_code : '' }}
                                {{ $address->country_label ? ', '.$address->country_label : '' }}
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-500">No addresses on file.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-primary hover:underline">View all orders</a>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="p-2 font-semibold text-gray-700">Order</th>
                            <th class="p-2 font-semibold text-gray-700">Status</th>
                            <th class="p-2 font-semibold text-gray-700">Total</th>
                            <th class="p-2 font-semibold text-gray-700">Placed</th>
                            <th class="p-2 font-semibold text-gray-700 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->orders as $order)
                            <tr class="border-t">
                                <td class="p-2 font-semibold text-gray-800">#{{ $order->order_number }}</td>
                                <td class="p-2 text-gray-600">{{ ucfirst($order->status) }}</td>
                                <td class="p-2 text-gray-600">{{ $order->currency_code ?? '' }} {{ number_format((float) $order->total, 2) }}</td>
                                <td class="p-2 text-gray-600">{{ $order->created_at?->format('M d, Y') }}</td>
                                <td class="p-2 text-end">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-primary text-xs font-semibold hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">No orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
