<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customersQuery = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->with('defaultAddress')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderBy('created_at', 'desc');

        $statsBase = User::query();
        $todayBase = User::query()->whereDate('created_at', Carbon::today());
        $thisMonthBase = User::query()->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);

        $stats = [
            'total_customers' => (clone $statsBase)->count(),
            'total_orders_all_time' => Order::query()->count(),
            'new_today' => (clone $todayBase)->count(),
            'new_this_month' => (clone $thisMonthBase)->count(),
            'with_orders' => (clone $statsBase)->has('orders')->count(),
            'without_orders' => (clone $statsBase)->doesntHave('orders')->count(),
            'repeat_customers' => (clone $statsBase)->has('orders', '>=', 2)->count(),
            'verified_emails' => (clone $statsBase)->whereNotNull('email_verified_at')->count(),
            'total_lifetime_spend' => Order::query()->sum('total'),
            'avg_orders_per_customer' => (clone $statsBase)->count() > 0
                ? round(Order::query()->count() / (clone $statsBase)->count(), 2)
                : 0,
        ];

        $customers = $customersQuery->paginate($this->perPage);

        return view('livewire.admin.customer-list', [
            'customers' => $customers,
            'stats' => $stats,
        ]);
    }

    public function deleteCustomer($customerId)
    {
        User::findOrFail($customerId)->delete();
        session()->flash('message', 'Customer deleted successfully.');
    }
}
