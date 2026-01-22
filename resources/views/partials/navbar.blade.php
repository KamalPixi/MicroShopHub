<nav class="bg-white border-b border-gray-200 relative z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center space-x-8 h-12 items-center">
            
            <a href="#" class="text-gray-600 hover:text-primary font-medium transition-colors">All Products</a>

            <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button class="flex items-center space-x-1 text-gray-600 group-hover:text-primary font-medium transition-colors focus:outline-none py-3">
                    <span>Clothing</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 shadow-lg rounded-b-lg py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Men's Fashion</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Women's Fashion</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Kids' Wear</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Accessories</a>
                </div>
            </div>

            <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button class="flex items-center space-x-1 text-gray-600 group-hover:text-primary font-medium transition-colors focus:outline-none py-3">
                    <span>Health & Medicine</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     class="absolute top-full left-1/2 -translate-x-1/2 w-56 bg-white border border-gray-100 shadow-lg rounded-b-lg py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Vitamins & Supplements</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">First Aid</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Personal Care</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Medical Devices</a>
                </div>
            </div>

            <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button class="flex items-center space-x-1 text-gray-600 group-hover:text-primary font-medium transition-colors focus:outline-none py-3">
                    <span>Handmade Items</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     class="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 shadow-lg rounded-b-lg py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Pottery & Ceramics</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Knitted Goods</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Woodworking</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Jewelry</a>
                </div>
            </div>

            <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button class="flex items-center space-x-1 text-gray-600 group-hover:text-primary font-medium transition-colors focus:outline-none py-3">
                    <span>Electronics</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     class="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 shadow-lg rounded-b-lg py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Phones & Tablets</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Laptops</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Audio & Headphones</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Cameras</a>
                </div>
            </div>

            <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button class="flex items-center space-x-1 text-gray-600 group-hover:text-primary font-medium transition-colors focus:outline-none py-3">
                    <span>Home & Garden</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     class="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 shadow-lg rounded-b-lg py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Home Decor</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Kitchen & Dining</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Garden Tools</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Furniture</a>
                </div>
            </div>

        </div>
    </div>
</nav>
