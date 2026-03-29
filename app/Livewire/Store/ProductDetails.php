<?php

namespace App\Livewire\Store;

use Livewire\Component;
use App\Models\AttributeValue;
use App\Services\CartService;
use App\Services\FlashSaleService;

class ProductDetails extends Component
{
    protected CartService $cartService;

    public $product;
    public $relatedProducts;
    public $mainImageUrl;
    
    // Data
    public $productOptions = []; 
    
    // State
    public $quantity = 1;
    public $selectedAttributes = []; // [attribute_id => value_id]
    public $currentPrice;
    public $basePrice;
    public $originalPrice;
    public $currentStock;
    public $selectedVariation = null;
    public $flashSale = [];
    public $flashSaleTitle = null;
    public $flashSaleEndsAt = null;
    public $hasFlashSale = false;
    
    // UI Feedback
    public $showSuccess = false;
    public $selectionMissing = false; // To trigger UI shake/warning

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount($product, $relatedProducts, $flashSale = [])
    {
        $this->product = $product;
        $this->relatedProducts = $relatedProducts;

        $this->flashSale = is_array($flashSale) ? $flashSale : [];
        $this->basePrice = (float) $product->price;
        $this->originalPrice = (float) $product->price;
        $this->currentPrice = (float) $product->price;
        $this->currentStock = $product->stock;
        $this->flashSaleTitle = $this->flashSale['title'] ?? null;
        $this->flashSaleEndsAt = $this->flashSale['ends_at'] ?? null;
        $this->hasFlashSale = ! empty($this->flashSale);
        
        // Build options for BOTH Variation products and Simple products with attributes
        $this->buildProductOptions();

        if ($this->product->has_variations) {
            $this->currentStock = 0; // Wait for selection
            $this->basePrice = (float) $product->variations->min('price');
            $this->originalPrice = $this->basePrice;
            $this->currentPrice = $this->basePrice;
        }

        $this->applyFlashSalePricing();
    }

    public function buildProductOptions()
    {
        $this->productOptions = [];

        if ($this->product->has_variations) {
            // 1. VARIATION LOGIC
            $this->product->load('variations.values.attribute');
            $allValues = $this->product->variations->flatMap(fn($v) => $v->values);
        } else {
            // 2. SIMPLE PRODUCT ATTRIBUTE LOGIC
            // Fetch attributes attached via pivot table
            $this->product->load('attributes');
            
            // Get all value_ids from the pivot table
            $valueIds = $this->product->attributes->pluck('pivot.value_id')->unique();
            
            if ($valueIds->isEmpty()) return;

            // Fetch the actual Value models with their parent Attribute
            $allValues = AttributeValue::with('attribute')->whereIn('id', $valueIds)->get();
        }

        // Group by Attribute to build the UI structure
        $this->productOptions = $allValues->groupBy('attribute_id')->map(function ($values) {
            $first = $values->first();
            return [
                'id' => $first->attribute_id,
                'name' => $first->attribute->name,
                'values' => $values->unique('id')->values()
            ];
        })->values()->toArray();
    }

    public function getSelectedValueName($attributeId)
    {
        if (!isset($this->selectedAttributes[$attributeId])) return null;
        
        foreach ($this->productOptions as $option) {
            if ($option['id'] == $attributeId) {
                $found = $option['values']->firstWhere('id', $this->selectedAttributes[$attributeId]);
                return $found ? $found->value : null;
            }
        }
        return null;
    }

    public function selectAttribute($attributeId, $valueId)
    {
        $this->selectedAttributes[$attributeId] = $valueId;
        $this->selectionMissing = false; // Reset warning
        $this->checkSelection();
    }

    public function resetSelection()
    {
        $this->selectedAttributes = [];
        $this->selectedVariation = null;
        $this->selectionMissing = false;
        
        if ($this->product->has_variations) {
            $this->currentStock = 0;
            $this->basePrice = (float) $this->product->variations->min('price');
        } else {
            // Reset to base
            $this->currentStock = $this->product->stock;
            $this->basePrice = (float) $this->product->price;
        }

        $this->originalPrice = $this->basePrice;
        $this->currentPrice = $this->basePrice;
        $this->applyFlashSalePricing();
    }

