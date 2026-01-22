@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-10" x-data="productGallery()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex mb-8 text-sm text-gray-500">
            <a href="{{ route('store.index') }}" class="hover:text-primary transition-colors">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('store.search') }}" class="hover:text-primary transition-colors">Shop</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">{{ $product->name }}</span>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                
                <div class="space-y-4 select-none">
                    <div class="aspect-square bg-gray-50 rounded-2xl overflow-hidden relative group cursor-zoom-in border border-gray-100"
                         @mousemove="zoomImage($event)"
                         @mouseleave="resetZoom()"
                         @click="openLightbox()">
                        
                        <img :src="mainImage" 
                             x-ref="mainImg"
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover transition-transform duration-200"
                             :style="zoomStyle">
                        
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur p-2 rounded-full shadow-sm opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        </div>
                    </div>
                    
                    @if(!empty($product->images) && is_array($product->images) && count($product->images) > 0)
                        <div class="grid grid-cols-5 gap-3">
                            <button @click="mainImage = '{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}'"
                                 class="aspect-square rounded-xl overflow-hidden border-2 transition-all focus:outline-none"
                                 :class="mainImage === '{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}' ? 'border-primary ring-2 ring-primary/20' : 'border-transparent hover:border-gray-200'">
                                <img src="{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}" 
                                     class="w-full h-full object-cover">
                            </button>

                            @foreach($product->images as $img)
                                @php $imgUrl = Str::startsWith($img, ['http']) ? $img : Storage::url($img); @endphp
                                <button @click="mainImage = '{{ $imgUrl }}'"
                                     class="aspect-square rounded-xl overflow-hidden border-2 transition-all focus:outline-none"
                                     :class="mainImage === '{{ $imgUrl }}' ? 'border-primary ring-2 ring-primary/20' : 'border-transparent hover:border-gray-200'">
                                    <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex flex-col">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">{{ $product->name }}</h1>
                    
                    <div class="flex items-center text-sm text-gray-500 mb-6 space-x-4">
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-semibold uppercase tracking-wide">
                            {{ $product->categories->first()->name ?? 'Product' }}
                        </span>
                        <span>SKU: {{ $product->slug }}</span>
                    </div>

                    <div class="flex items-end gap-3 mb-6 pb-6 border-b border-gray-100">
                        <span class="text-4xl font-bold text-primary">${{ number_format($product->price, 2) }}</span>
                        {{-- <span class="text-lg text-gray-400 line-through mb-1">$120.00</span> --}}
                    </div>

                    @if($product->attributes->count() > 0)
                        <div class="space-y-5 mb-8">
                            @foreach($product->attributes->unique('id') as $attribute)
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">{{ $attribute->name }}</label>
                                    <div class="flex flex-wrap gap-2" x-data="{ selected: null }">
                                        @foreach($attribute->values as $value)
                                            <button @click="selected = '{{ $value->id }}'"
                                                    class="px-4 py-2 text-sm border rounded-lg transition-all focus:outline-none"
                                                    :class="selected === '{{ $value->id }}' 
                                                        ? 'border-primary bg-primary text-white shadow-md' 
                                                        : 'border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50'">
                                                {{ $value->value }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mb-6">
                        @if($product->stock > 0)
                            <div class="flex items-center text-green-600 bg-green-50 px-3 py-2 rounded-lg w-fit">
                                <span class="relative flex h-2 w-2 mr-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                <span class="text-sm font-bold">In Stock</span>
                                <span class="text-xs ml-1 font-normal text-green-700">({{ $product->stock }} available)</span>
                            </div>
                        @else
                            <div class="flex items-center text-red-600 bg-red-50 px-3 py-2 rounded-lg w-fit">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-sm font-bold">Out of Stock</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-auto flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                             @livewire('add-to-cart-button', ['productId' => $product->id, 'showQuantity' => true], key('cart-'.$product->id))
                        </div>
                        
                        <button wire:click="addToCart({{ $product->id }}); window.location='{{ route('store.index') }}'" 
                                class="flex-1 bg-gray-900 text-white font-bold py-3 px-6 rounded-lg hover:bg-black transition shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 h-[46px] mt-[1px]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>Buy Now</span>
                        </button>
                    </div>

                    <div class="mt-6 flex items-center justify-center gap-4 text-xs text-gray-400 border-t border-gray-100 pt-4">
                        <div class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Secure Transaction</div>
                        <div class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg> Fast Shipping</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-primary/10 text-primary p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    </span>
                    Product Description
                </h3>
                <div class="prose prose-blue prose-lg max-w-none text-gray-600">
                    {!! $product->description !!}
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 h-fit">
                 <h3 class="text-lg font-bold text-gray-900 mb-4">Highlights</h3>
                 <ul class="space-y-3">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm text-gray-600">Premium Quality Material</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm text-gray-600">Authentic Brand</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm text-gray-600">30 Day Return Policy</span>
                    </li>
                 </ul>
            </div>
        </div>

        @if($relatedProducts->count() > 0)
            <div class="border-t border-gray-200 pt-12">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">You might also like</h2>
                    <a href="{{ route('store.search', ['category' => $product->categories->first()->id ?? null]) }}" class="text-primary hover:text-blue-700 font-medium text-sm">View More →</a>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group border border-gray-100 flex flex-col">
                            <a href="{{ route('store.product', $related->slug) }}" class="aspect-square relative overflow-hidden bg-gray-100 block">
                                <img src="{{ $related->thumbnail ? (Str::startsWith($related->thumbnail, ['http']) ? $related->thumbnail : Storage::url($related->thumbnail)) : 'https://placehold.co/500' }}" 
                                     alt="{{ $related->name }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                
                                @if($related->price < 50) 
                                    <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">HOT</span>
                                @endif
                            </a>
                            <div class="p-4 flex-1 flex flex-col">
                                <a href="{{ route('store.product', $related->slug) }}" class="block mb-1">
                                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-1">{{ $related->name }}</h3>
                                </a>
                                <p class="text-xs text-gray-500 mb-3">{{ $related->categories->first()->name ?? 'General' }}</p>
                                
                                <div class="mt-auto flex items-center justify-between">
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($related->price, 2) }}</span>
                                    <button class="text-gray-400 hover:text-primary transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <div x-show="lightboxOpen" 
         style="display: none;" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] bg-black/95 backdrop-blur-sm flex items-center justify-center p-4"
         @keydown.escape.window="closeLightbox()">
        
        <button @click="closeLightbox()" class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors z-50">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <img :src="mainImage" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl" @click.outside="closeLightbox()">
    </div>

</div>

<script>
    function productGallery() {
        return {
            mainImage: '{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : "https://placehold.co/600?text=No+Image" }}',
            lightboxOpen: false,
            zoomStyle: '',

            zoomImage(e) {
                const img = this.$refs.mainImg;
                const rect = img.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                // Calculate percentage position
                const xPercent = (x / rect.width) * 100;
                const yPercent = (y / rect.height) * 100;

                this.zoomStyle = `transform-origin: ${xPercent}% ${yPercent}%; transform: scale(2);`;
            },

            resetZoom() {
                this.zoomStyle = 'transform: scale(1);';
            },

            openLightbox() {
                this.lightboxOpen = true;
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            },

            closeLightbox() {
                this.lightboxOpen = false;
                document.body.style.overflow = 'auto'; // Re-enable scrolling
            }
        }
    }
</script>
@endsection
