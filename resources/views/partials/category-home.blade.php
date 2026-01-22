<section class="mb-8 relative group" x-data="{
    scrollAmount: 320,
    scrollLeft() {
        this.$refs.container.scrollBy({ left: -this.scrollAmount, behavior: 'smooth' });
    },
    scrollRight() {
        this.$refs.container.scrollBy({ left: this.scrollAmount, behavior: 'smooth' });
    }
}">
    <div class="mb-4 px-1">
        <h2 class="text-2xl font-bold text-gray-900">Shop by Category</h2>
    </div>

    <div class="relative">
        <button @click="scrollLeft()"
                class="absolute left-[-16px] top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur-sm shadow-md rounded-full p-2 hover:bg-white hover:shadow-lg hover:scale-110 transition-all duration-300 hidden md:flex items-center justify-center border border-gray-200 opacity-0 group-hover:opacity-100">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div x-ref="container" class="overflow-x-auto scrollbar-hide scroll-smooth snap-x snap-mandatory pb-2">
            <div class="flex gap-4 py-3 px-1">

                <div class="flex-none w-64 md:w-72 snap-start relative overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-all duration-300 cursor-pointer group/card">
                    <img src="https://placehold.co/400x225" alt="Fashion & Clothing" class="w-full h-40 object-cover">
                    <div class="p-3">
                        <h3 class="text-base font-semibold text-gray-900 group-hover/card:text-primary transition-colors line-clamp-1">Fashion & Clothing</h3>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">Trendy outfits for all seasons</p>
                    </div>
                </div>

                <div class="flex-none w-64 md:w-72 snap-start relative overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-all duration-300 cursor-pointer group/card">
                    <img src="https://placehold.co/400x225/4CAF50/white" alt="Health & Medicine" class="w-full h-40 object-cover">
                    <div class="p-3">
                        <h3 class="text-base font-semibold text-gray-900 group-hover/card:text-primary transition-colors line-clamp-1">Health & Medicine</h3>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">Quality healthcare products</p>
                    </div>
                </div>

                <div class="flex-none w-64 md:w-72 snap-start relative overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-all duration-300 cursor-pointer group/card">
                    <img src="https://placehold.co/400x225/FF9800/white" alt="Handmade Crafts" class="w-full h-40 object-cover">
                    <div class="p-3">
                        <h3 class="text-base font-semibold text-gray-900 group-hover/card:text-primary transition-colors line-clamp-1">Handmade Crafts</h3>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">Unique artisanal products</p>
                    </div>
                </div>

                <div class="flex-none w-64 md:w-72 snap-start relative overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-all duration-300 cursor-pointer group/card">
                    <img src="https://placehold.co/400x225/2196F3/white" alt="Electronics" class="w-full h-40 object-cover">
                    <div class="p-3">
                        <h3 class="text-base font-semibold text-gray-900 group-hover/card:text-primary transition-colors line-clamp-1">Electronics</h3>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">Latest gadgets & tech</p>
                    </div>
                </div>

                <div class="flex-none w-64 md:w-72 snap-start relative overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-all duration-300 cursor-pointer group/card">
                    <img src="https://placehold.co/400x225/795548/white" alt="Home & Garden" class="w-full h-40 object-cover">
                    <div class="p-3">
                        <h3 class="text-base font-semibold text-gray-900 group-hover/card:text-primary transition-colors line-clamp-1">Home & Garden</h3>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">Decor and essentials</p>
                    </div>
                </div>

                <div class="flex-none w-64 md:w-72 snap-start relative overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-all duration-300 cursor-pointer group/card">
                    <img src="https://placehold.co/400x225/E91E63/white" alt="Beauty & Personal Care" class="w-full h-40 object-cover">
                    <div class="p-3">
                        <h3 class="text-base font-semibold text-gray-900 group-hover/card:text-primary transition-colors line-clamp-1">Beauty & Personal Care</h3>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">Premium skincare & cosmetics</p>
                    </div>
                </div>

            </div>
        </div>

        <button @click="scrollRight()"
                class="absolute right-[-16px] top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur-sm shadow-md rounded-full p-2 hover:bg-white hover:shadow-lg hover:scale-110 transition-all duration-300 hidden md:flex items-center justify-center border border-gray-200 opacity-0 group-hover:opacity-100">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</section>

<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
</style>
