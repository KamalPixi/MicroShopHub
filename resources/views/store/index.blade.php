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
    <p class="text-blue-100 mb-4">Get the latest deals and new product announcements</p>
    <div class="max-w-md mx-auto flex gap-3">
        <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none">
        <button class="bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">Subscribe</button>
    </div>
</section>

@endsection
