<?php

namespace App\Livewire\Store\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\Address;

class Dashboard extends Component
{
    use WithFileUploads;

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
    }

    // --- Tab Switching ---

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function switchOrderTab($status)
    {
        $this->activeOrderTab = $status;
        $this->activeTab = 'orders'; // Ensure we are on the orders tab
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

        session()->flash('profile_success', 'Profile updated successfully.');
    }

    // --- Order Logic ---

    public function getOrdersProperty()
    {
        $query = $this->user->orders()->latest();

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
                $query->where('status', 'completed');
                break;
            case 'cancelled':
                $query->whereIn('status', ['cancelled', 'refunded']);
                break;
        }

        return $query->paginate(10);
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with('items')->find($orderId);
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

    public function render()
    {
        return view('livewire.store.customer.dashboard', [
            'orders' => $this->activeTab === 'orders' ? $this->orders : [], // Uses the computed property above
            'addresses' => $this->activeTab === 'addresses' ? $this->user->addresses()->latest()->get() : [],
            'recentOrders' => $this->user->orders()->latest()->take(5)->get(),
            'stats' => [
                'total_orders' => $this->user->orders()->count(),
                'pending_payment' => $this->user->orders()->where('status', 'pending')->where('payment_status', 'pending')->count(),
                'to_ship' => $this->user->orders()->where('status', 'processing')->count(),
                'to_receive' => $this->user->orders()->where('status', 'shipped')->count(),
            ]
        ]);
    }
}
