@extends('store.layouts.app')
@section('title', 'ShopHub - Home')

@section('content')

<!-- Shop by Category -->
@include('store.partials.category-home')
@include('store.partials.featured-products')
@include('store.partials.new-arrivals')

<!-- Newsletter -->
<section class="bg-primary rounded-lg p-6 text-center text-white mb-8">
    <h2 class="text-2xl font-bold mb-2">Stay Updated</h2>
    <p class="text-white/80 mb-4">Subscribe for new arrivals, exclusive offers, and restock alerts.</p>
    <form method="POST" action="{{ route('store.newsletter.subscribe') }}" class="max-w-md mx-auto flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="email" name="email" required placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none">
        <button type="submit" class="bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">Subscribe</button>
    </form>
    @if(session('newsletter_success'))
        <p class="mt-3 text-sm text-white/90">{{ session('newsletter_success') }}</p>
    @endif
    <p class="mt-2 text-xs text-white/70">No spam. Unsubscribe anytime.</p>
</section>

@endsection
