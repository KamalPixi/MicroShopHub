<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class CartCounter extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    #[On('cartUpdated')] 
    public function updateCount()
    {
        $cart = session()->get('cart', []);
        
        // If we only want unique products, we can use count($cart)
        $this->count = count($cart);
        
        // This calculates the TOTAL quantity (e.g., 2 shirts + 1 hat = 3 items)
        // $this->count = array_sum(array_column($cart, 'quantity'));
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
