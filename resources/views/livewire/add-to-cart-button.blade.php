<button
    wire:click="add"
    class="text-sm px-4 py-2 rounded-lg text-white transition-colors
           {{ $added ? 'bg-green-500 hover:bg-green-600' : 'bg-primary hover:bg-blue-700' }}"
>
    {{ $added ? 'Added!' : 'Add to Cart' }}
</button>
