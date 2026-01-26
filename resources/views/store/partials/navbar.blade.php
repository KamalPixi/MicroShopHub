<nav class="bg-white border-b border-gray-200 relative z-10" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-14 flex items-center justify-between md:justify-center">

            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary">
                <span class="sr-only">Open main menu</span>
                <svg x-show="!mobileMenuOpen" class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg x-show="mobileMenuOpen" x-cloak class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="md:hidden font-bold text-gray-900 text-lg">
                Categories
            </div>

            <div class="hidden md:flex justify-center space-x-8 items-center h-full">
                
                <a href="{{ route('store.index') }}" class="text-gray-600 hover:text-primary font-medium transition-colors text-sm lg:text-base">All Products</a>

                @if(isset($navbarCategories) && $navbarCategories->count() > 0)
                    @foreach($navbarCategories as $category)
                        
                        @if($category->children->isNotEmpty())
                            <div class="relative group h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                <button class="flex items-center space-x-1 text-gray-600 group-hover:text-primary font-medium transition-colors focus:outline-none">
                                    <span class="text-sm lg:text-base">{{ $category->name }}</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 mt-2"
                                     x-transition:enter-end="opacity-100 mt-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 mt-0"
                                     x-transition:leave-end="opacity-0 mt-2"
                                     class="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 shadow-lg rounded-b-lg py-2 min-w-[200px]">
                                    
                                    @foreach($category->children as $child)
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach

                                </div>
                            </div>
                        @else
                            <a href="#" class="text-gray-600 hover:text-primary font-medium transition-colors text-sm lg:text-base">
                                {{ $category->name }}
                            </a>
                        @endif

                    @endforeach
                @endif

            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-gray-200 bg-gray-50">
        <div class="px-2 pt-2 pb-3 space-y-1">
            
            <a href="{{ route('store.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-100">All Products</a>

            @if(isset($navbarCategories) && $navbarCategories->count() > 0)
                @foreach($navbarCategories as $category)
                    
                    @if($category->children->isNotEmpty())
                        <div x-data="{ expanded: false }">
                            <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-100 focus:outline-none">
                                <span>{{ $category->name }}</span>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="expanded" class="pl-4 space-y-1" x-collapse>
                                @foreach($category->children as $child)
                                    <a href="#" class="block px-3 py-2 rounded-md text-sm text-gray-600 hover:text-primary hover:bg-gray-100">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-100">
                            {{ $category->name }}
                        </a>
                    @endif

                @endforeach
            @endif

        </div>
    </div>
</nav>