    public function checkSelection()
    {
        // 1. Check Completeness
        if (count($this->selectedAttributes) < count($this->productOptions)) {
            $this->selectedVariation = null;
            if ($this->product->has_variations) {
                $this->currentStock = 0;
                $this->basePrice = (float) $this->product->variations->min('price');
                $this->originalPrice = $this->basePrice;
                $this->currentPrice = $this->basePrice;
                $this->applyFlashSalePricing();
            }
            return; 
        }

        // 2. Handle Variations
        if ($this->product->has_variations) {
            $variation = $this->product->variations->first(function ($var) {
                $varValueIds = $var->values->pluck('id')->toArray();
                return !array_diff($this->selectedAttributes, $varValueIds) && 
                       count($varValueIds) == count($this->selectedAttributes);
            });

            if ($variation) {
                $this->selectedVariation = $variation;
                $this->basePrice = (float) $variation->price;
                $this->originalPrice = $this->basePrice;
                $this->currentPrice = $this->basePrice;
                $this->currentStock = $variation->stock;
                $this->applyFlashSalePricing();
            } else {
                $this->selectedVariation = null;
                $this->currentStock = 0;
                $this->basePrice = (float) $this->product->variations->min('price');
                $this->originalPrice = $this->basePrice;
                $this->currentPrice = $this->basePrice;
                $this->applyFlashSalePricing();
            }
        }
        // 3. Simple Products: No extra logic needed, stock/price is already base
    }

    public function increment()
    {
        // Require selection if options exist
        if (count($this->productOptions) > 0 && count($this->selectedAttributes) < count($this->productOptions)) {
            $this->selectionMissing = true;
            return;
        }
        
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
        // 1. Validation: Check if all attributes are selected
        if (count($this->productOptions) > 0 && count($this->selectedAttributes) < count($this->productOptions)) {
            $this->selectionMissing = true; // Triggers UI warning
            return;
        }

        // 2. Validation: Stock
        if ($this->currentStock <= 0) return;

        // 3. Prepare Cart Data
        // Generate Key: ID + VariationID (if any) + Attribute Hash (for simple products with options)
        $cartKey = $this->cartService->generateCartKey(
            productId: $this->product->id,
            variationId: $this->selectedVariation?->id,
            selectedAttributes: $this->selectedAttributes
        );

        // Format Attributes for Display
        $optionsDisplay = [];
        
        // Use helper to get names from selected IDs
        foreach($this->selectedAttributes as $attrId => $valId) {
            $attrName = collect($this->productOptions)->firstWhere('id', $attrId)['name'] ?? 'Option';
            $valName = $this->getSelectedValueName($attrId);
            $optionsDisplay[$attrName] = $valName;
        }

        $this->cartService->addOrIncrementItem(
            key: $cartKey,
            item: [
                'product_id' => $this->product->id,
                'variation_id' => $this->selectedVariation?->id,
                'name' => $this->product->name,
                'price' => $this->currentPrice,
                'currency_symbol' => $this->product->currency_symbol,
                'thumbnail' => $this->product->thumbnail,
                'attributes' => $optionsDisplay,
            ],
            quantity: $this->quantity
        );
        $this->dispatch('cartUpdated'); 

        if ($buyNow) return redirect()->route('store.cart.index');

        $this->showSuccess = true;
        $this->dispatch('reset-success'); 
    }

    public function render()
    {
        return view('livewire.store.product-details');
    }

    protected function applyFlashSalePricing(): void
    {
        if (empty($this->flashSale) || empty($this->flashSale['product_ids']) || ! in_array($this->product->id, $this->flashSale['product_ids'], true)) {
            $this->hasFlashSale = false;
            $this->currentPrice = $this->basePrice;
            $this->originalPrice = $this->basePrice;
            return;
        }

        $flashSaleService = app(FlashSaleService::class);
        $salePrice = $flashSaleService->applySale(
            (float) $this->basePrice,
            (string) ($this->flashSale['sale_type'] ?? 'percentage'),
            (float) ($this->flashSale['sale_value'] ?? 0)
        );

        $salePrice = max(0, round($salePrice, 2));
        if ($salePrice < $this->basePrice) {
            $this->hasFlashSale = true;
            $this->currentPrice = $salePrice;
            $this->originalPrice = $this->basePrice;
            $this->flashSaleTitle = $this->flashSale['title'] ?? 'Flash Sale';
            $this->flashSaleEndsAt = $this->flashSale['ends_at'] ?? null;
            return;
        }

        $this->hasFlashSale = false;
        $this->currentPrice = $this->basePrice;
        $this->originalPrice = $this->basePrice;
    }
}
