<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Address;

class Dashboard extends Component
{
    public $activeTab = 'overview'; // overview, orders, addresses, profile
    public $user;
    
    // Profile Form Data
    public $name;
    public $email;
    public $phone;

    // Order View Modal
    public $selectedOrder = null;
    public $showOrderModal = false;

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        
        // Handle query parameter for deep linking (e.g. ?tab=orders)
        $this->activeTab = request()->query('tab', 'overview');
    }

    // --- Actions ---

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $this->user->update([
            'name' => $this->name,
            'phone' => $this->phone,
        ]);

        session()->flash('profile_success', 'Profile updated successfully.');
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with('items')->find($orderId); // Ensure you have 'items' relation
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

    // --- Render ---

    public function render()
    {
        return view('livewire.customer.dashboard', [
            'orders' => $this->activeTab === 'orders' ? $this->user->orders()->latest()->paginate(10) : [],
            'addresses' => $this->activeTab === 'addresses' ? $this->user->addresses()->latest()->get() : [],
            'recentOrders' => $this->activeTab === 'overview' ? $this->user->orders()->latest()->take(5)->get() : [],
            'stats' => $this->activeTab === 'overview' ? [
                'total_orders' => $this->user->orders()->count(),
                'total_spent' => $this->user->orders()->sum('total'),
                'pending_orders' => $this->user->orders()->where('status', 'pending')->count(),
            ] : []
        ]);
    }
}
