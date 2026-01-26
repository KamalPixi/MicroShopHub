<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" 
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
            this.zoomStyle = `transform-origin: ${x}% ${y}%; transform: scale(2);`;
         },
         resetZoom() { this.zoomStyle = 'transform: scale(1);'; }
     }">

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
                     @mousemove="zoomImage($event)" @mouseleave="resetZoom()" @click="lightboxOpen = true">
                    <img :src="mainImage" x-ref="mainImg" alt="{{ $product->name }}" 
                         class="w-full h-full object-cover transition-transform duration-200" :style="zoomStyle">
                </div>
                
                @if(!empty($product->images) && is_array($product->images) && count($product->images) > 0)
                    <div class="grid grid-cols-5 gap-3">
                         <button @click="mainImage = '{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}'"
                             class="aspect-square rounded-xl overflow-hidden border-2 transition-all focus:outline-none"
                             :class="mainImage.includes('{{ basename($product->thumbnail ?? '') }}') ? 'border-primary ring-2 ring-primary/20' : 'border-transparent hover:border-gray-200'">
                            <img src="{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/600' }}" class="w-full h-full object-cover">
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
                <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $product->name }}</h1>
                <div class="flex items-center text-sm text-gray-500 mb-6 space-x-4">
                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-semibold uppercase">{{ $product->categories->first()->name ?? 'Product' }}</span>
                    <span>SKU: {{ $selectedVariation ? $selectedVariation->sku : $product->sku }}</span>
                </div>

                <div class="flex items-end gap-3 mb-6 pb-6 border-b border-gray-100">
                    <span class="text-4xl font-bold text-primary transition-all duration-300">
                        ${{ number_format($currentPrice, 2) }}
                    </span>
                    @if($product->has_variations && !$selectedVariation)
                        <span class="text-sm font-medium text-gray-400 mb-2 bg-gray-50 px-2 py-1 rounded">From (Base Price)</span>
                    @elseif($selectedVariation && $product->has_variations)
                        <span class="text-sm font-medium text-green-600 mb-2 bg-green-50 px-2 py-1 rounded">Selected Price</span>
                    @endif
                </div>

                @if(count($productOptions) > 0)
                    <div id="attributes-section" class="space-y-6 mb-8 transition-all duration-300 {{ $selectionMissing ? 'p-4 border-2 border-red-100 bg-red-50 rounded-xl animate-pulse ring-4 ring-red-50' : '' }}">
                        
                        @if($selectionMissing)
                            <div class="text-red-600 text-sm font-bold flex items-center mb-2 animate-bounce">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Please select the options below to proceed
                            </div>
                        @endif

                        @foreach($productOptions as $option)
                            <div class="animate-fade-in">
                                <div class="flex justify-between items-baseline mb-2">
                                    <label class="block text-sm font-bold text-gray-900">
                                        {{ $option['name'] }}: 
                                        <span class="text-primary ml-1 font-extrabold">{{ $this->getSelectedValueName($option['id']) ?? 'Select one' }}</span>
                                    </label>
                                    
                                    @if($loop->first && !empty($selectedAttributes))
                                        <button wire:click="resetSelection" class="text-xs text-gray-400 hover:text-red-500 underline transition-colors">Clear Selection</button>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @foreach($option['values'] as $value)
                                        <button wire:click="selectAttribute({{ $option['id'] }}, {{ $value['id'] }})"
                                                type="button"
                                                class="px-5 py-2.5 text-sm font-medium border rounded-lg transition-all duration-200 focus:outline-none transform active:scale-95
                                                {{ isset($selectedAttributes[$option['id']]) && $selectedAttributes[$option['id']] == $value['id']
                                                    ? 'border-primary bg-primary text-white shadow-md ring-2 ring-offset-1 ring-primary/30' 
                                                    : 'border-gray-200 text-gray-700 hover:border-primary hover:text-primary bg-white' }}">
                                            {{ $value['value'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mb-6">
                    @if($product->has_variations && !$selectedVariation)
                        <div class="flex items-center text-blue-700 bg-blue-50 px-4 py-3 rounded-lg w-full border border-blue-100 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-medium">Select options to see availability.</span>
                        </div>
                    @elseif($currentStock > 0)
                        <div class="flex items-center text-green-700 bg-green-50 px-4 py-3 rounded-lg w-fit border border-green-100 shadow-sm transition-all duration-500">
                            <span class="relative flex h-2.5 w-2.5 mr-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                            </span>
                            <span class="text-sm font-bold">In Stock</span>
                            <span class="text-xs ml-2 text-green-600 bg-white px-2 py-0.5 rounded border border-green-200">{{ $currentStock }} units</span>
                        </div>
                    @else
                        <div class="flex items-center text-red-700 bg-red-50 px-4 py-3 rounded-lg w-fit border border-red-100 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-bold">Currently Out of Stock</span>
                        </div>
                    @endif
                </div>

                <div class="mt-auto pt-6 border-t border-gray-100">
                    <div class="flex items-end justify-between mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Quantity</label>
                            <div class="flex items-center border border-gray-300 rounded-lg h-10 w-fit">
                                <button wire:click="decrement" class="px-3 text-gray-500 hover:text-primary hover:bg-gray-50 h-full rounded-l-lg transition">-</button>
                                <input type="text" value="{{ $quantity }}" readonly class="w-10 text-center border-none p-0 text-gray-900 font-bold focus:ring-0">
                                <button wire:click="increment" class="px-3 text-gray-500 hover:text-primary hover:bg-gray-50 h-full rounded-r-lg transition">+</button>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <span class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Total</span>
                            <span class="text-2xl font-bold text-gray-900">${{ number_format($currentPrice * $quantity, 2) }}</span>
                        </div>
                    </div>

                    @php
                        $isOutOfStock = false;
                        if ($product->has_variations) {
                            if ($selectedVariation && $currentStock <= 0) {
                                $isOutOfStock = true;
                            }
                        } else {
                            if ($currentStock <= 0) {
                                $isOutOfStock = true;
                            }
                        }
                    @endphp

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button wire:click="addToCart" 
                                wire:loading.attr="disabled"
                                class="flex-1 h-12 px-4 rounded-lg font-bold text-white shadow-sm hover:shadow-lg transform active:scale-[0.98] transition-all flex items-center justify-center text-sm
                                {{ $showSuccess ? 'bg-green-600 hover:bg-green-700' : 'bg-primary hover:bg-blue-700' }} 
                                {{ $isOutOfStock ? 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400' : '' }}"
                                {{ $isOutOfStock ? 'disabled' : '' }}>
                            
                            <span wire:loading.remove class="flex items-center gap-2">
                                @if($showSuccess)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Added to Cart
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Add to Cart
                                @endif
                            </span>
                            <span wire:loading><svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                        </button>

                        <button wire:click="addToCart(true)" 
                                class="flex-1 h-12 px-4 rounded-lg font-bold bg-gray-900 text-white shadow-sm hover:shadow-lg hover:bg-black transform active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm
                                {{ $isOutOfStock ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $isOutOfStock ? 'disabled' : '' }}>
                            <span>Buy Now</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-16">
        <livewire:store.product-reviews :product="$product" />
    </div>

    @if($relatedProducts && $relatedProducts->count() > 0)
        <div class="border-t border-gray-200 pt-12">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900">You might also like</h2>
                @if($product->categories->first())
                    <a href="{{ route('store.search', ['category' => $product->categories->first()->id]) }}" class="text-primary hover:text-blue-700 font-medium text-sm">View More →</a>
                @endif
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group border border-gray-100 flex flex-col">
                        <a href="{{ route('store.product.show', $related->slug) }}" class="aspect-square relative overflow-hidden bg-gray-100 block">
                            <img src="{{ $related->thumbnail ? (Str::startsWith($related->thumbnail, ['http']) ? $related->thumbnail : Storage::url($related->thumbnail)) : 'https://placehold.co/500' }}" 
                                 alt="{{ $related->name }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            
                            @if($related->price < 50) 
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">HOT</span>
                            @endif
                        </a>
                        <div class="p-4 flex-1 flex flex-col">
                            <a href="{{ route('store.product.show', $related->slug) }}" class="block mb-1">
                                <h3 class="text-sm font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-1">{{ $related->name }}</h3>
                            </a>
                            <p class="text-xs text-gray-500 mb-3">{{ $related->categories->first()->name ?? 'General' }}</p>
                            
                            <div class="mt-auto flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900">${{ number_format($related->price, 2) }}</span>
                                @livewire('store.add-to-cart-button', ['productId' => $related->id], key('related-'.$related->id))
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <div x-show="lightboxOpen" style="display: none;" 
         x-transition.opacity class="fixed inset-0 z-[9999] bg-black/95 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="lightboxOpen = false">
        <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white/70 hover:text-white"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        <img :src="mainImage" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
    </div>

</div>
