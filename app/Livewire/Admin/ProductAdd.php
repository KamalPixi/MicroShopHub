<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariation;

class ProductAdd extends Component
{
    use WithFileUploads;

    // Wizard State
    public $currentStep = 1;
    public $totalSteps = 4;

    // Step 1: Basic Info
    public $name = '';
    public $slug = '';
    public $description = '';
    public $status = 1;
    public $featured = false;

    // Step 2: Organization
    public $selectedCategories = [];
    public $relatedProducts = [];
    
    // Step 3: Media
    public $thumbnail;
    public $images = [];
    public $tempImage;

    // Step 4: Inventory & Variations
    public $price;
    public $stock;
    public $has_attributes = false;
    public $has_variations = false;
    public $selectedAttributes = [];
    public $attributeValues = [];
    public $variations = [];
    
    // Data Sources (Loaded in Mount)
    public $categories = [];
    public $productAttributes = [];
    public $availableProducts = [];
    
    // Attribute Management (UI helpers)
    public $newAttribute = ['name' => '', 'values' => ['']];
    public $tempAttributes = [];

    public function mount()
    {
        $this->categories = Category::whereNull('parent_id')->with('children')->get();
        $this->productAttributes = Attribute::with('values')->get();
        $this->availableProducts = Product::pluck('name', 'id')->toArray();
    }

    // --- Wizard Navigation Logic ---

