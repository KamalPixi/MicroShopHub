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
                <span class="text-sm text-gray-500 mr-2 shrink-0">Sort by:</span>
                <form method="GET" action="{{ route('store.search') }}">
                    @if($query) <input type="hidden" name="query" value="{{ $query }}"> @endif
                    @if($categoryId) <input type="hidden" name="category" value="{{ $categoryId }}"> @endif
                    @if($minPrice) <input type="hidden" name="min_price" value="{{ $minPrice }}"> @endif
                    @if($maxPrice) <input type="hidden" name="max_price" value="{{ $maxPrice }}"> @endif

                    <div class="relative">
                        <select name="sort" onchange="this.form.submit()" class="w-full appearance-none rounded-lg border border-gray-200 bg-white py-2 pl-3 pr-10 text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-200 cursor-pointer">
                            <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                            <option value="price_low" {{ $sort == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ $sort == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
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
                            <button type="submit" class="w-full bg-primary text-white py-2 rounded text-sm font-medium hover:bg-primary transition">
                                Apply Price
                            </button>
                        </div>
                    </div>
                </form>

                <div x-data="{ expanded: false }" class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 uppercase text-xs tracking-wider">Categories</h3>
                        @if($categoryId)
                            <a href="{{ route('store.search', request()->except('category', 'page')) }}" class="text-xs text-primary hover:underline">Clear</a>
                        @endif
                    </div>

                    <div class="relative rounded-xl border border-gray-100 bg-gray-50/60">
                        <div :class="expanded ? 'max-h-none' : 'max-h-72 lg:max-h-80 overflow-hidden'" class="relative space-y-2 px-3 py-3 transition-all duration-300">
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

                        <div class="border-t border-gray-200 bg-white px-3 py-2">
                            <button
                                type="button"
                                @click="expanded = !expanded"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100"
                            >
                                <span x-text="expanded ? 'Show fewer categories' : 'Show all categories'"></span>
                                <svg class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="flex-1">
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group border border-gray-100 flex flex-col cursor-pointer"
                             role="link"
                             tabindex="0"
                             onclick="if (!event.target.closest('button, a, [wire\\:click], input, select, textarea')) window.location='{{ route('store.product.show', $product->slug) }}'"
                             onkeydown="if ((event.key === 'Enter' || event.key === ' ') && !event.target.closest('button, a, [wire\\:click], input, select, textarea')) { event.preventDefault(); window.location='{{ route('store.product.show', $product->slug) }}'; }">
                            
                            <div class="block aspect-square relative overflow-hidden bg-gray-100">
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
                            </div>
                            
                            <div class="p-3 flex-1 flex flex-col">
                                <div class="block">
                                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-primary truncate mb-1">
                                        {{ $product->name }}
                                    </h3>
                                </div>
                                
                                <p class="text-xs text-gray-500 mb-2 truncate">
                                    {{ $product->categories->first()->name ?? 'General' }}
                                </p>
                                
                                <div class="mt-auto flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">{{$product->currency_symbol}}{{ number_format($product->price, 2) }}</span>
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
                        <a href="{{ route('store.search') }}" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary transition">
                            Clear all filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
