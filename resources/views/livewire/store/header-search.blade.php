<div class="relative w-full">
    <div class="flex w-full border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-primary focus-within:border-transparent bg-white">
        
        <div class="relative border-r border-gray-300 bg-gray-50 hidden sm:block">
            <select wire:model.live="category" 
                    class="appearance-none bg-transparent h-full pl-4 pr-8 py-2 text-sm text-gray-600 focus:outline-none cursor-pointer hover:bg-gray-100 max-w-[150px] truncate">
                <option value="">{{ __('store.all_categories') }}</option>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                @endif
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
            </div>
        </div>

        <div class="flex-1 relative">
            <input wire:model.live.debounce.300ms="query" 
                   wire:keydown.enter="performSearch"
                   type="text" 
                   placeholder="{{ __('store.search_products') }}"
                   class="w-full pl-10 pr-12 py-2 border-none focus:ring-0 h-full text-gray-700 placeholder-gray-400"
                   autocomplete="off">
            
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            @if(!empty($query))
                <button type="button"
                        wire:loading.remove
                        wire:target="query"
                        wire:click="$set('query','')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors"
                        aria-label="{{ __('store.clear') }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif

            <div wire:loading wire:target="query" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>

    @if(!empty($query) && strlen($query) >= 2)
        <div class="absolute top-full left-0 right-0 bg-white shadow-xl rounded-b-lg border border-gray-200 mt-1 z-50 overflow-hidden">
            @if(count($results) > 0)
                <ul class="divide-y divide-gray-100">
                    @foreach($results as $product)
                        @php
                            $saleInfo = !empty($flashSaleMap) ? ($flashSaleMap[$product->id] ?? null) : null;
                        @endphp
                        <li class="hover:bg-gray-50 transition-colors">
                            <a href="{{ route('store.product.show', $product->slug) }}" class="flex items-center px-4 py-3 group">
                                <div class="flex-shrink-0 h-10 w-10 border border-gray-200 rounded overflow-hidden bg-gray-100">
                                     <img src="{{ $product->thumbnail ? (Str::startsWith($product->thumbnail, ['http']) ? $product->thumbnail : Storage::url($product->thumbnail)) : 'https://placehold.co/50' }}" 
                                          alt="{{ $product->name }}" 
                                          class="w-full h-full object-cover">
                                </div>
                                <div class="ml-3 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate group-hover:text-primary">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Str::limit(strip_tags($product->description), 40) }}</p>
                                </div>
                                <div class="ml-2">
                                    @if($saleInfo)
                                        @if($product->has_variations && empty($product->price))
                                            <span class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-gray-500 text-right">{{ __('store.from') }}</span>
                                        @endif
                                        <span class="block text-[10px] font-medium text-gray-400 line-through text-right">{{ $product->currency_symbol }}{{ number_format($saleInfo['original_price'], 2) }}</span>
                                        <span class="text-sm font-bold text-primary">{{ $product->currency_symbol }}{{ number_format($saleInfo['sale_price'], 2) }}</span>
                                    @else
                                        @if($product->price)
                                            <span class="text-sm font-bold text-primary">{{ $product->currency_symbol }}{{ number_format($product->price, 2) }}</span>
                                        @elseif($product->has_variations)
                                            <span class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">{{ __('store.see_options') }}</span>
                                        @endif
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                
                <div class="bg-gray-50 p-2 text-center border-t border-gray-100">
                    <button wire:click="performSearch" class="text-primary text-sm font-medium hover:underline w-full py-1">
                        {{ __('store.view_all_results') }} for "{{ $query }}"
                    </button>
                </div>
            @else
                <div class="px-4 py-4 text-center text-sm text-gray-500">
                    {{ __('store.no_results_for', ['query' => $query]) }}
                </div>
            @endif
        </div>
    @endif
</div>
