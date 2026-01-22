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

    public $name = '';
    public $slug = '';
    public $description = '';
    public $price;
    public $stock;
    public $featured = false;
    public $has_attributes = false;
    public $has_variations = false;
    public $selectedCategories = [];
    public $selectedAttributes = [];
    public $attributeValues = [];
    public $variations = [];
    public $relatedProducts = [];
    public $categories = [];
    public $productAttributes = [];
    public $availableProducts = [];
    public $newAttribute = ['name' => '', 'values' => ['']];
    public $tempAttributes = [];
    public $status = 1;
    
    // Images
    public $thumbnail;
    public $images = []; // Stores temporary uploaded files
    public $tempImage;

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug',
            'description' => 'nullable|string',
            'selectedCategories' => 'array|min:1',
            'relatedProducts' => 'nullable|array',
            'has_variations' => 'boolean',
            'featured' => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png|max:2048', 
            'images.*' => 'nullable|image|mimes:jpeg,png|max:2048',
            'status' => 'required|integer|in:0,1',
        ];

        if ($this->has_variations) {
            $rules['variations.*.sku'] = 'required|string|unique:product_variations,sku';
            $rules['variations.*.price'] = 'required|numeric|min:0';
            $rules['variations.*.stock'] = 'required|integer|min:0';
        } else {
            $rules['price'] = 'required|numeric|min:0';
            $rules['stock'] = 'required|integer|min:0';
        }

        return $rules;
    }

    public function mount()
    {
        $this->categories = Category::whereNull('parent_id')->with('children')->get();
        $this->productAttributes = Attribute::with('values')->get();
        $this->availableProducts = Product::pluck('name', 'id')->toArray();
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

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function updatedHasVariations()
    {
        if (!$this->has_variations) {
            $this->variations = [];
            $this->price = null;
            $this->stock = null;
        }
    }

    public function updatedSelectedAttributes()
    {
        $this->attributeValues = array_intersect_key($this->attributeValues, array_flip($this->selectedAttributes));
        $this->variations = [];
    }

    public function updatedAttributeValues()
    {
        $this->variations = [];
    }

    public function addAttributeValueField()
    {
        $this->newAttribute['values'][] = '';
    }

    public function removeAttributeValueField($index)
    {
        unset($this->newAttribute['values'][$index]);
        $this->newAttribute['values'] = array_values($this->newAttribute['values']);
    }

    public function saveNewAttribute()
    {
        $this->validate([
            'newAttribute.name' => 'required|string|max:255',
            'newAttribute.values.*' => 'required|string|max:255',
        ]);

        $attribute = Attribute::create(['name' => $this->newAttribute['name']]);
        foreach ($this->newAttribute['values'] as $value) {
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'value' => $value,
            ]);
        }

        $this->tempAttributes[] = [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'values' => $attribute->values->pluck('value', 'id')->toArray(),
        ];

        $this->productAttributes = Attribute::with('values')->get();
        $this->selectedAttributes[] = $attribute->id;
        $this->attributeValues[$attribute->id] = $attribute->values->pluck('id')->toArray();
        $this->newAttribute = ['name' => '', 'values' => ['']];
    }

    public function generateVariations()
    {
        if (!$this->has_variations) {
            $this->addError('has_variations', 'Please check "Has Variations" to generate variations.');
            return;
        }

        $this->validate([
            'selectedAttributes' => 'required|array|min:1',
        ]);

        $valueSets = [];
        foreach ($this->selectedAttributes as $attrId) {
            if (empty($this->attributeValues[$attrId] ?? [])) {
                $this->addError("attributeValues.{$attrId}", 'Select at least one value.');
                return;
            }
            $valueSets[$attrId] = $this->attributeValues[$attrId];
        }

        $combinations = $this->cartesian($valueSets);

        $this->variations = [];
        foreach ($combinations as $combo) {
            $valueNames = [];
            foreach ($combo as $attrId => $valId) {
                $value = $this->productAttributes->find($attrId)->values->find($valId)->value;
                $valueNames[] = Str::slug($value);
            }
            $sku = Str::slug($this->name) . '-' . implode('-', $valueNames);

            $this->variations[] = [
                'attribute_values' => $combo,
                'sku' => $sku,
                'price' => '',
                'stock' => 0,
            ];
        }
    }

    private function cartesian($input)
    {
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

    public function submit()
    {
        $this->validate();

        // 1. Handle Thumbnail
        $thumbnailPath = null;
        if ($this->thumbnail) {
            $thumbnailPath = $this->thumbnail->store('images/products', 'public');
        }

        // 2. Handle Additional Images (Array)
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
            'images' => $galleryPaths, // Save array directly to JSON column
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
        $this->reset();
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

    public function render()
    {
        $selectedAttributesDisplay = $this->getSelectedAttributesDisplay();

        return view('livewire.admin.product-add', [
            'selectedAttributesDisplay' => $selectedAttributesDisplay,
        ]);
    }
}
