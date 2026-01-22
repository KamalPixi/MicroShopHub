@extends('layouts.app')

@section('title', 'ShopHub - Home')

@section('content')

<!-- Shop by Category -->
@include('partials.category-home')
@include('partials.featured-products')
@include('partials.new-arrivals')

<!-- Newsletter -->
<section class="bg-primary rounded-lg p-8 text-center text-white mb-12">
    <h2 class="text-2xl font-bold mb-2">Stay Updated</h2>
    <p class="text-blue-100 mb-6">Get the latest deals and new product announcements</p>
    <div class="max-w-md mx-auto flex gap-3">
        <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none">
        <button class="bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">Subscribe</button>
    </div>
</section>

@endsection
