<section class="mb-12 relative group" x-data="{
    scrollAmount: 300,
    scrollContainer: null,
    scrollLeft() {
        this.$refs.container.scrollBy({ left: -this.scrollAmount, behavior: 'smooth' });
    },
    scrollRight() {
        this.$refs.container.scrollBy({ left: this.scrollAmount, behavior: 'smooth' });
    }
}">
    <div class="flex items-center justify-between mb-6 px-1">
        <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
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

            <div class="flex-none w-[200px] sm:w-[250px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group/card cursor-pointer">
                <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                    <img src="https://placehold.co/500x500" alt="Cotton T-Shirt" class="w-full h-full object-cover">
                </div>
                <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover/card:text-primary">Cotton T-Shirt</h3>
                <p class="text-xs text-gray-600 mb-2">Comfortable daily wear</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary">$29.99</span>
                    @livewire('add-to-cart-button')
                </div>
            </div>

            <div class="flex-none w-[200px] sm:w-[250px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group/card cursor-pointer">
                <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                    <img src="https://placehold.co/500x500" alt="Vitamin D3" class="w-full h-full object-cover">
                </div>
                <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover/card:text-primary">Vitamin D3</h3>
                <p class="text-xs text-gray-600 mb-2">60 capsules</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary">$19.99</span>
                    @livewire('add-to-cart-button')
                </div>
            </div>

            <div class="flex-none w-[200px] sm:w-[250px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group/card cursor-pointer">
                <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                    <img src="https://placehold.co/500x500" alt="Handmade Scarf" class="w-full h-full object-cover">
                </div>
                <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover/card:text-primary">Handmade Scarf</h3>
                <p class="text-xs text-gray-600 mb-2">Wool knitted</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary">$45.00</span>
                    @livewire('add-to-cart-button')
                </div>
            </div>

            <div class="flex-none w-[200px] sm:w-[250px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group/card cursor-pointer">
                <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                    <img src="https://placehold.co/500x500" alt="Denim Jacket" class="w-full h-full object-cover">
                </div>
                <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover/card:text-primary">Denim Jacket</h3>
                <p class="text-xs text-gray-600 mb-2">Classic fit</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary">$79.99</span>
                    @livewire('add-to-cart-button')
                </div>
            </div>

            <div class="flex-none w-[200px] sm:w-[250px] snap-start bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 group/card cursor-pointer">
                <div class="aspect-square rounded-lg mb-3 overflow-hidden">
                    <img src="https://placehold.co/500x500" alt="Clay Pottery" class="w-full h-full object-cover">
                </div>
                <h3 class="font-medium text-gray-900 text-sm mb-1 group-hover/card:text-primary">Clay Pottery</h3>
                <p class="text-xs text-gray-600 mb-2">Handcrafted vase</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-primary">$35.00</span>
                    @livewire('add-to-cart-button')
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
