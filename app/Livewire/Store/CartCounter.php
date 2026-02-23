<?php

namespace App\Livewire\Store;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\CartService;

class CartCounter extends Component
{
    protected CartService $cartService;

    public $count = 0;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->updateCount();
    }

    #[On('cartUpdated')] 
    public function updateCount()
    {
        $cart = $this->cartService->getCart();
        
        // If we only want unique products, we can use count($cart)
        $this->count = count($cart);
        
        // This calculates the TOTAL quantity (e.g., 2 shirts + 1 hat = 3 items)
        // $this->count = array_sum(array_column($cart, 'quantity'));
    }

    public function render()
    {
        return view('livewire.store.cart-counter');
    }
}
