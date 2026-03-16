<?php

namespace App\Livewire\Admin;

use App\Models\NewsletterSubscription;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterSubscriptions extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $subscription = NewsletterSubscription::findOrFail($id);

        if ($subscription->status === 'subscribed') {
            $subscription->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);
        } else {
            $subscription->update([
                'status' => 'subscribed',
                'subscribed_at' => $subscription->subscribed_at ?? now(),
                'unsubscribed_at' => null,
            ]);
        }

        session()->flash('message', 'Subscription status updated.');
    }

    public function deleteSubscription($id)
    {
        NewsletterSubscription::findOrFail($id)->delete();
        session()->flash('message', 'Subscription deleted.');
    }

    public function render()
    {
        $query = NewsletterSubscription::query()
            ->when($this->search, function ($q) {
                $q->where('email', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->orderBy('created_at', 'desc');

        return view('livewire.admin.newsletter-subscriptions', [
            'subscriptions' => $query->paginate($this->perPage),
            'totalCount' => NewsletterSubscription::count(),
            'subscribedCount' => NewsletterSubscription::where('status', 'subscribed')->count(),
            'unsubscribedCount' => NewsletterSubscription::where('status', 'unsubscribed')->count(),
        ]);
    }
}
