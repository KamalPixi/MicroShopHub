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
        <a href="#" class="text-primary font-medium hover:text-blue-700">View All →</a>
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

            <div class="flex-none w-[220px] sm:w-[260px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group/card cursor-pointer">
                <div class="aspect-square overflow-hidden">
                    <img src="https://placehold.co/500x500" alt="Summer Dress" class="w-full h-full object-cover">
                </div>
                <div class="p-3">
                    <h3 class="font-semibold text-gray-900 mb-1 text-sm sm:text-base group-hover/card:text-primary">Summer Dress</h3>
                    <p class="text-xs text-gray-600 mb-2">Light and breezy fabric</p>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-primary text-base sm:text-lg">$59.99</span>
                        @livewire('add-to-cart-button')
                    </div>
                </div>
            </div>

            <div class="flex-none w-[220px] sm:w-[260px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group/card cursor-pointer">
                <div class="aspect-square overflow-hidden">
                    <img src="https://placehold.co/500x500" alt="Herbal Tea" class="w-full h-full object-cover">
                </div>
                <div class="p-3">
                    <h3 class="font-semibold text-gray-900 mb-1 text-sm sm:text-base group-hover/card:text-primary">Herbal Tea</h3>
                    <p class="text-xs text-gray-600 mb-2">Organic blend, 20 bags</p>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-primary text-base sm:text-lg">$14.99</span>
                        @livewire('add-to-cart-button')
                    </div>
                </div>
            </div>

            <div class="flex-none w-[220px] sm:w-[260px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group/card cursor-pointer">
                <div class="aspect-square overflow-hidden">
                    <img src="https://placehold.co/500x500" alt="Knitted Blanket" class="w-full h-full object-cover">
                </div>
                <div class="p-3">
                    <h3 class="font-semibold text-gray-900 mb-1 text-sm sm:text-base group-hover/card:text-primary">Knitted Blanket</h3>
                    <p class="text-xs text-gray-600 mb-2">Soft wool, queen size</p>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-primary text-base sm:text-lg">$89.99</span>
                        @livewire('add-to-cart-button')
                    </div>
                </div>
            </div>

            <div class="flex-none w-[220px] sm:w-[260px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group/card cursor-pointer">
                <div class="aspect-square overflow-hidden">
                    <img src="https://placehold.co/500x500" alt="Running Shoes" class="w-full h-full object-cover">
                </div>
                <div class="p-3">
                    <h3 class="font-semibold text-gray-900 mb-1 text-sm sm:text-base group-hover/card:text-primary">Running Shoes</h3>
                    <p class="text-xs text-gray-600 mb-2">Lightweight, breathable</p>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-primary text-base sm:text-lg">$99.99</span>
                        @livewire('add-to-cart-button')
                    </div>
                </div>
            </div>

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
