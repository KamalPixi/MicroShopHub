<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductDetails extends Component
{
    public $product;
    public $relatedProducts;
    
    // State
    public $quantity = 1;
    public $selectedAttributes = []; // Format: [attribute_id => value_id]
    public $currentPrice;
    public $currentStock;
    public $selectedVariation = null;
    
    // UI Feedback
    public $showSuccess = false;

    public function mount($product, $relatedProducts)
    {
        $this->product = $product;
        $this->relatedProducts = $relatedProducts;
        
        // Initialize defaults
        $this->currentPrice = $product->price;
        $this->currentStock = $product->stock;
        
        // If product has variations, we set stock to 0 until they select options
        if ($this->product->has_variations) {
            $this->currentStock = 0; 
            $this->currentPrice = $product->variations->min('price'); // Show "From $X" logic visually if needed
        }
    }

    public function updatedSelectedAttributes()
    {
        $this->checkVariation();
    }

    public function selectAttribute($attributeId, $valueId)
    {
        $this->selectedAttributes[$attributeId] = $valueId;
        $this->checkVariation();
    }

    public function checkVariation()
    {
        if (!$this->product->has_variations) return;

        // 1. Check if all attributes are selected
        $requiredAttributes = $this->product->attributes->pluck('id')->unique();
        if (count($this->selectedAttributes) < $requiredAttributes->count()) {
            return; // Still waiting for user to select all options
        }

        // 2. Find matching variation
        // We filter variations where the 'values' collection contains ALL selected value IDs
        $variation = $this->product->variations->first(function ($var) {
            $varValueIds = $var->values->pluck('id')->toArray();
            // Check if selected attributes are a subset of this variation's values
            // (intersection of arrays should match selected)
            return !array_diff($this->selectedAttributes, $varValueIds);
        });

        if ($variation) {
            $this->selectedVariation = $variation;
            $this->currentPrice = $variation->price;
            $this->currentStock = $variation->stock;
        } else {
            // Combination not found
            $this->selectedVariation = null;
            $this->currentStock = 0; 
        }
    }

    public function increment()
    {
        if ($this->product->has_variations && !$this->selectedVariation) return;
        
        // Check stock limit
        if ($this->quantity < $this->currentStock) {
            $this->quantity++;
        }
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart($buyNow = false)
    {
        // Validation
        if ($this->product->has_variations) {
            if (!$this->selectedVariation) {
                $this->dispatch('notify', ['message' => 'Please select all options first.', 'type' => 'error']);
                return;
            }
            if ($this->currentStock <= 0) {
                 $this->dispatch('notify', ['message' => 'Selected option is out of stock.', 'type' => 'error']);
                 return;
            }
        } else {
             if ($this->product->stock <= 0) {
                 $this->dispatch('notify', ['message' => 'Product is out of stock.', 'type' => 'error']);
                 return;
             }
        }

        // Prepare Cart Data
        $cart = session()->get('cart', []);
        
        // Unique ID logic: If variation, use variation ID, else product ID
        // To allow multiple variations of same product, key should be distinct.
        $cartKey = $this->product->id;
        if ($this->selectedVariation) {
            $cartKey = $this->product->id . '-' . $this->selectedVariation->id;
        }

        // Format Attributes for display in Cart
        $optionsDisplay = [];
        if ($this->selectedVariation) {
            foreach($this->selectedVariation->values as $val) {
                $optionsDisplay[$val->attribute->name] = $val->value;
            }
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $this->quantity;
        } else {
            $cart[$cartKey] = [
                "product_id" => $this->product->id,
                "variation_id" => $this->selectedVariation ? $this->selectedVariation->id : null,
                "name" => $this->product->name,
                "quantity" => $this->quantity,
                "price" => $this->currentPrice,
                "thumbnail" => $this->product->thumbnail,
                "attributes" => $optionsDisplay // e.g. ['Color' => 'Red', 'Size' => 'L']
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cartUpdated'); // Updates the header cart counter

        if ($buyNow) {
            return redirect()->route('store.index'); // Or route('checkout')
        }

        // Show success visual
        $this->showSuccess = true;
        
        // Reset success message after 2s
        $this->dispatch('reset-success'); 
    }

    public function render()
    {
        return view('livewire.product-details');
    }
}
