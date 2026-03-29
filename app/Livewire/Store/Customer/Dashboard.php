<?php

namespace App\Livewire\Store\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\Address;
use App\Models\Currency;

class Dashboard extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Navigation
    public $activeTab = 'overview'; 
    public $activeOrderTab = 'all'; // all, to_pay, to_ship, to_receive, completed, cancelled

    public $user;
    
    // Profile Form Data
    public $name;
    public $email;
    public $phone;
    public $gender;
    public $birthday;
    public $avatar; // Temporary upload
    public $existingAvatar;

    // Order View Modal
    public $selectedOrder = null;
    public $showOrderModal = false;
    public $currencySymbol = '$';
    public $currencyCode = 'BDT';
    public $showAddressForm = false;
    public array $addressTypeOptions = [];
    public $newAddress = [
        'type' => 'home',
        'name' => '',
        'phone' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'country' => '',
        'is_default' => false,
    ];

    public function mount()
    {
        $this->user = Auth::user();
        
        // Initialize Profile Data
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->gender = $this->user->gender;
        $this->birthday = $this->user->birthday;
        $this->existingAvatar = $this->user->avatar;
        
        // Handle deep linking
        $this->activeTab = request()->query('tab', 'overview');
        $this->activeOrderTab = request()->query('status', 'all');

        $currency = Currency::getActive();
        $this->currencySymbol = $currency?->symbol ?? '$';
        $this->currencyCode = $currency?->code ?? 'BDT';
        $this->addressTypeOptions = $this->getAddressTypeOptions();
    }

    // --- Tab Switching ---

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        if ($tab !== 'orders') {
            $this->resetPage('ordersPage');
        }
        if ($tab !== 'addresses') {
            $this->resetPage('addressesPage');
        }
    }

    public function switchOrderTab($status)
    {
        $this->activeOrderTab = $status;
        $this->activeTab = 'orders'; // Ensure we are on the orders tab
        $this->resetPage('ordersPage');
    }

    // --- Profile Logic ---

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:1,2,3',
            'birthday' => 'nullable|date',
            'avatar' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
        ];

        // Handle Avatar Upload
        if ($this->avatar) {
            // Delete old avatar if exists
            if ($this->user->avatar) {
                Storage::delete($this->user->avatar);
            }
            $path = $this->avatar->store('avatars', 'public');
            $data['avatar'] = $path;
            $this->existingAvatar = $path; // Update UI preview
        }

        $this->user->update($data);
        
        // Clear file input
        $this->avatar = null;

        session()->flash('profile_success', __('store.profile_updated_success'));
    }

    public function sendVerificationEmail(): void
    {
        if ($this->user->hasVerifiedEmail()) {
            session()->flash('message', __('store.email_already_verified'));
            return;
        }

        $cacheKey = 'customer-email-verification-sent:'.$this->user->id;
        $lastSentAt = Cache::get($cacheKey);

        if ($lastSentAt && now()->diffInSeconds($lastSentAt) < 120) {
            $wait = 120 - now()->diffInSeconds($lastSentAt);

            session()->flash('message', __('store.please_wait_seconds', ['seconds' => $wait]));
            return;
        }

        $this->user->sendEmailVerificationNotification();
        Cache::put($cacheKey, now(), now()->addMinutes(10));

        session()->flash('message', __('store.verification_link_sent'));
    }

    // --- Order Logic ---

    public function getOrdersProperty()
    {
        $query = $this->user->orders()
            ->with(['items' => function ($q) {
                $q->limit(3);
            }])
            ->latest();

        switch ($this->activeOrderTab) {
            case 'to_pay':
                $query->where('status', 'pending')->where('payment_status', 'pending');
                break;
            case 'to_ship':
                // Assuming 'paid' or 'processing' means ready to ship
                $query->where(function($q) {
                    $q->where('status', 'processing')
                      ->orWhere(function($sub) {
                          $sub->where('status', 'pending')->where('payment_status', 'paid');
                      });
                });
                break;
            case 'to_receive':
                $query->where('status', 'shipped');
                break;
            case 'completed':
                $query->where('status', 'delivered');
                break;
            case 'cancelled':
                $query->whereIn('status', ['cancelled', 'refunded']);
                break;
        }

        return $query->paginate(8, ['*'], 'ordersPage');
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with([
            'items',
            'billingAddress',
            'shippingAddress',
            'currency',
        ])->find($orderId);
        if($this->selectedOrder && $this->selectedOrder->user_id == Auth::id()) {
            $this->showOrderModal = true;
        }
    }

    public function deleteAddress($addressId)
    {
        $address = $this->user->addresses()->find($addressId);
        if ($address) {
            $address->delete();
            session()->flash('address_success', 'Address removed.');
        }
    }

    public function toggleAddressForm(): void
    {
        $this->showAddressForm = ! $this->showAddressForm;
    }

    public function addAddress(): void
    {
        $allowedTypes = array_keys($this->getAddressTypeOptions());

        $validated = $this->validate([
            'newAddress.type' => ['required', 'in:' . implode(',', $allowedTypes)],
            'newAddress.name' => 'required|string|max:255',
            'newAddress.phone' => 'nullable|string|max:50',
            'newAddress.address_line1' => 'required|string|max:255',
            'newAddress.address_line2' => 'nullable|string|max:255',
            'newAddress.city' => 'required|string|max:100',
            'newAddress.state' => 'nullable|string|max:100',
            'newAddress.postal_code' => 'nullable|string|max:30',
            'newAddress.country' => 'nullable|string|max:100',
            'newAddress.is_default' => 'boolean',
        ]);

        if (! empty($validated['newAddress']['is_default'])) {
            $this->user->addresses()->update(['is_default' => false]);
        }

        $this->user->addresses()->create($validated['newAddress']);

        $this->newAddress = [
            'type' => 'home',
            'name' => '',
            'phone' => '',
            'address_line1' => '',
            'address_line2' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country' => '',
            'is_default' => false,
        ];
        $this->showAddressForm = false;
        session()->flash('address_success', __('store.address_added'));
    }

    public function getAddressTypeOptions(): array
    {
        return [
            'home' => __('store.address_type_home'),
            'office' => __('store.address_type_office'),
            'billing' => __('store.address_type_billing'),
            'shipping' => __('store.address_type_shipping'),
            'other' => __('store.address_type_other'),
        ];
    }

    public function render()
    {
        $statsQuery = $this->user->orders();
        $totalOrders = (clone $statsQuery)->count();
        $pendingPayment = (clone $statsQuery)->where('status', 'pending')->where('payment_status', 'pending')->count();
        $toShip = (clone $statsQuery)->where('status', 'processing')->count();
        $toReceive = (clone $statsQuery)->where('status', 'shipped')->count();
        $completed = (clone $statsQuery)->where('status', 'delivered')->count();
        $totalSpend = (clone $statsQuery)->sum('total');
        $lastOrder = (clone $statsQuery)->latest()->first();
        $orderTabCounts = [
            'all' => $totalOrders,
            'to_pay' => $pendingPayment,
            'to_ship' => $toShip,
            'to_receive' => $toReceive,
            'completed' => $completed,
            'cancelled' => (clone $statsQuery)->whereIn('status', ['cancelled', 'refunded'])->count(),
        ];

        return view('livewire.store.customer.dashboard', [
            'orders' => $this->activeTab === 'orders' ? $this->orders : [], // Uses the computed property above
            'addresses' => $this->activeTab === 'addresses' ? $this->user->addresses()->latest()->paginate(6, ['*'], 'addressesPage') : [],
            'recentOrders' => $this->user->orders()->latest()->take(5)->get(),
            'orderTabCounts' => $orderTabCounts,
            'stats' => [
                'total_orders' => $totalOrders,
                'pending_payment' => $pendingPayment,
                'to_ship' => $toShip,
                'to_receive' => $toReceive,
                'completed' => $completed,
                'total_spend' => $totalSpend,
                'address_count' => $this->user->addresses()->count(),
                'last_order' => $lastOrder,
            ],
        ]);
    }
}
