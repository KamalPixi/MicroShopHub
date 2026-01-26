<div x-data="{ 
        init() {
            Livewire.on('reset-button-{{ $productId }}', () => {
                setTimeout(() => {
                    $wire.checkCartState(); 
                }, 2000);
            });
        }
    }" 
    class="flex items-center {{ $showQuantity ? 'w-full' : 'w-auto' }}"> {{-- FIX: Only full width on details page --}}

    @if($showQuantity)
        <div class="flex items-center space-x-3 w-full">
            <div class="flex items-center border border-gray-300 rounded-lg h-10 shadow-sm bg-white">
                <button wire:click="decrement" class="px-3 text-gray-600 hover:text-primary transition text-lg font-medium focus:outline-none h-full">-</button>
                <input type="text" wire:model="quantity" readonly class="w-10 text-center border-none p-0 text-gray-900 font-bold focus:ring-0 bg-transparent text-sm h-full">
                <button wire:click="increment" class="px-3 text-gray-600 hover:text-primary transition text-lg font-medium focus:outline-none h-full">+</button>
            </div>

            <button wire:click="addToCart" 
                    wire:loading.attr="disabled"
                    class="flex-1 font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg h-10 flex items-center justify-center whitespace-nowrap transition-all duration-200 text-white text-sm
                           {{ $isInCart ? 'bg-green-600 hover:bg-green-700' : 'bg-primary hover:bg-blue-700' }}">
                
                <span wire:loading.remove wire:target="addToCart">
                    {{ $isInCart ? 'Update Cart' : 'Add to Cart' }}
                </span>
                
                <span wire:loading wire:target="addToCart">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>

    @else
        <button wire:click="addToCart"
                wire:loading.attr="disabled"
                class="text-xs font-semibold px-3 py-2 rounded-lg transition-all transform active:scale-95 flex items-center shadow-sm border
                       {{ $isInCart 
                           ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' 
                           : 'bg-white text-gray-700 border-gray-200 hover:border-primary hover:text-primary' }}"
                title="{{ $isInCart ? 'Added to cart' : 'Add to Cart' }}">
            
            <svg wire:loading wire:target="addToCart" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <span wire:loading.remove wire:target="addToCart" class="flex items-center">
                @if($isInCart || $buttonText == 'Added!')
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Added
                @else
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Add
                @endif
            </span>
        </button>
    @endif
</div>
