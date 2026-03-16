<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;

class OrderShow extends Component
{
    public Order $order;
    public int $customerOrderCount = 0;
    public float $customerTotalSpend = 0.0;

    public function mount(int $id): void
    {
        $this->order = Order::query()->with([
            'user',
            'items.product',
            'items.productVariation',
            'currency',
            'shippingMethod',
            'billingAddress',
            'shippingAddress',
        ])->findOrFail($id);

        if ($this->order->user_id) {
            $this->customerOrderCount = Order::where('user_id', $this->order->user_id)->count();
            $this->customerTotalSpend = (float) Order::where('user_id', $this->order->user_id)->sum('total');
        }
    }

    public function updateStatus(string $status): void
    {
        if (! in_array($status, ['pending', 'processing', 'delivered', 'cancelled'], true)) {
            return;
        }

        $this->order->update([
            'status' => $status,
        ]);

        $this->order->refresh();
        session()->flash('message', 'Order status updated.');
    }

    public function render()
    {
        return view('livewire.admin.order-show');
    }
}
