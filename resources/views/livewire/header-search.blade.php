<div class="relative w-full">
    <div class="flex w-full border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-primary focus-within:border-transparent bg-white">
        
        <div class="relative border-r border-gray-300 bg-gray-50">
            <select wire:model.live="category" 
                    class="appearance-none bg-transparent h-full pl-4 pr-8 py-2 text-sm text-gray-600 focus:outline-none cursor-pointer hover:bg-gray-100 max-w-[100px] sm:max-w-[150px] truncate">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
            </div>
        </div>

        <div class="flex-1 relative">
            <input wire:model.live.debounce.300ms="query" 
                   type="text" 
                   placeholder="Search for products..."
                   class="w-full pl-10 pr-4 py-2 border-none focus:ring-0 h-full text-gray-700 placeholder-gray-400"
                   autocomplete="off">
            
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div wire:loading wire:target="query" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>

    @if(strlen($query) >= 2)
        <div class="absolute top-full left-0 right-0 bg-white shadow-xl rounded-b-lg border border-gray-200 mt-1 z-50 overflow-hidden">
            @if(count($results) > 0)
                <ul class="divide-y divide-gray-100">
                    @foreach($results as $product)
                        <li class="hover:bg-gray-50 transition-colors">
                            <a href="#" class="flex items-center px-4 py-3">
                                <div class="flex-shrink-0 h-10 w-10 border border-gray-200 rounded overflow-hidden">
                                    @php
                                        $img = 'https://placehold.co/50';
                                        if ($product->thumbnail) {
                                            $img = Str::startsWith($product->thumbnail, ['http','https']) 
                                                ? $product->thumbnail 
                                                : Storage::url($product->thumbnail);
                                        }
                                    @endphp
                                    <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Str::limit(strip_tags($product->description), 60) }}</p>
                                </div>
                                <div class="ml-2">
                                    <span class="text-sm font-bold text-primary">${{ number_format($product->price, 2) }}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-4 text-center text-sm text-gray-500">
                    No products found for "<span class="font-semibold">{{ $query }}</span>"
                </div>
            @endif
        </div>
    @endif
</div>
