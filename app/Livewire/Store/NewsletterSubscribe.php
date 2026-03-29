<?php

namespace App\Livewire\Store;

use App\Models\NewsletterSubscription;
use Illuminate\Support\Carbon;
use Livewire\Component;

class NewsletterSubscribe extends Component
{
    public $email = '';
    public $name = '';
    public $successMessage = '';
    public $errorMessage = '';

    protected function rules()
    {
        return [
            'email' => 'required|email:rfc,dns|max:255',
            'name' => 'nullable|string|max:120',
        ];
    }

    public function subscribe()
    {
        $this->errorMessage = '';
        $validated = $this->validate();

        $subscription = NewsletterSubscription::where('email', $validated['email'])->first();

        if ($subscription) {
            if ($subscription->status === 'subscribed') {
                $this->errorMessage = __('store.already_subscribed');
                return;
            }
            $subscription->update([
                'name' => $validated['name'] ?? $subscription->name,
                'status' => 'subscribed',
                'subscribed_at' => $subscription->subscribed_at ?? Carbon::now(),
                'unsubscribed_at' => null,
                'source' => $subscription->source ?? 'home',
            ]);
        } else {
            NewsletterSubscription::create([
                'email' => $validated['email'],
                'name' => $validated['name'] ?? null,
                'status' => 'subscribed',
                'subscribed_at' => Carbon::now(),
                'unsubscribed_at' => null,
                'source' => 'home',
            ]);
        }

        $this->successMessage = __('store.newsletter_success');
        $this->reset('email', 'name');
    }

    public function render()
    {
        return view('livewire.store.newsletter-subscribe');
    }
}