    public function nextStep()
    {
        $this->validateStep();
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function setStep($step)
    {
        // Only allow jumping to previous steps or the immediate next one if validated
        if ($step < $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    public function validateStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:products,slug',
                'description' => 'nullable|string',
                'status' => 'required|integer|in:0,1',
            ]);
        }
        elseif ($this->currentStep == 2) {
            $this->validate([
                'selectedCategories' => 'required|array|min:1',
                'selectedCategories.*' => 'exists:categories,id',
            ]);
        }
        elseif ($this->currentStep == 3) {
            $this->validate([
                'thumbnail' => 'nullable|image|mimes:jpeg,png|max:2048',
                'images.*' => 'nullable|image|mimes:jpeg,png|max:2048',
            ]);
        }
        elseif ($this->currentStep == 4) {
            if ($this->has_variations) {
                $this->validate([
                    'variations' => 'required|array|min:1',
                    'variations.*.sku' => 'required|string|distinct',
                    'variations.*.price' => 'required|numeric|min:0',
                    'variations.*.stock' => 'required|integer|min:0',
                ]);
            } else {
                $this->validate([
                    'price' => 'required|numeric|min:0',
                    'stock' => 'required|integer|min:0',
                ]);
            }
        }
    }

    // --- Standard Logic (Same as before) ---

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function updatedTempImage()
    {
        if ($this->tempImage) {
            $this->images[] = $this->tempImage;
            $this->tempImage = null; 
        }
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images); 
    }

    // ... [Attribute & Variation logic kept identical to your file] ...
    public function updatedHasVariations() { if (!$this->has_variations) { $this->variations = []; $this->price = null; $this->stock = null; } }
    public function updatedSelectedAttributes() { $this->attributeValues = array_intersect_key($this->attributeValues, array_flip($this->selectedAttributes)); $this->variations = []; }
    public function updatedAttributeValues() { $this->variations = []; }
    public function addAttributeValueField() { $this->newAttribute['values'][] = ''; }
    public function removeAttributeValueField($index) { unset($this->newAttribute['values'][$index]); $this->newAttribute['values'] = array_values($this->newAttribute['values']); }
    
    public function saveNewAttribute()
    {
        $this->validate([ 'newAttribute.name' => 'required|string|max:255', 'newAttribute.values.*' => 'required|string|max:255' ]);
        $attribute = Attribute::create(['name' => $this->newAttribute['name']]);
        foreach ($this->newAttribute['values'] as $value) {
            AttributeValue::create(['attribute_id' => $attribute->id, 'value' => $value]);
        }
        $this->productAttributes = Attribute::with('values')->get();
        $this->selectedAttributes[] = $attribute->id;
        $this->attributeValues[$attribute->id] = $attribute->values->pluck('id')->toArray();
        $this->newAttribute = ['name' => '', 'values' => ['']];
    }

    public function generateVariations()
    {
        if (!$this->has_variations) return;
        $this->validate(['selectedAttributes' => 'required|array|min:1']);
        
        $valueSets = [];
        foreach ($this->selectedAttributes as $attrId) {
            if (empty($this->attributeValues[$attrId] ?? [])) {
                $this->addError("attributeValues.{$attrId}", 'Select values.');
                return;
            }
            $valueSets[$attrId] = $this->attributeValues[$attrId];
        }

        $combinations = $this->cartesian($valueSets);
        $this->variations = [];
        foreach ($combinations as $combo) {
            $valueNames = [];
            foreach ($combo as $attrId => $valId) {
                $valueNames[] = Str::slug($this->productAttributes->find($attrId)->values->find($valId)->value);
            }
            $this->variations[] = [
                'attribute_values' => $combo,
                'sku' => Str::slug($this->name) . '-' . implode('-', $valueNames),
                'price' => $this->price, // Pre-fill with main price if available
                'stock' => 0,
            ];
        }
    }

    private function cartesian($input) {
        $result = [[]];
        foreach ($input as $key => $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }
            $result = $append;
        }
        return $result;
    }

    public function getSelectedAttributesDisplay()
    {
        $display = [];
        foreach ($this->selectedAttributes as $attrId) {
            $attribute = $this->productAttributes->find($attrId);
            if ($attribute) {
                $values = collect($this->attributeValues[$attrId] ?? [])->map(function ($valId) use ($attribute) {
                    return $attribute->values->find($valId)->value ?? '';
                })->filter()->implode(', ');
                $display[] = ['name' => $attribute->name, 'values' => $values];
            }
        }
        return $display;
    }

    public function submit()
    {
        // Final Global Validation
        $this->validateStep(); // Validate the final step
        
        // 1. Handle Thumbnail
        $thumbnailPath = $this->thumbnail ? $this->thumbnail->store('images/products', 'public') : null;

        // 2. Handle Gallery
        $galleryPaths = [];
        foreach ($this->images as $image) {
            $galleryPaths[] = $image->store('images/products', 'public');
        }

        // 3. Create Product
        $product = Product::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->has_variations ? null : $this->price,
            'stock' => $this->has_variations ? null : $this->stock,
            'has_variations' => $this->has_variations,
            'featured' => $this->featured,
            'status' => $this->status,
            'thumbnail' => $thumbnailPath,
            'images' => $galleryPaths,
        ]);

        // 4. Relations
        $product->categories()->sync($this->selectedCategories);
        $product->relatedProducts()->sync($this->relatedProducts);
        $product->attributes()->sync(
            collect($this->attributeValues)->mapWithKeys(function ($values, $attrId) {
                return [$attrId => ['value_id' => $values[0]]];
            })->toArray()
        );

        if ($this->has_variations) {
            foreach ($this->variations as $var) {
                $variation = ProductVariation::create([
                    'product_id' => $product->id,
                    'sku' => $var['sku'],
                    'price' => $var['price'],
                    'stock' => $var['stock'],
                ]);
                $variation->values()->sync(array_values($var['attribute_values']));
            }
        }

        session()->flash('message', 'Product created successfully!');
        
        // Use resetExcept to avoid "isEmpty on array" error
        $this->resetExcept(['categories', 'productAttributes', 'availableProducts']);
        $this->currentStep = 1;
    }

    public function render()
    {
        return view('livewire.admin.product-add', [
            'selectedAttributesDisplay' => $this->getSelectedAttributesDisplay(),
        ]);
    }
}
