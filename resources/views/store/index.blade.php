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
    <livewire:store.newsletter-subscribe />
</section>

@endsection
