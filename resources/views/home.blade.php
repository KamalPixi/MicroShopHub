@extends('layouts.app')

@section('title', 'ShopHub - Home')

@section('content')

<!-- Shop by Category -->
<section class="mb-12">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Shop by Category</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Category Card -->
        <div class="relative overflow-hidden rounded-lg bg-white shadow-md hover:shadow-lg transition-shadow cursor-pointer group">
            <div class="aspect-w-16 aspect-h-9">
                <img src="https://placehold.co/400x225" alt="Fashion & Clothing" class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary transition-colors">Fashion & Clothing</h3>
                <p class="text-sm text-gray-600 mt-1">Trendy outfits for all seasons</p>
            </div>
        </div>

        <!-- Category Card -->
        <div class="relative overflow-hidden rounded-lg bg-white shadow-md hover:shadow-lg transition-shadow cursor-pointer group">
            <div class="aspect-w-16 aspect-h-9">
                <img src="https://placehold.co/400x225" alt="Health & Medicine" class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary transition-colors">Health & Medicine</h3>
                <p class="text-sm text-gray-600 mt-1">Quality healthcare products</p>
            </div>
        </div>

        <!-- Category Card -->
        <div class="relative overflow-hidden rounded-lg bg-white shadow-md hover:shadow-lg transition-shadow cursor-pointer group">
            <div class="aspect-w-16 aspect-h-9">
                <img src="https://placehold.co/400x225" alt="Handmade Crafts" class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary transition-colors">Handmade Crafts</h3>
                <p class="text-sm text-gray-600 mt-1">Unique artisanal products</p>
            </div>
        </div>

    </div>
</section>

<!-- Featured Products -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
        <a href="#" class="text-primary font-medium hover:text-blue-700">View All →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">

        <!-- Product Cards -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group cursor-pointer">
            <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                <img src="https://placehold.co/500x500" alt="Cotton T-Shirt" class="w-full h-full object-cover">
            </div>
            <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover:text-primary">Cotton T-Shirt</h3>
            <p class="text-xs text-gray-600 mb-2">Comfortable daily wear</p>
            <div class="flex items-center justify-between">
                <span class="font-bold text-primary">$29.99</span>
                @livewire('add-to-cart-button')
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group cursor-pointer">
            <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                <img src="https://placehold.co/500x500" alt="Vitamin D3" class="w-full h-full object-cover">
            </div>
            <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover:text-primary">Vitamin D3</h3>
            <p class="text-xs text-gray-600 mb-2">60 capsules</p>
            <div class="flex items-center justify-between">
                <span class="font-bold text-primary">$19.99</span>
                @livewire('add-to-cart-button')
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group cursor-pointer">
            <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                <img src="https://placehold.co/500x500" alt="Handmade Scarf" class="w-full h-full object-cover">
            </div>
            <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover:text-primary">Handmade Scarf</h3>
            <p class="text-xs text-gray-600 mb-2">Wool knitted</p>
            <div class="flex items-center justify-between">
                <span class="font-bold text-primary">$45.00</span>
                @livewire('add-to-cart-button')
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group cursor-pointer">
            <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                <img src="https://placehold.co/500x500" alt="Denim Jacket" class="w-full h-full object-cover">
            </div>
            <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover:text-primary">Denim Jacket</h3>
            <p class="text-xs text-gray-600 mb-2">Classic fit</p>
            <div class="flex items-center justify-between">
                <span class="font-bold text-primary">$79.99</span>
                @livewire('add-to-cart-button')
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group cursor-pointer">
            <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                <img src="https://placehold.co/500x500" alt="Clay Pottery" class="w-full h-full object-cover">
            </div>
            <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover:text-primary">Clay Pottery</h3>
            <p class="text-xs text-gray-600 mb-2">Handcrafted vase</p>
            <div class="flex items-center justify-between">
                <span class="font-bold text-primary">$35.00</span>
                @livewire('add-to-cart-button')
            </div>
        </div>

    </div>
</section>

<!-- New Arrivals -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">New Arrivals</h2>
        <a href="#" class="text-primary font-medium hover:text-blue-700">View All →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group cursor-pointer">
            <div class="aspect-square overflow-hidden">
                <img src="https://placehold.co/500x500" alt="Summer Dress" class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-primary">Summer Dress</h3>
                <p class="text-sm text-gray-600 mb-2">Light and breezy fabric</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary text-lg">$59.99</span>
                    @livewire('add-to-cart-button')
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group cursor-pointer">
            <div class="aspect-square overflow-hidden">
                <img src="https://placehold.co/500x500" alt="Herbal Tea" class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-primary">Herbal Tea</h3>
                <p class="text-sm text-gray-600 mb-2">Organic blend, 20 bags</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary text-lg">$14.99</span>
                    @livewire('add-to-cart-button')
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group cursor-pointer">
            <div class="aspect-square overflow-hidden">
                <img src="https://placehold.co/500x500" alt="Knitted Blanket" class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-primary">Knitted Blanket</h3>
                <p class="text-sm text-gray-600 mb-2">Soft wool, queen size</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary text-lg">$89.99</span>
                    @livewire('add-to-cart-button')
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group cursor-pointer">
            <div class="aspect-square overflow-hidden">
                <img src="https://placehold.co/500x500" alt="Running Shoes" class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-primary">Running Shoes</h3>
                <p class="text-sm text-gray-600 mb-2">Lightweight, breathable</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary text-lg">$99.99</span>
                    @livewire('add-to-cart-button')
                </div>
            </div>
        </div>

    </div>
</section>

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
