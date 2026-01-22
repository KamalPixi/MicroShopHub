<?php

namespace App\Livewire;

use Livewire\Component;

class AddToCartButton extends Component
{
    public bool $added = false;

    public function add()
    {
        $this->added = true;

        // Notify the cart counter to increase
        $this->dispatch('itemAdded');

        // Reset button text after 1.5 seconds
        $this->dispatch('$refresh')->delay(1500);
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }
}
