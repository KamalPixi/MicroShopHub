<?php

namespace App\Livewire;

use Livewire\Component;

class CartCounter extends Component
{
    public $count = 0;

    protected $listeners = ['itemAdded' => 'increment'];

    public function mount()
    {
        $this->count = session('cart_count', 3); // Start with 3 as in your design
    }

    public function increment()
    {
        $this->count++;
        session(['cart_count' => $this->count]);
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
