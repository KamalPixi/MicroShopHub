<?php

namespace App\Livewire\Store;

use Livewire\Component;
use App\Models\Product;

class AddToCartButton extends Component
{
    public $productId;
    public $showQuantity = false; // Toggles between simple button vs quantity selector
    public $quantity = 1;
    public $isInCart = false;
    public $buttonText = 'Add to Cart';
    public $buttonClass = 'bg-primary hover:bg-blue-700';

    public function mount($productId, $showQuantity = false)
    {
        $this->productId = $productId;
        $this->showQuantity = $showQuantity;
        $this->checkCartState();
    }

    public function checkCartState()
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$this->productId])) {
            $this->isInCart = true;
            
            // If we are on the details page (showQuantity mode), sync the quantity
            if ($this->showQuantity) {
                $this->quantity = $cart[$this->productId]['quantity'];
            } else {
                // For grid view, show "In Cart" state
                $this->buttonText = 'In Cart';
                $this->buttonClass = 'bg-green-600 hover:bg-green-700';
            }
        } else {
            $this->isInCart = false;
            $this->buttonText = 'Add to Cart';
            $this->buttonClass = 'bg-primary hover:bg-blue-700';
        }
    }

    public function increment()
    {
        if ($this->quantity < 100) {
            $this->quantity++;
        }
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        $product = Product::find($this->productId);
        if (!$product) return;

        $cart = session()->get('cart', []);

        // Logic: If already in cart, update quantity. Else, add new.
        if (isset($cart[$this->productId])) {
            if ($this->showQuantity) {
                // On details page, set specific quantity
                $cart[$this->productId]['quantity'] = $this->quantity; 
            } else {
                // On grid, just increment by 1
                $cart[$this->productId]['quantity']++;
            }
        } else {
            $cart[$this->productId] = [
                "name" => $product->name,
                "quantity" => $this->showQuantity ? $this->quantity : 1,
                "price" => $product->price,
                "currency_symbol" => $product->currency_symbol,
                "thumbnail" => $product->thumbnail
            ];
        }

        session()->put('cart', $cart);

        // Update local state
        $this->isInCart = true;
        
        // Trigger global event (e.g., to update header cart counter)
        $this->dispatch('cartUpdated');

        // Visual Feedback (Flash "Added!")
        $this->buttonText = 'Added!';
        $this->buttonClass = 'bg-green-500';
        
        // Revert button text after 2 seconds (if not showQuantity mode)
        if (!$this->showQuantity) {
            $this->dispatch('reset-button-' . $this->productId); 
        }
    }

    public function render()
    {
        return view('livewire.store.add-to-cart-button');
    }
}
