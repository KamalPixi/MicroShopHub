<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Setting;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class OrderShow extends Component
{
    public Order $order;
    public int $customerOrderCount = 0;
    public float $customerTotalSpend = 0.0;
    public string $emailSubject = '';
    public string $emailMessage = '';
    public string $statusSelection = '';
    public bool $notifyCustomer = false;

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

    public function saveOrderAction(): void
    {
        if (! $this->statusSelection) {
            $this->addError('statusSelection', 'Please select a status.');
            return;
        }

        if (! in_array($this->statusSelection, ['pending', 'processing', 'delivered', 'cancelled'], true)) {
            $this->addError('statusSelection', 'Invalid status selected.');
            return;
        }

        $this->order->update([
            'status' => $this->statusSelection,
        ]);

        if ($this->notifyCustomer) {
            $subject = "Order #{$this->order->order_number} status updated";
            $message = "Hello,\n\nYour order status is now: ".ucfirst($this->statusSelection).".\n\nThank you.";
            $this->sendCustomerEmailInternal($subject, $message);
        }

        $this->order->refresh();
        $this->statusSelection = '';
        $this->notifyCustomer = false;
        session()->flash('message', 'Order action saved.');
    }

    public function sendCustomerEmail(): void
    {
        $validated = $this->validate([
            'emailSubject' => 'required|string|max:150',
            'emailMessage' => 'required|string|max:2000',
        ]);

        $toEmail = $this->order->user?->email ?? $this->order->billingAddress?->email;
        if (! $toEmail) {
            $this->addError('emailSubject', 'Customer email is not available for this order.');
            return;
        }

        $this->sendCustomerEmailInternal($validated['emailSubject'], $validated['emailMessage']);

        $this->emailSubject = '';
        $this->emailMessage = '';
        session()->flash('message', 'Email sent to customer.');
    }

    protected function sendCustomerEmailInternal(string $subject, string $messageBody): void
    {
        $toEmail = $this->order->user?->email ?? $this->order->billingAddress?->email;
        if (! $toEmail) {
            return;
        }

        $fromAddress = Setting::where('key', 'mail_from_address')->value('value') ?: config('mail.from.address');
        $fromName = Setting::where('key', 'mail_from_name')->value('value') ?: config('mail.from.name');

        Mail::raw($messageBody, function ($message) use ($toEmail, $subject, $fromAddress, $fromName) {
            if ($fromAddress) {
                $message->from($fromAddress, $fromName ?: null);
            }
            $message->to($toEmail);
            $message->subject($subject);
        });
    }

    public function render()
    {
        return view('livewire.admin.order-show');
    }
}
