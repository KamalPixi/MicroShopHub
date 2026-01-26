<div class="bg-gray-50 min-h-screen font-sans">
    
    <div class="md:hidden bg-white border-b border-gray-200 p-4 sticky top-0 z-10">
        <h1 class="text-lg font-bold text-gray-900">My Account</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                    
                    <div class="p-6 border-b border-gray-100 flex flex-col items-center text-center bg-gradient-to-b from-white to-gray-50">
                        <div class="relative mb-3">
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
                    </div>

                    <nav class="p-2 space-y-1">
                        @foreach([
                            'overview' => ['label' => 'Overview', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                            'orders' => ['label' => 'My Orders', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                            'addresses' => ['label' => 'Address Book', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                            'profile' => ['label' => 'Profile Settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                        ] as $key => $item)
                            <button wire:click="switchTab('{{ $key }}')"
                                    class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 text-sm font-medium
                                    {{ $activeTab === $key 
                                        ? 'bg-primary text-white shadow-md' 
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-primary' }}">
                                <svg class="w-5 h-5 {{ $activeTab === $key ? 'text-white' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                                </svg>
                                <span>{{ $item['label'] }}</span>
                            </button>
                        @endforeach
                        
                        <form method="POST" action="{{ route('logout') }}" class="mt-2 pt-2 border-t border-gray-100">
                            @csrf
                            <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 transition-colors">
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
                        
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-bold text-gray-900 mb-4">My Orders</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <button wire:click="switchOrderTab('to_pay')" class="flex flex-col items-center justify-center p-4 rounded-lg bg-gray-50 hover:bg-blue-50 transition group">
                                    <div class="relative mb-2">
                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        @if($stats['pending_payment'] > 0)
                                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stats['pending_payment'] }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 group-hover:text-primary">To Pay</span>
                                </button>

                                <button wire:click="switchOrderTab('to_ship')" class="flex flex-col items-center justify-center p-4 rounded-lg bg-gray-50 hover:bg-blue-50 transition group">
                                    <div class="relative mb-2">
                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        @if($stats['to_ship'] > 0)
                                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stats['to_ship'] }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 group-hover:text-primary">To Ship</span>
                                </button>

                                <button wire:click="switchOrderTab('to_receive')" class="flex flex-col items-center justify-center p-4 rounded-lg bg-gray-50 hover:bg-blue-50 transition group">
                                    <div class="relative mb-2">
                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                        @if($stats['to_receive'] > 0)
                                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stats['to_receive'] }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 group-hover:text-primary">To Receive</span>
                                </button>

                                <button wire:click="switchOrderTab('completed')" class="flex flex-col items-center justify-center p-4 rounded-lg bg-gray-50 hover:bg-blue-50 transition group">
                                    <div class="mb-2">
                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 group-hover:text-primary">Completed</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="font-bold text-gray-900">Recent Orders</h3>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @forelse($recentOrders as $order)
                                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">Order #{{ $order->order_number }}</p>
                                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-primary">${{ number_format($order->total, 2) }}</p>
                                            <p class="text-xs font-medium capitalize {{ $order->status == 'completed' ? 'text-green-600' : 'text-orange-500' }}">{{ $order->status }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-500 text-sm">No recent activity.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'orders')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden min-h-[500px]">
                        
                        <div class="flex overflow-x-auto border-b border-gray-100 no-scrollbar">
                            @foreach(['all' => 'All', 'to_pay' => 'To Pay', 'to_ship' => 'To Ship', 'to_receive' => 'To Receive', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
                                <button wire:click="switchOrderTab('{{ $key }}')"
                                        class="px-6 py-4 text-sm font-bold whitespace-nowrap border-b-2 transition-colors
                                        {{ $activeOrderTab === $key ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        <div class="p-0">
                            @forelse($orders as $order)
                                <div class="p-6 border-b border-gray-100 hover:bg-gray-50 transition">
                                    <div class="flex flex-col md:flex-row justify-between mb-4">
                                        <div class="flex items-center gap-2 mb-2 md:mb-0">
                                            <span class="font-bold text-gray-900">#{{ $order->order_number }}</span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase
                                                {{ $order->status == 'completed' ? 'bg-green-100 text-green-700' : 
                                                  ($order->status == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                                                {{ $order->status }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-3 mb-4">
                                        @foreach($order->items->take(2) as $item)
                                            <div class="flex items-center gap-4">
                                                <div class="w-14 h-14 bg-gray-100 rounded border border-gray-200 overflow-hidden flex-shrink-0">
                                                    </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $item->name }}</p>
                                                    <p class="text-xs text-gray-500">x{{ $item->quantity }}</p>
                                                </div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($item->price, 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 2)
                                            <p class="text-xs text-gray-500 pl-18">and {{ $order->items->count() - 2 }} more items...</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between pt-2 border-t border-dashed border-gray-200">
                                        <div class="text-sm">
                                            Total: <span class="font-bold text-xl text-primary">${{ number_format($order->total, 2) }}</span>
                                        </div>
                                        <div class="flex gap-2">
                                            @if($order->status == 'pending')
                                                <button class="px-4 py-2 bg-primary text-white text-xs font-bold rounded hover:bg-blue-700 transition">Pay Now</button>
                                            @endif
                                            <button wire:click="viewOrder({{ $order->id }})" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded hover:bg-gray-50 transition">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center py-20">
                                    <div class="bg-gray-50 p-4 rounded-full mb-4">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No orders found.</p>
                                </div>
                            @endforelse
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($addresses as $addr)
                                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative group hover:border-primary/50 transition-colors">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-2 py-1 rounded">{{ $addr->type ?? 'Address' }}</span>
                                        <button wire:click="deleteAddress({{ $addr->id }})" wire:confirm="Delete?" class="text-gray-400 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </div>
                                    <p class="font-bold text-gray-900">{{ $addr->name }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $addr->address_line1 }}</p>
                                    <p class="text-sm text-gray-600">{{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}</p>
                                </div>
                            @empty
                                <div class="col-span-2 text-center py-10 bg-white rounded-xl border border-dashed border-gray-300"><p class="text-gray-500">No addresses saved.</p></div>
                            @endforelse
                        </div>
                    </div>
                @endif

                @if($activeTab === 'profile')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 animate-fade-in">
                        <h3 class="font-bold text-gray-900 text-lg mb-6 pb-4 border-b border-gray-100">My Profile</h3>

                        @if (session()->has('profile_success'))
                            <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm mb-6">{{ session('profile_success') }}</div>
                        @endif

                        <form wire:submit="updateProfile" class="space-y-8">
                            
                            <div class="flex items-center gap-6">
                                <div class="relative group">
                                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 ring-4 ring-gray-50">
                                        @if($avatar)
                                            <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                                        @elseif($existingAvatar)
                                            <img src="{{ Storage::url($existingAvatar) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <label class="absolute bottom-0 right-0 bg-white rounded-full p-1.5 shadow border border-gray-200 cursor-pointer hover:text-primary transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <input type="file" wire:model="avatar" class="hidden" accept="image/*">
                                    </label>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">Profile Photo</h4>
                                    <p class="text-xs text-gray-500 mt-1">Accepts PNG, JPG or JPEG. Max 1MB.</p>
                                    @error('avatar') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                                    <input wire:model="name" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary h-10">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                    <input value="{{ $email }}" type="email" disabled class="w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm text-gray-500 cursor-not-allowed h-10">
                                    <span class="text-[10px] text-primary cursor-pointer hover:underline float-right mt-1">Change Email</span>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Phone Number</label>
                                    <input wire:model="phone" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary h-10">
                                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Gender</label>
                                    <select wire:model="gender" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary h-10">
                                        <option value="">Select Gender</option>
                                        <option value="1">Male</option>
                                        <option value="2">Female</option>
                                        <option value="3">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Date of Birth</label>
                                    <input wire:model="birthday" type="date" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary h-10">
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-100 flex justify-end">
                                <button type="submit" class="bg-primary text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:bg-blue-700 hover:shadow-xl transition transform active:scale-95">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="$wire.set('showOrderModal', false)">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-bold text-gray-900">Order #{{ $selectedOrder->order_number }}</h3>
                    <button wire:click="$set('showOrderModal', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6 space-y-6">
                    <div class="space-y-4">
                        @foreach($selectedOrder->items as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg mr-4 overflow-hidden border border-gray-200"></div>
                                    <div><p class="text-sm font-bold text-gray-900">{{ $item->name }}</p><p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p></div>
                                </div>
                                <p class="text-sm font-bold text-gray-900">${{ number_format($item->price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-100 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600"><span>Subtotal</span><span>${{ number_format($selectedOrder->subtotal, 2) }}</span></div>
                        <div class="flex justify-between text-sm text-gray-600"><span>Shipping</span><span>${{ number_format($selectedOrder->shipping_cost, 2) }}</span></div>
                        <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-dashed border-gray-200"><span>Total</span><span class="text-primary">${{ number_format($selectedOrder->total, 2) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
