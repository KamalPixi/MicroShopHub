<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns|max:255',
            'name' => 'nullable|string|max:120',
        ]);

        $subscription = NewsletterSubscription::where('email', $validated['email'])->first();

        if ($subscription) {
            $subscription->update([
                'name' => $validated['name'] ?? $subscription->name,
                'status' => 'subscribed',
                'subscribed_at' => $subscription->subscribed_at ?? Carbon::now(),
                'unsubscribed_at' => null,
                'source' => $subscription->source ?? 'home',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        } else {
            NewsletterSubscription::create([
                'email' => $validated['email'],
                'name' => $validated['name'] ?? null,
                'status' => 'subscribed',
                'subscribed_at' => Carbon::now(),
                'unsubscribed_at' => null,
                'source' => 'home',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        }

        return back()->with('newsletter_success', 'Thanks for subscribing. We will keep you updated with new products and offers.');
    }
}
