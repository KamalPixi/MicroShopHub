@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex mb-8 text-sm text-gray-500">
            <a href="{{ route('store.index') }}" class="hover:text-primary">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('store.search') }}" class="hover:text-primary">Shop</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10" x-data="{ 
            mainImage: '{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600?text=No+Image' }}' 
        }">
            
            <div class="space-y-4">
                <div class="aspect-square bg-gray-50 rounded-xl overflow-hidden border border-gray-100 relative group">
                    <img :src="mainImage" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                </div>
                
                @if(!empty($product->images) && is_array($product->images) && count($product->images) > 0)
                    <div class="grid grid-cols-5 gap-3">
                        <div @click="mainImage = '{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}'"
                             class="aspect-square bg-gray-50 rounded-lg cursor-pointer border-2 overflow-hidden transition-all"
                             :class="mainImage === '{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}' ? 'border-primary' : 'border-transparent hover:border-gray-300'">
                            <img src="{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}" 
                                 class="w-full h-full object-cover">
                        </div>

                        @foreach($product->images as $img)
                            @php
                                $imgUrl = Str::startsWith($img, ['http']) ? $img : Storage::url($img);
                            @endphp
                            <div @click="mainImage = '{{ $imgUrl }}'"
                                 class="aspect-square bg-gray-50 rounded-lg cursor-pointer border-2 overflow-hidden transition-all"
                                 :class="mainImage === '{{ $imgUrl }}' ? 'border-primary' : 'border-transparent hover:border-gray-300'">
                                <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                
                @if($product->categories->isNotEmpty())
                    <div class="mb-4 text-sm text-gray-500">
                        @foreach($product->categories as $cat)
                            <a href="{{ route('store.search', ['category' => $cat->id]) }}" class="hover:text-primary hover:underline">{{ $cat->name }}</a>{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center mb-6">
                    <span class="text-3xl font-bold text-primary">${{ number_format($product->price, 2) }}</span>
                    @if($product->stock > 0)
                        <span class="ml-4 px-2.5 py-0.5 bg-green-100 text-green-800 text-xs font-bold rounded-full">In Stock</span>
                    @else
                        <span class="ml-4 px-2.5 py-0.5 bg-red-100 text-red-800 text-xs font-bold rounded-full">Out of Stock</span>
                    @endif
                </div>

                <div class="prose prose-sm text-gray-600 mb-8">
                    {!! $product->description !!}
                </div>

                <div class="border-t border-gray-200 pt-6">
                     @livewire('add-to-cart-button', ['productId' => $product->id, 'showQuantity' => true], key($product->id))
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-100 text-sm text-gray-500 space-y-2">
                    <p><span class="font-medium text-gray-900">SKU:</span> {{ $product->slug }}</p>
                </div>
            </div>
        </div>

        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">You might also like</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group border border-gray-100 flex flex-col">
                            <a href="{{ route('store.product', $related->slug) }}" class="aspect-square relative overflow-hidden bg-gray-100 block">
                                <img src="{{ $related->thumbnail ? (Str::startsWith($related->thumbnail, ['http']) ? $related->thumbnail : Storage::url($related->thumbnail)) : 'https://placehold.co/500' }}" 
                                     alt="{{ $related->name }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </a>
                            <div class="p-4 flex-1 flex flex-col">
                                <a href="{{ route('store.product', $related->slug) }}">
                                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-primary truncate mb-1">{{ $related->name }}</h3>
                                </a>
                                <div class="mt-auto flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-900">${{ number_format($related->price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
