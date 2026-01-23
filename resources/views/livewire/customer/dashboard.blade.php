<div class="bg-gray-50 min-h-screen font-sans">
    
    <div class="md:hidden bg-white border-b border-gray-200 p-4">
        <h1 class="text-xl font-bold text-gray-900">My Account</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            
            <aside class="w-full md:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-8">
                    
                    <div class="p-6 bg-gray-50 border-b border-gray-100 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center text-2xl font-bold mb-3">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <h2 class="font-bold text-gray-900 truncate w-full">{{ auth()->user()->name }}</h2>
                        <p class="text-xs text-gray-500 truncate w-full">{{ auth()->user()->email }}</p>
                    </div>

                    <nav class="p-2 space-y-1">
                        @foreach([
                            'overview' => ['label' => 'Overview', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                            'orders' => ['label' => 'My Orders', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                            'addresses' => ['label' => 'Addresses', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                            'profile' => ['label' => 'Profile Settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                        ] as $key => $item)
                            <button wire:click="switchTab('{{ $key }}')"
                                    class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 text-sm font-medium
                                    {{ $activeTab === $key 
                                        ? 'bg-primary text-white shadow-md' 
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                <svg class="w-5 h-5 {{ $activeTab === $key ? 'text-white' : 'text-gray-400 group-hover:text-primary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                                </svg>
                                <span>{{ $item['label'] }}</span>
                            </button>
                        @endforeach
                        
                        <form method="POST" action="{{ route('logout') }}" class="mt-2 pt-2 border-t border-gray-100">
                            @csrf
                            <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </nav>
                </div>
            </aside>

            <main class="flex-1 min-w-0">
                
                @if($activeTab === 'overview')
                    <div class="space-y-6 animate-fade-in">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center">
                                <div class="p-3 rounded-full bg-blue-50 text-primary mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold">Total Orders</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center">
                                <div class="p-3 rounded-full bg-orange-50 text-orange-600 mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold">Pending</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_orders'] }}</p>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center">
                                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold">Total Spent</p>
                                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_spent'], 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="font-bold text-gray-900">Recent Orders</h3>
                                <button wire:click="switchTab('orders')" class="text-sm text-primary hover:underline">View All</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-6 py-3">Order #</th>
                                            <th class="px-6 py-3">Date</th>
                                            <th class="px-6 py-3">Status</th>
                                            <th class="px-6 py-3 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($recentOrders as $order)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 font-medium text-gray-900">{{ $order->order_number }}</td>
                                                <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold 
                                                        {{ $order->status == 'completed' ? 'bg-green-100 text-green-700' : 
                                                          ($order->status == 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600') }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right font-bold text-gray-900">${{ number_format($order->total, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No orders yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'orders')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-bold text-gray-900">Order History</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-3">Order #</th>
                                        <th class="px-6 py-3">Date</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Items</th>
                                        <th class="px-6 py-3 text-right">Total</th>
                                        <th class="px-6 py-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($orders as $order)
                                        <tr class="hover:bg-gray-50 group">
                                            <td class="px-6 py-4 font-medium text-gray-900">{{ $order->order_number }}</td>
                                            <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold 
                                                    {{ $order->status == 'completed' ? 'bg-green-100 text-green-700' : 
                                                      ($order->status == 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $order->items ? $order->items->count() : 0 }} Items
                                            </td>
                                            <td class="px-6 py-4 text-right font-bold text-gray-900">${{ number_format($order->total, 2) }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <button wire:click="viewOrder({{ $order->id }})" class="text-primary hover:text-blue-700 font-medium text-xs border border-primary/20 hover:bg-primary/5 px-3 py-1.5 rounded transition">
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">You haven't placed any orders yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(method_exists($orders, 'links'))
                            <div class="p-4 border-t border-gray-100">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </div>
                @endif

                @if($activeTab === 'addresses')
                    <div class="space-y-6 animate-fade-in">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-gray-900 text-lg">Saved Addresses</h3>
                            <button class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                                + Add New
                            </button>
                        </div>
                        
                        @if (session()->has('address_success'))
                            <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm">{{ session('address_success') }}</div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($addresses as $addr)
                                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative group hover:border-primary/50 transition-colors">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-2 py-1 rounded">{{ $addr->type ?? 'Address' }}</span>
                                        <button wire:click="deleteAddress({{ $addr->id }})" wire:confirm="Are you sure you want to delete this address?" class="text-gray-400 hover:text-red-500 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <p class="font-bold text-gray-900">{{ $addr->name }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $addr->address_line1 }}</p>
                                    <p class="text-sm text-gray-600">{{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}</p>
                                    <p class="text-xs text-gray-400 mt-2 uppercase tracking-wide">{{ $addr->country_code }}</p>
                                </div>
                            @empty
                                <div class="col-span-2 text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                                    <p class="text-gray-500">No addresses saved.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                @if($activeTab === 'profile')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 animate-fade-in max-w-2xl">
                        <h3 class="font-bold text-gray-900 text-lg mb-6">Profile Settings</h3>

                        @if (session()->has('profile_success'))
                            <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm mb-6 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ session('profile_success') }}
                            </div>
                        @endif

                        <form wire:submit="updateProfile" class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                                <input wire:model="name" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                                <input wire:model="email" type="email" disabled class="w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm text-gray-500 cursor-not-allowed">
                                <p class="text-xs text-gray-400 mt-1">Email cannot be changed.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Phone Number</label>
                                <input wire:model="phone" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary">
                                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="pt-4 border-t border-gray-100">
                                <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg font-bold shadow-md hover:bg-blue-700 transition">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

            </main>
        </div>
    </div>

    @if($showOrderModal && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-transition>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="$wire.set('showOrderModal', false)">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Order #{{ $selectedOrder->order_number }}</h3>
                        <p class="text-xs text-gray-500">{{ $selectedOrder->created_at->format('F j, Y g:i A') }}</p>
                    </div>
                    <button wire:click="$set('showOrderModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="space-y-4">
                        @foreach($selectedOrder->items as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg mr-4 overflow-hidden border border-gray-200">
                                        </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $item->name }}</p>
                                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-bold text-gray-900">${{ number_format($item->price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($selectedOrder->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Shipping</span>
                            <span>${{ number_format($selectedOrder->shipping_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-dashed border-gray-200">
                            <span>Total</span>
                            <span class="text-primary">${{ number_format($selectedOrder->total, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-bold text-gray-900 mb-1">Billing Address</p>
                            <p class="text-gray-600">
                                {{ $selectedOrder->addresses->where('type', 'billing')->first()->address_line1 ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 mb-1">Payment Method</p>
                            <p class="text-gray-600 uppercase">{{ $selectedOrder->payment_method }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
