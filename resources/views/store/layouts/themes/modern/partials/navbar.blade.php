@if(request()->routeIs('store.index'))
<nav class="relative z-30" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mt-2 rounded-2xl border border-gray-200 bg-white/90 shadow-sm backdrop-blur">
            <div class="flex items-center justify-between gap-4 px-4 py-3">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-lg border border-gray-200 text-gray-500 hover:text-primary hover:border-primary/30 focus:outline-none focus:ring-2 focus:ring-primary">
                    <span class="sr-only">Open main menu</span>
                    <svg x-show="!mobileMenuOpen" class="block h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="block h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="hidden md:flex flex-wrap items-center gap-2">
                    <a href="{{ route('store.index') }}" class="inline-flex items-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-primary/25 hover:text-primary">All Products</a>
                    @if(isset($navbarCategories) && $navbarCategories->count() > 0)
                        @foreach($navbarCategories as $category)
                            @if($category->children->isNotEmpty())
                                <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                    <div class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition group-hover:border-primary/25 group-hover:text-primary focus:outline-none">
                                        <a href="{{ route('store.search', ['category' => $category->id]) }}" class="hover:text-primary">
                                            {{ $category->name }}
                                        </a>
                                        <button type="button" class="inline-flex items-center" aria-label="Open {{ $category->name }} categories">
                                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 translate-y-1"
                                         class="absolute left-1/2 top-full z-40 mt-3 w-56 -translate-x-1/2 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
                                        @foreach($category->children as $child)
                                            <a href="{{ route('store.search', ['category' => $child->id]) }}" class="block px-4 py-3 text-sm text-gray-700 transition hover:bg-primary/5 hover:text-primary">
                                                {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('store.search', ['category' => $category->id]) }}" class="inline-flex items-center rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-primary/25 hover:text-primary">{{ $category->name }}</a>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="md:hidden text-sm font-semibold text-gray-800">Categories</div>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" x-cloak class="md:hidden mt-2 border-t border-gray-200 bg-white/95 backdrop-blur shadow-sm relative z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 space-y-1">
            <a href="{{ route('store.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-primary/5 hover:text-primary">All Products</a>
            @if(isset($navbarCategories) && $navbarCategories->count() > 0)
                @foreach($navbarCategories as $category)
                    @if($category->children->isNotEmpty())
                        <div x-data="{ expanded: false }">
                            <div class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-semibold text-gray-700 hover:bg-primary/5 hover:text-primary">
                                <a href="{{ route('store.search', ['category' => $category->id]) }}" class="hover:text-primary">{{ $category->name }}</a>
                                <button type="button" @click="expanded = !expanded" class="inline-flex items-center" aria-label="Open {{ $category->name }} categories">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            <div x-show="expanded" x-transition class="pl-3 pt-1 space-y-1">
                                @foreach($category->children as $child)
                                    <a href="{{ route('store.search', ['category' => $child->id]) }}" class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-primary/5 hover:text-primary">{{ $child->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ route('store.search', ['category' => $category->id]) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-primary/5 hover:text-primary">{{ $category->name }}</a>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</nav>
@endif
