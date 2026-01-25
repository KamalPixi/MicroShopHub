<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductDetails extends Component
{
    public $product;
    public $relatedProducts;
    
    // Data
    public $productOptions = []; 
    
    // State
    public $quantity = 1;
    public $selectedAttributes = []; // [attribute_id => value_id]
    public $currentPrice;
    public $currentStock;
    public $selectedVariation = null;
    
    // UI Feedback
    public $showSuccess = false;

    public function mount($product, $relatedProducts)
    {
        $this->product = $product;
        $this->relatedProducts = $relatedProducts;
        
        $this->currentPrice = $product->price;
        $this->currentStock = $product->stock;
        
        if ($this->product->has_variations) {
            $this->currentStock = 0;
            // Show the lowest price initially to encourage clicks
            $this->currentPrice = $product->variations->min('price'); 
            $this->buildProductOptions();
        }
    }

    public function buildProductOptions()
    {
        $this->product->load('variations.values.attribute');
        
        $allValues = $this->product->variations->flatMap(fn($v) => $v->values);

        $this->productOptions = $allValues->groupBy('attribute_id')->map(function ($values) {
            $first = $values->first();
            return [
                'id' => $first->attribute_id,
                'name' => $first->attribute->name,
                'values' => $values->unique('id')->values()
            ];
        })->values()->toArray();
    }

    // Helper to get the NAME of the selected option (e.g., "Red" instead of "15")
    public function getSelectedValueName($attributeId)
    {
        if (!isset($this->selectedAttributes[$attributeId])) return null;
        
        $valueId = $this->selectedAttributes[$attributeId];
        
        // Find the value name from our pre-loaded options to avoid DB query
        foreach ($this->productOptions as $option) {
            if ($option['id'] == $attributeId) {
                $found = $option['values']->firstWhere('id', $valueId);
                return $found ? $found->value : null;
            }
        }
        return null;
    }

    public function selectAttribute($attributeId, $valueId)
    {
        $this->selectedAttributes[$attributeId] = $valueId;
        $this->checkVariation();
    }

    public function resetSelection()
    {
        $this->selectedAttributes = [];
        $this->selectedVariation = null;
        $this->currentStock = 0;
        $this->currentPrice = $this->product->variations->min('price');
    }

    public function checkVariation()
    {
        if (!$this->product->has_variations) return;

        if (count($this->selectedAttributes) < count($this->productOptions)) {
            // Reset to base price if they deselected something or are incomplete
            $this->selectedVariation = null;
            $this->currentStock = 0;
            return; 
        }

        $variation = $this->product->variations->first(function ($var) {
            $varValueIds = $var->values->pluck('id')->toArray();
            return !array_diff($this->selectedAttributes, $varValueIds) && 
                   count($varValueIds) == count($this->selectedAttributes);
        });

        if ($variation) {
            $this->selectedVariation = $variation;
            $this->currentPrice = $variation->price; // Update Price Immediately
            $this->currentStock = $variation->stock;
        } else {
            $this->selectedVariation = null;
            $this->currentStock = 0;
        }
    }

    public function increment()
    {
        if ($this->product->has_variations && !$this->selectedVariation) return;
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
        if ($this->product->has_variations) {
            if (!$this->selectedVariation) return;
            if ($this->currentStock <= 0) return;
        } elseif ($this->product->stock <= 0) {
            return;
        }

        $cart = session()->get('cart', []);
        $cartKey = $this->product->id . ($this->selectedVariation ? '-' . $this->selectedVariation->id : '');

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
                "attributes" => $optionsDisplay
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cartUpdated'); 

        if ($buyNow) return redirect()->route('store.index');

        $this->showSuccess = true;
        $this->dispatch('reset-success'); 
    }

    public function render()
    {
        return view('livewire.product-details');
    }
}
