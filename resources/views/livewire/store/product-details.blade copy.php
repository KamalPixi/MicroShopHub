<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" 
     x-data="{ 
         lightboxOpen: false,
         mainImage: '{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}',
         zoomStyle: '',
         init() {
             Livewire.on('reset-success', () => { setTimeout(() => { @this.set('showSuccess', false) }, 2000) });
         },
         zoomImage(e) {
            const img = this.$refs.mainImg;
            const rect = img.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            this.zoomStyle = `transform-origin: ${x}% ${y}%; transform: scale(1.5);`;
         },
         resetZoom() { this.zoomStyle = 'transform: scale(1);'; }
     }">

    <nav class="flex mb-4 text-[11px] uppercase tracking-wider text-gray-400">
        <a href="{{ route('store.index') }}" class="hover:text-black transition-colors">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('store.search') }}" class="hover:text-black transition-colors">Shop</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-bold">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden relative cursor-zoom-in"
                 @mousemove="zoomImage($event)" @mouseleave="resetZoom()" @click="lightboxOpen = true">
                <img :src="mainImage" x-ref="mainImg" alt="{{ $product->name }}" 
                     class="w-full h-auto object-cover transition-transform duration-200" :style="zoomStyle">
            </div>
            
            @if(!empty($product->images) && is_array($product->images))
                <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                    <button @click="mainImage = '{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}'"
                         class="w-16 h-16 flex-shrink-0 rounded border-2 transition-all"
                         :class="mainImage.includes('{{ basename($product->thumbnail ?? '') }}') ? 'border-black' : 'border-gray-100'">
                        <img src="{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}" class="w-full h-full object-cover">
                    </button>
                    @foreach($product->images as $img)
                        @php $imgUrl = Str::startsWith($img, ['http']) ? $img : Storage::url($img); @endphp
                        <button @click="mainImage = '{{ $imgUrl }}'"
                             class="w-16 h-16 flex-shrink-0 rounded border-2 transition-all"
                             :class="mainImage === '{{ $imgUrl }}' ? 'border-black' : 'border-gray-100'">
                            <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <div class="mb-4">
                    <span class="text-[10px] font-bold bg-gray-900 text-white px-2 py-0.5 rounded uppercase">{{ $product->categories->first()->name ?? 'Product' }}</span>
                    <h1 class="text-2xl font-black text-gray-900 mt-2 leading-tight">{{ $product->name }}</h1>
                    <p class="text-xs text-gray-500 mt-1">SKU: {{ $selectedVariation ? $selectedVariation->sku : $product->sku }}</p>
                </div>

                <div class="flex items-baseline gap-2 mb-6">
                    <span class="text-3xl font-black text-gray-900">
                        {{$product->currency_symbol}}{{ number_format($currentPrice, 2) }}
                    </span>
                    @if($product->has_variations && !$selectedVariation)
                        <span class="text-[10px] font-bold text-gray-400 border border-gray-200 px-2 py-0.5 rounded">BASE PRICE</span>
                    @endif
                </div>

                @if(count($productOptions) > 0)
                    <div class="space-y-5 py-6 border-y border-gray-100 mb-6">
                        @foreach($productOptions as $option)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">{{ $option['name'] }}</label>
                                    <span class="text-[11px] font-bold text-black">{{ $this->getSelectedValueName($option['id']) ?? 'Not Selected' }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($option['values'] as $value)
                                        <button wire:click="selectAttribute({{ $option['id'] }}, {{ $value['id'] }})"
                                                type="button"
                                                class="px-3 py-1.5 text-xs font-bold border transition-all
                                                {{ isset($selectedAttributes[$option['id']]) && $selectedAttributes[$option['id']] == $value['id']
                                                    ? 'border-black bg-black text-white' 
                                                    : 'border-gray-200 text-gray-600 hover:border-black hover:text-black bg-white' }}">
                                            {{ $value['value'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @if($selectionMissing)
                            <p class="text-[10px] font-bold text-red-600 uppercase animate-pulse">! Please complete selection to buy</p>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-between mb-6">
                    <div>
                        @if($currentStock > 0)
                            <div class="flex items-center text-green-700 text-[11px] font-bold uppercase">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                In Stock ({{ $currentStock }})
                            </div>
                        @else
                            <div class="flex items-center text-red-600 text-[11px] font-bold uppercase">
                                <span class="w-2 h-2 bg-red-600 rounded-full mr-2"></span>
                                Out of Stock
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center border border-gray-200 rounded overflow-hidden">
                        <button wire:click="decrement" class="px-3 py-1 bg-gray-50 hover:bg-gray-100 border-r border-gray-200">-</button>
                        <span class="px-4 text-sm font-bold">{{ $quantity }}</span>
                        <button wire:click="increment" class="px-3 py-1 bg-gray-50 hover:bg-gray-100 border-l border-gray-200">+</button>
                    </div>
                </div>

                <div class="space-y-3">
                    @php
                        $isOutOfStock = ($product->has_variations && $selectedVariation && $currentStock <= 0) || (!$product->has_variations && $currentStock <= 0);
                        $btnDisabled = $isOutOfStock || ($product->has_variations && !$selectedVariation);
                    @endphp

                    <div class="flex gap-2">
                        <button wire:click="addToCart" 
                                wire:loading.attr="disabled"
                                @if($btnDisabled) disabled @endif
                                class="flex-1 h-12 border-2 border-black font-bold uppercase text-xs tracking-widest transition-all
                                {{ $showSuccess ? 'bg-green-600 border-green-600 text-white' : ($btnDisabled ? 'opacity-30 cursor-not-allowed' : 'bg-white text-black hover:bg-black hover:text-white') }}">
                            <span wire:loading.remove>
                                {{ $showSuccess ? 'Added' : 'Add to Cart' }}
                            </span>
                            <span wire:loading>...</span>
                        </button>

                        <button wire:click="addToCart(true)" 
                                @if($btnDisabled) disabled @endif
                                class="flex-1 h-12 bg-black text-white font-bold uppercase text-xs tracking-widest hover:bg-gray-800 transition-all {{ $btnDisabled ? 'opacity-30 cursor-not-allowed' : '' }}">
                            Buy Now
                        </button>
                    </div>
                    
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Subtotal</span>
                        <span class="text-lg font-black">{{$product->currency_symbol}}{{ number_format($currentPrice * $quantity, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 border border-gray-100 rounded bg-gray-50/50">
                    <p class="text-[10px] font-bold text-gray-900 uppercase">Secure Shipping</p>
                    <p class="text-[10px] text-gray-500">Fast & tracked delivery</p>
                </div>
                <div class="p-3 border border-gray-100 rounded bg-gray-50/50">
                    <p class="text-[10px] font-bold text-gray-900 uppercase">Authentic</p>
                    <p class="text-[10px] text-gray-500">100% genuine product</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-16 pt-12 border-t border-gray-200">
        <livewire:store.product-reviews :product="$product" />
    </div>

    @if($relatedProducts && $relatedProducts->count() > 0)
        <div class="mt-16 pt-12 border-t border-gray-200">
            <h2 class="text-sm font-black uppercase tracking-[0.2em] mb-8">Related Items</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($relatedProducts as $related)
                    <div class="group">
                        <a href="{{ route('store.product.show', $related->slug) }}" class="aspect-square block bg-gray-100 mb-3 border border-gray-100 overflow-hidden">
                            <img src="{{ $related->thumbnail ? (Str::startsWith($related->thumbnail, ['http']) ? $related->thumbnail : Storage::url($related->thumbnail)) : 'https://placehold.co/500' }}" 
                                 class="w-full h-full object-cover transition-transform group-hover:scale-105">
                        </a>
                        <a href="{{ route('store.product.show', $related->slug) }}" class="block">
                            <h3 class="text-[11px] font-bold uppercase truncate">{{ $related->name }}</h3>
                            <p class="text-sm font-medium mt-1">{{$related->currency_symbol}}{{ number_format($related->price, 2) }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <div x-show="lightboxOpen" style="display: none;" 
         x-transition.opacity class="fixed inset-0 z-[9999] bg-white/95 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="lightboxOpen = false">
        <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-black"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        <img :src="mainImage" class="max-w-full max-h-[90vh] object-contain shadow-xl border border-gray-100">
    </div>

</div>
