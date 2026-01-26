@extends('store.layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> {{-- Reduced max-width slightly for 4 columns --}}
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Search Results</h1>
                <p class="text-gray-600 mt-2 text-sm">
                    Found <span class="font-bold text-gray-900">{{ $products->total() }}</span> results 
                    @if($query) for <span class="font-semibold text-primary">"{{ $query }}"</span> @endif
                </p>
            </div>

            <div class="flex items-center bg-white rounded-lg shadow-sm border border-gray-200 px-3 py-2">
                <span class="text-sm text-gray-500 mr-2">Sort by:</span>
                <form method="GET" action="{{ route('store.search') }}">
                    @if($query) <input type="hidden" name="query" value="{{ $query }}"> @endif
                    @if($categoryId) <input type="hidden" name="category" value="{{ $categoryId }}"> @endif
                    @if($minPrice) <input type="hidden" name="min_price" value="{{ $minPrice }}"> @endif
                    @if($maxPrice) <input type="hidden" name="max_price" value="{{ $maxPrice }}"> @endif

                    <select name="sort" onchange="this.form.submit()" class="text-sm font-medium text-gray-900 bg-transparent border-none focus:ring-0 cursor-pointer">
                        <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_low" {{ $sort == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ $sort == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <aside class="w-full lg:w-64 flex-shrink-0 space-y-8">
                <form method="GET" action="{{ route('store.search') }}">
                    @if($query) <input type="hidden" name="query" value="{{ $query }}"> @endif
                    @if($categoryId) <input type="hidden" name="category" value="{{ $categoryId }}"> @endif
                    @if($sort) <input type="hidden" name="sort" value="{{ $sort }}"> @endif

                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="font-bold text-gray-900 mb-4 uppercase text-xs tracking-wider">Price Range</h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                                    <input type="number" name="min_price" value="{{ $minPrice }}" placeholder="Min" 
                                           class="w-full pl-6 pr-2 py-2 text-sm border border-gray-300 rounded focus:ring-primary focus:border-primary">
                                </div>
                                <span class="text-gray-400">-</span>
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">$</span>
                                    <input type="number" name="max_price" value="{{ $maxPrice }}" placeholder="Max" 
                                           class="w-full pl-6 pr-2 py-2 text-sm border border-gray-300 rounded focus:ring-primary focus:border-primary">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-gray-900 text-white py-2 rounded text-sm font-medium hover:bg-black transition">
                                Apply Price
                            </button>
                        </div>
                    </div>
                </form>

                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 uppercase text-xs tracking-wider">Categories</h3>
                        @if($categoryId)
                            <a href="{{ route('store.search', request()->except('category', 'page')) }}" class="text-xs text-red-500 hover:underline">Clear</a>
                        @endif
                    </div>
                    
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('store.search', request()->except('category', 'page')) }}" 
                               class="block text-sm {{ request('category') == '' ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary' }}">
                                All Categories
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ request()->fullUrlWithQuery(['category' => $cat->id, 'page' => null]) }}" 
                                   class="block text-sm {{ request('category') == $cat->id ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary' }}">
                                    {{ $cat->name }}
                                </a>
                                @if($cat->children->isNotEmpty())
                                    <ul class="ml-4 mt-1 space-y-1 border-l border-gray-200 pl-2">
                                        @foreach($cat->children as $child)
                                            <li>
                                                <a href="{{ request()->fullUrlWithQuery(['category' => $child->id, 'page' => null]) }}" 
                                                   class="block text-xs {{ request('category') == $child->id ? 'text-primary font-bold' : 'text-gray-500 hover:text-primary' }}">
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <div class="flex-1">
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group border border-gray-100 flex flex-col">
                            
                            <a href="{{ route('store.product.show', $product->slug) }}" class="block aspect-square relative overflow-hidden bg-gray-100">
                                @php
                                    $img = 'https://placehold.co/500?text=No+Image';
                                    if ($product->thumbnail) {
                                        $img = Str::startsWith($product->thumbnail, ['http','https']) 
                                            ? $product->thumbnail 
                                            : Storage::url($product->thumbnail);
                                    }
                                @endphp
                                <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                
                                @if($product->price < 50) 
                                    <span class="absolute top-2 left-2 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded">DEAL</span>
                                @endif
                            </a>
                            
                            <div class="p-3 flex-1 flex flex-col">
                                <a href="{{ route('store.product.show', $product->slug) }}" class="block">
                                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-primary truncate mb-1">
                                        {{ $product->name }}
                                    </h3>
                                </a>
                                
                                <p class="text-xs text-gray-500 mb-2 truncate">
                                    {{ $product->categories->first()->name ?? 'General' }}
                                </p>
                                
                                <div class="mt-auto flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                    </div>
                                    @livewire('store.add-to-cart-button', ['productId' => $product->id], key($product->id))
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $products->links() }} 
                    </div>

                @else
                    <div class="flex flex-col items-center justify-center py-16 bg-white rounded-lg border border-dashed border-gray-300">
                        <div class="bg-gray-50 p-4 rounded-full mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No products found</h3>
                        <p class="text-gray-500 text-sm mt-1 mb-6">We couldn't find any matches for your filters.</p>
                        <a href="{{ route('store.search') }}" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                            Clear all filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
