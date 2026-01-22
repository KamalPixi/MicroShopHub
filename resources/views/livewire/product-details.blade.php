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
                    <span>SKU: {{ $selectedVariation ? $selectedVariation->sku : $product->slug }}</span>
                </div>

                <div class="flex items-end gap-3 mb-6 pb-6 border-b border-gray-100">
                    <span class="text-4xl font-bold text-primary">${{ number_format($currentPrice, 2) }}</span>
                    @if($product->has_variations && !$selectedVariation)
                        <span class="text-sm text-gray-400 mb-2">(Base Price)</span>
                    @endif
                </div>

                @if($product->has_variations)
                    <div class="space-y-5 mb-8">
                        @foreach($product->attributes->unique('id') as $attribute)
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">{{ $attribute->name }}</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($attribute->values as $value)
                                        <button wire:click="selectAttribute({{ $attribute->id }}, {{ $value->id }})"
                                                class="px-4 py-2 text-sm border rounded-lg transition-all focus:outline-none 
                                                {{ isset($selectedAttributes[$attribute->id]) && $selectedAttributes[$attribute->id] == $value->id 
                                                    ? 'border-primary bg-primary text-white shadow-md' 
                                                    : 'border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                                            {{ $value->value }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mb-6">
                    @if($currentStock > 0)
                        <div class="flex items-center text-green-600 bg-green-50 px-3 py-2 rounded-lg w-fit">
                            <span class="relative flex h-2 w-2 mr-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            <span class="text-sm font-bold">In Stock</span>
                            <span class="text-xs ml-1 text-green-700">({{ $currentStock }} available)</span>
                        </div>
                    @else
                        <div class="flex items-center text-red-600 bg-red-50 px-3 py-2 rounded-lg w-fit">
                            <span class="text-sm font-bold">
                                {{ $product->has_variations && !$selectedVariation ? 'Select options to see stock' : 'Out of Stock' }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="mt-auto bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="flex flex-col sm:flex-row gap-4 items-center">
                        
                        <div class="flex items-center bg-white border border-gray-300 rounded-lg h-10 w-fit shadow-sm">
                            <button wire:click="decrement" class="px-3 text-gray-600 hover:text-primary transition text-lg">-</button>
                            <input type="text" value="{{ $quantity }}" readonly class="w-10 text-center border-none p-0 text-gray-900 font-bold focus:ring-0">
                            <button wire:click="increment" class="px-3 text-gray-600 hover:text-primary transition text-lg">+</button>
                        </div>

                        <div class="flex flex-col px-2">
                            <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total</span>
                            <span class="text-lg font-bold text-gray-900">${{ number_format($currentPrice * $quantity, 2) }}</span>
                        </div>

                        <button wire:click="addToCart" 
                                wire:loading.attr="disabled"
                                class="flex-1 h-10 px-6 rounded-lg font-bold text-white shadow-md transition-all flex items-center justify-center space-x-2 text-sm
                                {{ $showSuccess ? 'bg-green-500 hover:bg-green-600' : 'bg-primary hover:bg-blue-700' }} 
                                {{ $currentStock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $currentStock <= 0 ? 'disabled' : '' }}>
                            
                            <span wire:loading.remove>
                                {{ $showSuccess ? 'Added to Cart!' : ($currentStock <= 0 ? 'Unavailable' : 'Add to Cart') }}
                            </span>
                            <span wire:loading><svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                        </button>
                    </div>

                    <div class="mt-3 text-center">
                         <button wire:click="addToCart(true)" class="text-sm text-gray-500 hover:text-primary underline decoration-dotted">Buy now & checkout</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="bg-primary/10 text-primary p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                </span>
                Description
            </h3>
            <div class="prose prose-blue prose-lg max-w-none text-gray-600">
                {!! $product->description !!}
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 h-fit">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Why shop with us?</h3>
            <ul class="space-y-3">
                <li class="flex items-start text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Premium Quality</li>
                <li class="flex items-start text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Secure Payment</li>
            </ul>
        </div>
    </div>
    
    <div x-show="lightboxOpen" style="display: none;" 
         x-transition.opacity class="fixed inset-0 z-[9999] bg-black/95 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="lightboxOpen = false">
        <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white/70 hover:text-white"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        <img :src="mainImage" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
    </div>

</div>
