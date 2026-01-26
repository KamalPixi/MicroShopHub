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

                @if(isset($homeCategories) && $homeCategories->count() > 0)
                    @foreach($homeCategories as $category)
                        <a href="{{ route('store.search', ['category' => $category->id]) }}" 
                           class="flex-none w-64 md:w-72 snap-start relative overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-all duration-300 cursor-pointer group/card block">
                            
                            @php
                                $imageUrl = 'https://placehold.co/400x225?text=No+Image';
                                if ($category->thumbnail) {
                                    if (Str::startsWith($category->thumbnail, ['http://', 'https://'])) {
                                        $imageUrl = $category->thumbnail;
                                    } else {
                                        $imageUrl = Storage::url($category->thumbnail);
                                    }
                                }
                            @endphp

                            <img src="{{ $imageUrl }}" alt="{{ $category->name }}" class="w-full h-40 object-cover">
                            
                            <div class="p-3">
                                <h3 class="text-base font-semibold text-gray-900 group-hover/card:text-primary transition-colors line-clamp-1">
                                    {{ $category->name }}
                                </h3>
                                @if($category->subtitle)
                                    <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                                        {{ $category->subtitle }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="w-full text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                        <p>No categories found to display.</p>
                    </div>
                @endif

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
