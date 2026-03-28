<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
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
        $ordersQuery = Order::query()
            ->with(['user', 'currency'])
            ->when($this->search, function ($query) {
                $query->where('id', 'like', '%' . $this->search . '%')
                      ->orWhere('status', 'like', '%' . $this->search . '%')
                      ->orWhere('currency_code', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            });

        $statsBase = Order::query();
        $todayBase = Order::query()->whereDate('created_at', now()->toDateString());

        $stats = [
            'total_orders' => (clone $statsBase)->count(),
            'pending_payment' => (clone $statsBase)->whereIn('payment_status', ['pending', 'pending_verification'])->count(),
            'to_process' => (clone $statsBase)->where('status', 'processing')->count(),
            'shipped' => (clone $statsBase)->where('status', 'shipped')->count(),
            'delivered' => (clone $statsBase)->where('status', 'delivered')->count(),
            'cancelled' => (clone $statsBase)->whereIn('status', ['cancelled', 'refunded'])->count(),
            'today_total_orders' => (clone $todayBase)->count(),
            'today_pending_payment' => (clone $todayBase)->whereIn('payment_status', ['pending', 'pending_verification'])->count(),
            'today_to_process' => (clone $todayBase)->where('status', 'processing')->count(),
            'today_shipped' => (clone $todayBase)->where('status', 'shipped')->count(),
            'today_delivered' => (clone $todayBase)->where('status', 'delivered')->count(),
            'today_cancelled' => (clone $todayBase)->whereIn('status', ['cancelled', 'refunded'])->count(),
        ];

        $orders = $ordersQuery
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.order-list', [
            'orders' => $orders,
            'stats' => $stats,
        ]);
    }

    public function deleteOrder($orderId)
    {
        Order::findOrFail($orderId)->delete();
        session()->flash('message', 'Order deleted successfully.');
    }
}
