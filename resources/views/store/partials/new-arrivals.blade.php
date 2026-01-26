<section class="mb-12 relative group" x-data="{
    scrollAmount: 280,
    scrollLeft() {
        this.$refs.container.scrollBy({ left: -this.scrollAmount, behavior: 'smooth' });
    },
    scrollRight() {
        this.$refs.container.scrollBy({ left: this.scrollAmount, behavior: 'smooth' });
    }
}">
    <div class="flex items-center justify-between mb-6 px-1">
        <h2 class="text-2xl font-bold text-gray-900">New Arrivals</h2>
        <a href="{{ route('store.index') }}" class="text-primary font-medium hover:text-blue-700">View All →</a>
    </div>

    <div class="relative">
        <button 
            @click="scrollLeft()"
            class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 bg-white shadow-lg border border-gray-100 rounded-full p-2 text-gray-600 hover:text-primary hover:scale-110 transition-all opacity-0 group-hover:opacity-100 hidden md:block"
            aria-label="Scroll Left">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </button>

        <div x-ref="container" class="flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-4 no-scrollbar">

            @if(isset($newArrivals) && $newArrivals->count() > 0)
                @foreach($newArrivals as $product)
                    <div class="flex-none w-[220px] sm:w-[260px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group/card cursor-pointer">
                        
                        <a href="{{ route('store.product.show', $product->slug) }}" class="block aspect-square overflow-hidden bg-gray-100 relative">
                            @php
                                $imageUrl = 'https://placehold.co/500x500?text=No+Image';
                                if ($product->thumbnail) {
                                    if (Str::startsWith($product->thumbnail, ['http://', 'https://'])) {
                                        $imageUrl = $product->thumbnail;
                                    } else {
                                        $imageUrl = Storage::url($product->thumbnail);
                                    }
                                }
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover/card:scale-105">
                        </a>

                        <div class="p-3">
                            <a href="{{ route('store.product.show', $product->slug) }}" class="block">
                                <h3 class="font-semibold text-gray-900 mb-1 text-sm sm:text-base group-hover/card:text-primary truncate">
                                    {{ $product->name }}
                                </h3>
                            </a>
                            
                            <p class="text-xs text-gray-600 mb-2 truncate">
                                {{ Str::limit(strip_tags($product->description), 40) }}
                            </p>

                            <div class="flex items-center justify-between">
                                <span class="font-bold text-primary text-base sm:text-lg">
                                    @if($product->price)
                                        {{$product->currency_symbol}}{{ number_format($product->price, 2) }}
                                    @else
                                        <span class="text-xs text-gray-500">View</span>
                                    @endif
                                </span>
                                
                                @livewire('store.add-to-cart-button', ['productId' => $product->id], key('new-arrival-'.$product->id))
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="w-full text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                    No new arrivals found.
                </div>
            @endif

        </div>

        <button 
            @click="scrollRight()"
            class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 bg-white shadow-lg border border-gray-100 rounded-full p-2 text-gray-600 hover:text-primary hover:scale-110 transition-all opacity-0 group-hover:opacity-100 hidden md:block"
            aria-label="Scroll Right">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </button>

    </div>
</section>

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
