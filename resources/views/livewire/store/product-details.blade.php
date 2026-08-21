<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6" 
     x-data="productDetailData()" 
     @keydown.escape="lightboxOpen = false; sizeGuideOpen = false">

    {{-- Breadcrumb Navigation --}}
    <nav class="flex flex-wrap items-center gap-1 text-xs text-gray-500 mb-4 pb-2 border-b border-gray-100" aria-label="Breadcrumb">
        <a href="{{ route('store.index') }}" class="hover:text-primary transition-colors">Home</a>
        <span class="mx-1" aria-hidden="true">/</span>
        <a href="{{ route('store.search') }}" class="hover:text-primary transition-colors">Shop</a>
        <span class="mx-1" aria-hidden="true">/</span>
        <span class="text-gray-900 font-medium" aria-current="page">{{ $product->name }}</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
            
            {{-- Product Image & Video Gallery --}}
            <div class="space-y-4">
                {{-- Main Media Area (Image with Zoom OR Active Video) --}}
                <div class="aspect-square bg-gray-50 rounded-2xl overflow-hidden relative group border border-gray-100 flex items-center justify-center">
                    {{-- Video Viewport --}}
                    <div x-show="activeMediaType === 'video'" class="w-full h-full bg-black flex items-center justify-center" style="display: none;">
                        @if ($product->video_path)
                            <video controls autoplay playsinline class="w-full h-full object-contain">
                                <source src="{{ Storage::url($product->video_path) }}">
                                Your browser does not support the video tag.
                            </video>
                        @elseif ($product->video_url)
                            @php
                                $vUrl = $product->video_url;
                                $isYt = preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?|shorts)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i', $vUrl, $ytM);
                                $ytId = $isYt ? $ytM[1] : null;
                                $isVm = preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/i', $vUrl, $vmM);
                                $vmId = $isVm ? end($vmM) : null;
                            @endphp
                            @if ($ytId)
                                <iframe src="https://www.youtube.com/embed/{{ $ytId }}?rel=0"
                                        class="w-full h-full"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        referrerpolicy="strict-origin-when-cross-origin"
                                        allowfullscreen
                                        title="{{ $product->name }} product video">
                                </iframe>
                            @elseif ($vmId)
                                <iframe src="https://player.vimeo.com/video/{{ $vmId }}"
                                        class="w-full h-full"
                                        frameborder="0"
                                        allow="autoplay; fullscreen; picture-in-picture"
                                        allowfullscreen>
                                </iframe>
                            @else
                                <video controls autoplay class="w-full h-full object-contain">
                                    <source src="{{ $vUrl }}">
                                </video>
                            @endif
                        @endif
                    </div>

                    {{-- Image Viewport with Zoom --}}
                    <div x-show="activeMediaType === 'image'"
                         class="w-full h-full cursor-zoom-in relative"
                         role="button"
                         tabindex="0"
                         @keydown.enter="lightboxOpen = true"
                         @click="lightboxOpen = true"
                         @mousemove="zoomImage($event)" 
                         @mouseleave="resetZoom()"
                         aria-label="Click to open image in full screen">
                        <button type="button"
                                class="absolute top-3 right-3 z-10 inline-flex items-center justify-center rounded-full border border-white/70 bg-white/90 p-2 text-gray-700 shadow-sm transition-all hover:bg-primary hover:text-white hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                @click.stop="lightboxOpen = true"
                                aria-label="Zoom product image">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h6v6m0-6L14 10M9 21H3v-6m0 6l7-7"></path>
                            </svg>
                        </button>
                        <img :src="mainImage" 
                             x-ref="mainImg" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover transition-transform duration-200" 
                             :style="zoomStyle"
                             loading="lazy">
                    </div>
                </div>

                {{-- Thumbnail Gallery --}}
                @php
                    $mainImageUrl = $product->thumbnail 
                        ? (Str::startsWith($product->thumbnail, ['http']) 
                            ? $product->thumbnail 
                            : Storage::url($product->thumbnail))
                        : 'https://placehold.co/600';
                    
                    $allImages = array_merge(
                        [$mainImageUrl],
                        collect($product->images ?? [])->map(fn($img) => 
                            Str::startsWith($img, ['http']) ? $img : Storage::url($img)
                        )->toArray()
                    );
                @endphp

                @if((!empty($product->images) && is_array($product->images) && count($product->images) > 0) || $product->video_path || $product->video_url)
                    <div class="grid gap-3" 
                          style="grid-template-columns: repeat(auto-fill, minmax(80px, 1fr))"
                          role="tablist"
                          aria-label="Product image gallery">
                        {{-- Thumbnail: Main Image --}}
                        <button @click="activeMediaType = 'image'; mainImage = '{{ $mainImageUrl }}'"
                                :aria-selected="activeMediaType === 'image' && mainImage === '{{ $mainImageUrl }}'"
                                role="tab"
                                class="aspect-square rounded-xl overflow-hidden border-2 transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                :class="activeMediaType === 'image' && mainImage === '{{ $mainImageUrl }}' ? 'border-primary ring-2 ring-primary/20' : 'border-transparent hover:border-gray-200'">
                            <img src="{{ $mainImageUrl }}" 
                                 class="w-full h-full object-cover" 
                                 loading="lazy"
                                 alt="Product main image">
                        </button>

                        {{-- Additional Image Thumbnails --}}
                        @foreach($product->images ?? [] as $index => $img)
                            @php $imgUrl = Str::startsWith($img, ['http']) ? $img : Storage::url($img); @endphp
                            <button @click="activeMediaType = 'image'; mainImage = '{{ $imgUrl }}'"
                                    :aria-selected="activeMediaType === 'image' && mainImage === '{{ $imgUrl }}'"
                                    role="tab"
                                    class="aspect-square rounded-xl overflow-hidden border-2 transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                    :class="activeMediaType === 'image' && mainImage === '{{ $imgUrl }}' ? 'border-primary ring-2 ring-primary/20' : 'border-transparent hover:border-gray-200'">
                                <img src="{{ $imgUrl }}" 
                                     class="w-full h-full object-cover" 
                                     loading="lazy"
                                     alt="Product image {{ $index + 2 }}">
                            </button>
                        @endforeach

                        {{-- Video Thumbnail in Gallery --}}
                        @if ($product->video_path || $product->video_url)
                            <button @click="activeMediaType = 'video'"
                                    :aria-selected="activeMediaType === 'video'"
                                    role="tab"
                                    class="aspect-square rounded-xl overflow-hidden border-2 transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 relative bg-gray-900 flex flex-col items-center justify-center text-white group"
                                    :class="activeMediaType === 'video' ? 'border-primary ring-2 ring-primary/20' : 'border-transparent hover:border-gray-300'">
                                <div class="w-8 h-8 rounded-full bg-red-600/90 text-white flex items-center justify-center shadow group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold mt-1 tracking-tight text-white/90">VIDEO</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Product Details --}}
            <div class="flex flex-col">
                {{-- Product Header --}}
                <div class="mb-3">
                    <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-gray-900 leading-tight mb-2">
                        {{ $product->name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600">
                        <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">
                            {{ $product->categories->first()->name ?? __('store.product') }}
                        </span>
                        <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs">
                            SKU: <span class="font-mono ml-1 font-semibold text-gray-900">{{ $selectedVariation ? $selectedVariation->sku : $product->sku }}</span>
                        </span>
                    </div>
                </div>

                {{-- Price Section --}}
                @php
                    $minVariationPrice = $product->has_variations ? (float) $product->variations->min('price') : (float) $basePrice;
                    $maxVariationPrice = $product->has_variations ? (float) $product->variations->max('price') : (float) $basePrice;
                    $hasPriceRange = $product->has_variations && $minVariationPrice !== $maxVariationPrice;
                    $discountPercent = ($originalPrice > 0 && $currentPrice < $originalPrice)
                        ? (int) round((($originalPrice - $currentPrice) / $originalPrice) * 100)
                        : 0;
                @endphp
                <div class="mb-2 pb-3 border-b border-gray-100">
                    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-gray-50 to-white p-3 sm:p-4 space-y-2">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">{{ __('store.price_label') }}</p>
                        <div class="flex flex-wrap items-end gap-3">
                        <span class="text-3xl font-bold text-primary transition-all duration-300" 
                              role="status"
                              aria-label="Product price">
                            {{ $product->currency_symbol }}{{ number_format($currentPrice, 2) }}
                        </span>
                        @if($discountPercent > 0)
                            <span class="text-base text-gray-400 line-through">
                                {{ $product->currency_symbol }}{{ number_format($originalPrice, 2) }}
                            </span>
                            <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full">
                                {{ __('store.save') }} {{ $discountPercent }}%
                            </span>
                        @endif
                        </div>
                        @if($hasFlashSale)
                            <div class="inline-flex items-center gap-1.5 text-xs font-medium text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-md">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                {{ $flashSaleTitle ?? __('store.flash_sale_label') }}
                            </div>
                        @endif
                        @if($product->has_variations && count($selectedAttributes) == 0)
                            <div class="inline-flex items-center gap-1.5 text-xs font-medium text-primary bg-primary/10 border border-primary/20 px-2.5 py-1 rounded-md">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('store.choose_options_to_see_price') }}
                            </div>
                        @elseif($selectedVariation && $product->has_variations)
                            <div class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-md">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('store.final_price_selected_options') }}
                            </div>
                        @endif
                        @if($hasPriceRange && count($selectedAttributes) == 0)
                            <p class="text-sm text-gray-500">
                                {{ __('store.range') }}: {{ $product->currency_symbol }}{{ number_format($minVariationPrice, 2) }} - {{ $product->currency_symbol }}{{ number_format($maxVariationPrice, 2) }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Product Options/Variations --}}
                @if(count($productOptions) > 0)
                    <div id="attributes-section" 
                         class="space-y-3 mb-4 transition-all duration-300 p-4 rounded-2xl border bg-gray-50/70 shadow-sm"
                         :class="selectionMissing ? 'border-red-200 ring-2 ring-red-100 bg-red-50' : 'border-gray-200'"
                         role="region"
                         aria-labelledby="options-heading">
                        
                        @if($selectionMissing)
                            <div class="text-red-600 text-sm font-bold flex items-center gap-2" role="alert">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>Please select the options below to proceed</span>
                            </div>
                        @endif

                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-2.5 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h3 id="options-heading" class="text-xs font-bold text-gray-900">{{ __('store.attributes_variations') }}</h3>
                                <p class="text-[11px] text-gray-500">{{ __('store.variation_options') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!empty($product->measurements['columns']) && !empty($product->measurements['rows']))
                                    <button type="button" 
                                            @click="sizeGuideOpen = true" 
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700 hover:text-primary bg-white hover:bg-primary/5 border border-gray-200 hover:border-primary/40 px-2.5 py-1 rounded-lg transition-all shadow-2xs group focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1">
                                        <svg class="w-3.5 h-3.5 text-primary group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                        </svg>
                                        <span>Size Guide</span>
                                    </button>
                                @endif
                                @if(!empty($selectedAttributes))
                                    <button wire:click="resetSelection" 
                                            type="button"
                                            class="text-xs text-gray-500 hover:text-red-600 underline transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded px-1">
                                        {{ __('store.clear_all') }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        @foreach($productOptions as $option)
                            <div class="space-y-2 border border-gray-100 bg-white rounded-xl p-2.5">
                                <div class="flex justify-between items-baseline gap-2">
                                    <label class="text-xs font-bold text-gray-900">
                                        {{ $option['name'] }}: 
                                        <span class="text-primary ml-1 font-extrabold">
                                            {{ $this->getSelectedValueName($option['id']) ?? __('store.select_one') }}
                                        </span>
                                    </label>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @foreach($option['values'] as $value)
                                        <button wire:click="selectAttribute({{ $option['id'] }}, {{ $value->id }})"
                                                type="button"
                                                class="px-4 py-2.5 min-w-[84px] text-center text-xs font-medium border rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 transform active:scale-95
                                                {{ isset($selectedAttributes[$option['id']]) && $selectedAttributes[$option['id']] == $value->id
                                                    ? 'border-primary bg-primary text-white shadow-md ring-primary/30' 
                                                    : 'border-gray-200 text-gray-700 hover:border-primary hover:text-primary bg-white focus:ring-primary' }}">
                                            {{ $value->value }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif(!empty($product->measurements['columns']) && !empty($product->measurements['rows']))
                    {{-- Size Guide Button for simple product (no variation options) --}}
                    <div class="mb-4">
                        <button type="button" 
                                @click="sizeGuideOpen = true" 
                                class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700 hover:text-primary bg-white hover:bg-primary/5 border border-gray-200 hover:border-primary/40 px-3.5 py-2 rounded-xl transition-all shadow-2xs group focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1">
                            <svg class="w-4 h-4 text-primary group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            <span>View Size & Measurements Guide</span>
                        </button>
                    </div>
                @endif

                {{-- Quantity & Add to Cart Section --}}
                <div class="space-y-2">
                    {{-- Quantity Selector & Total Price - Same Box --}}
                    <div class="bg-white border border-gray-200 rounded-xl p-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                            {{-- Quantity Selector --}}
                            <div class="flex-1">
                                <label class="block text-[11px] font-bold text-gray-900 mb-2">
                                    {{ __('store.quantity') }}
                                </label>
                                <div class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 p-1">
                                    <button wire:click="decrement" 
                                            type="button"
                                            class="h-10 w-10 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                            wire:loading.attr="disabled"
                                            @disabled($quantity <= 1)
                                            aria-label="Decrease quantity">
                                        <span class="text-lg">-</span>
                                    </button>
                                    <input type="text" 
                                           value="{{ $quantity }}"
                                           class="w-16 sm:w-20 h-10 text-center font-semibold border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none"
                                           aria-label="Quantity"
                                           role="spinbutton"
                                           aria-valuemin="1"
                                           aria-valuemax="{{ $currentStock > 0 ? $currentStock : 1 }}"
                                           aria-valuenow="{{ $quantity }}"
                                           readonly>
                                    <button wire:click="increment" 
                                            type="button"
                                            class="h-10 w-10 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                            wire:loading.attr="disabled"
                                            @disabled($currentStock <= 0 || $quantity >= $currentStock)
                                            aria-label="Increase quantity">
                                        <span class="text-lg">+</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="hidden sm:block h-16 border-l border-gray-300"></div>
                            <div class="sm:hidden border-t border-gray-200"></div>

                            {{-- Total Price --}}
                            <div class="flex-1 text-left sm:text-right">
                                <label class="block text-[11px] font-bold text-gray-900 mb-2">
                                    {{ __('store.total') }}
                                </label>
                                <span class="text-xl font-bold text-primary">
                                        {{ $product->currency_symbol }}{{ number_format($currentPrice * $quantity, 2) }}
                                    </span>
                                </div>
                        </div>

                        {{-- Stock Status --}}
                        <div class="mt-3 rounded-lg border px-3 py-2 text-xs sm:text-sm flex items-center gap-2"
                             :class="currentStock > 0 ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'"
                             role="status">
                            <span class="inline-block h-2 w-2 rounded-full"
                                  :class="currentStock > 0 ? 'bg-green-500' : 'bg-red-500'"></span>
                            @if($product->has_variations)
                                <span x-show="selectedAttributes.length > 0">
                                    <span x-show="currentStock > 5">{{ __('store.in_stock_ready_to_ship') }}</span>
                                    <span x-show="currentStock > 0 && currentStock <= 5">{{ __('store.only_left_in_stock', ['count' => '<span x-text="currentStock"></span>']) }}</span>
                                    <span x-show="currentStock <= 0" class="font-semibold">{{ __('store.out_of_stock') }}</span>
                                </span>
                                <span x-show="selectedAttributes.length === 0">{{ __('store.select_options_to_view_stock_availability') }}</span>
                            @else
                                @if($currentStock > 5)
                                    <span>{{ __('store.in_stock_ready_to_ship') }}</span>
                                @elseif($currentStock > 0)
                                    <span>{{ __('store.only_left_in_stock', ['count' => $currentStock]) }}</span>
                                @else
                                    <span class="font-semibold">{{ __('store.out_of_stock') }}</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Add to Cart Buttons --}}
                    <div class="flex flex-col md:flex-row gap-3">
                        <button wire:click="addToCart" 
                                type="button"
                                wire:loading.attr="disabled"
                                wire:target="addToCart"
                                class="w-full md:flex-1 h-12 px-4 rounded-lg font-bold text-white shadow-sm hover:shadow-lg transform active:scale-[0.98] transition-all flex items-center justify-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2
                                {{ $showSuccess ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-primary hover:bg-primary focus:ring-primary' }}
                                {{ $currentStock <= 0 ? 'opacity-50 cursor-not-allowed bg-gray-400 hover:bg-gray-400' : '' }}"
                                :disabled="currentStock <= 0">
                            
                            <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-2">
                                @if($showSuccess)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('store.added_to_cart') }}
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ __('store.add_to_cart') }}
                                @endif
                            </span>
                            <span wire:loading.flex wire:target="addToCart" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ __('store.adding') }}</span>
                            </span>
                        </button>

                        <button wire:click="buyNow" 
                                type="button"
                                wire:loading.attr="disabled"
                                wire:target="buyNow"
                                class="w-full md:flex-1 h-12 px-4 rounded-lg font-bold bg-gray-900 text-white shadow-sm hover:shadow-lg hover:bg-black transform active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
                                {{ $currentStock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                :disabled="currentStock <= 0">
                            <span wire:loading.remove wire:target="buyNow">{{ __('store.buy_now') }}</span>
                            <svg wire:loading.remove wire:target="buyNow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            <span wire:loading.flex wire:target="buyNow" class="items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ __('store.adding') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Product Description --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2.5">
            <span class="bg-primary/10 text-primary p-2 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </span>
            {{ __('store.product_description') }}
        </h2>
        <div class="prose prose-blue prose-lg max-w-none text-gray-600">
            {!! $product->description !!}
        </div>
    </div>

    {{-- Product Video Section --}}
    @if ($product->video_path || $product->video_url)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2.5">
                <span class="bg-primary/10 text-primary p-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                Product Video
            </h2>

            @if ($product->video_path)
                {{-- Uploaded video file --}}
                <div class="rounded-xl overflow-hidden bg-black shadow-sm">
                    <video controls preload="metadata"
                           class="w-full max-h-[480px] rounded-xl"
                           aria-label="{{ $product->name }} product video">
                        <source src="{{ Storage::url($product->video_path) }}">
                        Your browser does not support the video tag.
                    </video>
                </div>
            @elseif ($product->video_url)
                @php
                    $videoUrl = $product->video_url;
                    $isYoutube = preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?|shorts)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i', $videoUrl, $ytMatch);
                    $youtubeId = $isYoutube ? $ytMatch[1] : null;

                    $isVimeo = preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/i', $videoUrl, $vimeoMatch);
                    $vimeoId = $isVimeo ? end($vimeoMatch) : null;
                @endphp

                @if ($youtubeId)
                    <div class="rounded-xl overflow-hidden shadow-sm aspect-video">
                        <iframe
                            src="https://www.youtube.com/embed/{{ $youtubeId }}?rel=0"
                            class="w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                            loading="lazy"
                            title="{{ $product->name }} product video">
                        </iframe>
                    </div>
                @elseif ($vimeoId)
                    <div class="rounded-xl overflow-hidden shadow-sm aspect-video">
                        <iframe
                            src="https://player.vimeo.com/video/{{ $vimeoId }}"
                            class="w-full h-full"
                            frameborder="0"
                            allow="autoplay; fullscreen; picture-in-picture"
                            allowfullscreen
                            loading="lazy"
                            title="{{ $product->name }} product video">
                        </iframe>
                    </div>
                @else
                    {{-- Generic external video URL fallback --}}
                    <div class="rounded-xl overflow-hidden bg-black shadow-sm">
                        <video controls preload="metadata"
                               class="w-full max-h-[480px] rounded-xl"
                               aria-label="{{ $product->name }} product video">
                            <source src="{{ $videoUrl }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- Product Reviews Section --}}
    <div class="mt-10">
        <livewire:store.product-reviews :product="$product" />
    </div>

    {{-- Related Products Section --}}
    @if($relatedProducts && $relatedProducts->count() > 0)
        <div class="border-t border-gray-200 pt-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-5 gap-3">
                <h2 class="text-xl font-bold text-gray-900">{{ __('store.you_might_also_like') }}</h2>
                @if($product->categories->first())
                    <a href="{{ route('store.search', ['category' => $product->categories->first()->id]) }}" 
                       class="text-primary hover:text-primary font-medium text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded px-2">
                        {{ __('store.view_more') }} →
                    </a>
                @endif
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group border border-gray-100 flex flex-col">
                        <a href="{{ route('store.product.show', $related->slug) }}" 
                           class="aspect-square relative overflow-hidden bg-gray-100 block focus:outline-none">
                            @php
                                $relatedImageUrl = $related->thumbnail 
                                    ? (Str::startsWith($related->thumbnail, ['http']) 
                                        ? $related->thumbnail 
                                        : Storage::url($related->thumbnail))
                                    : 'https://placehold.co/500';
                            @endphp
                            <img src="{{ $relatedImageUrl }}" 
                                 alt="{{ $related->name }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                 loading="lazy">
                        </a>
                        <div class="p-4 flex-1 flex flex-col">
                            <a href="{{ route('store.product.show', $related->slug) }}" class="block mb-1 focus:outline-none focus:ring-2 focus:ring-primary rounded">
                                <h3 class="text-sm font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $related->name }}
                                </h3>
                            </a>
                            <p class="text-xs text-gray-500 mb-3">
                                {{ $related->categories->first()->name ?? __('store.general') }}
                            </p>
                            
                            <div class="mt-auto flex items-center justify-between gap-2">
                                <span class="text-lg font-bold text-gray-900">
                                    {{ $related->currency_symbol }}{{ number_format($related->price, 2) }}
                                </span>
                                @livewire('store.add-to-cart-button', ['productId' => $related->id], key('related-'.$related->id))
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    {{-- Size Guide Modal --}}
    @if(!empty($product->measurements['columns']) && !empty($product->measurements['rows']))
        <div x-show="sizeGuideOpen" 
             style="display: none;" 
             x-transition.opacity 
             class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
             role="dialog"
             aria-modal="true"
             aria-labelledby="size-guide-title"
             @click.self="sizeGuideOpen = false">
            
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-fade-in"
                 @click.stop>
                {{-- Modal Header --}}
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 bg-primary/10 text-primary rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                        </span>
                        <div>
                            <h3 id="size-guide-title" class="text-base font-bold text-gray-900">Product Size & Measurements</h3>
                            <p class="text-xs text-gray-500">All measurements are in <span class="font-semibold text-primary uppercase">{{ $product->measurements['unit'] ?? 'cm' }}</span></p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="sizeGuideOpen = false" 
                            class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-primary"
                            aria-label="Close size guide">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body / Table --}}
                <div class="p-5 overflow-y-auto space-y-4">
                    <div class="rounded-xl border border-gray-200 overflow-hidden shadow-xs">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                                <tr>
                                    <th class="py-3 px-4 bg-gray-100/80 font-extrabold text-gray-900">Size</th>
                                    @foreach($product->measurements['columns'] as $col)
                                        <th class="py-3 px-4 font-semibold text-gray-700">
                                            {{ $col }} <span class="text-[10px] text-gray-400 font-normal">({{ $product->measurements['unit'] ?? 'cm' }})</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($product->measurements['rows'] as $row)
                                    <tr class="hover:bg-primary/5 transition">
                                        <td class="py-3 px-4 font-bold text-gray-900 bg-gray-50/50">
                                            <span class="inline-block px-2 py-0.5 rounded-md bg-white border border-gray-200 font-mono shadow-2xs">
                                                {{ $row['label'] }}
                                            </span>
                                        </td>
                                        @foreach($row['values'] as $val)
                                            <td class="py-3 px-4 text-gray-700 font-medium font-mono">
                                                {{ $val !== '' && $val !== null ? $val : '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Tips Box --}}
                    <div class="p-3.5 bg-blue-50/60 rounded-xl border border-blue-100 text-xs text-blue-800 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="leading-relaxed">
                            <span class="font-bold">Measuring Tip:</span> For the best fit, measure a similar garment that fits you well and compare its measurements to the chart above.
                        </p>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button type="button" 
                            @click="sizeGuideOpen = false" 
                            class="px-4 py-2 bg-gray-900 text-white text-xs font-semibold rounded-xl hover:bg-black transition shadow-sm">
                        Close Size Guide
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Lightbox Modal --}}
    <div x-show="lightboxOpen" 
         style="display: none;" 
         x-transition.opacity 
         class="fixed inset-0 z-[9999] bg-black/95 backdrop-blur-sm flex items-center justify-center p-4"
         role="dialog"
         aria-modal="true"
         aria-labelledby="lightbox-title"
         @click.self="lightboxOpen = false">
        
        <div id="lightbox-title" class="sr-only">Product image lightbox viewer</div>
        
        <button @click="lightboxOpen = false" 
                class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white rounded-lg p-1"
                aria-label="Close lightbox">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <img :src="mainImage" 
             alt="{{ $product->name }} full size image" 
             class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
    </div>

</div>

{{-- Alpine.js Data Function --}}
<script>
function productDetailData() {
    return {
        activeMediaType: 'image',
        lightboxOpen: false,
        sizeGuideOpen: false,
        mainImage: '{{ $mainImageUrl ?? 'https://placehold.co/600' }}',
        zoomStyle: '',
        currentStock: @js($currentStock),
        selectedAttributes: @js($selectedAttributes),
        selectionMissing: @js($selectionMissing),
        showSuccess: @js($showSuccess),

        init() {
            // Listen for Livewire success message
            Livewire.on('reset-success', () => {
                setTimeout(() => { 
                    this.showSuccess = false;
                }, 2000);
            });

            // Watch for Livewire property changes
            this.$watch('$wire.currentStock', (value) => {
                this.currentStock = value;
            });
            
            this.$watch('$wire.selectedAttributes', (value) => {
                this.selectedAttributes = value;
            });

            this.$watch('$wire.selectionMissing', (value) => {
                this.selectionMissing = value;
            });

            this.$watch('$wire.showSuccess', (value) => {
                this.showSuccess = value;
            });
        },

        zoomImage(e) {
            const img = this.$refs.mainImg;
            if (!img) return;
            const rect = img.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            this.zoomStyle = `transform-origin: ${x}% ${y}%; transform: scale(2);`;
        },

        resetZoom() {
            this.zoomStyle = 'transform: scale(1);';
        }
    }
}
</script>
