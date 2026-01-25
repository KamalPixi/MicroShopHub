<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeValue;

class ProductEdit extends Component
{
    use WithFileUploads;

    // Query String Support
    public $product_id;
    protected $queryString = ['product_id'];

    public $product; 
    public $currentStep = 1;
    public $totalSteps = 4;

    // Step 1: Basic Info
    public $name;
    public $slug;
    public $sku;
    public $status = 1;
    public $description;
    public $featured = false;

    // Step 2: Organization
    public $selectedCategories = [];
    public $relatedProducts = [];

    // Step 3: Media
    public $thumbnail; // New upload
    public $existingThumbnail; // Path string
    
    public $newImages = []; // Array of newly uploaded temporary files
    public $existingGallery = []; // Array of paths strings (from JSON column)

    // Step 4: Pricing & Inventory
    public $has_variations = false;
    public $price;
    public $stock;

    // Variation Logic
    public $selectedAttributes = [];
    public $attributeValues = [];
    public $variations = [];
    public $new_attribute_name; 

    public function mount()
    {
        if (!$this->product_id) {
            abort(404, 'Product ID is missing');
        }

        // Load Product with correct relationships
        // Note: 'values' is the relation name in your ProductVariation model
        $this->product = Product::with(['categories', 'variations.values', 'relatedProducts'])
            ->findOrFail($this->product_id);

        // 1. Fill Basic Info
        $this->name = $this->product->name;
        $this->slug = $this->product->slug;
        $this->sku = $this->product->sku; // Only if you have 'sku' column on products table (checked your fillable, strict check not needed)
        $this->status = $this->product->status;
        $this->description = $this->product->description;
        $this->featured = (bool) $this->product->featured;

        // 2. Fill Organization
        $this->selectedCategories = $this->product->categories->pluck('id')->toArray();
        $this->relatedProducts = $this->product->relatedProducts->pluck('id')->map(fn($id) => (string) $id)->toArray();

        // 3. Fill Media (JSON Column Logic)
        $this->existingThumbnail = $this->product->thumbnail;
        // The 'images' attribute is already cast to array by your Model
        $this->existingGallery = $this->product->images ?? [];

        // 4. Fill Pricing/Variations
        if ($this->product->has_variations) {
            $this->has_variations = true;
            $this->loadExistingVariations();
        } else {
            $this->has_variations = false;
            $this->price = $this->product->price;
            $this->stock = $this->product->stock;
        }
    }

    public function loadExistingVariations()
    {
        $usedAttributeIds = [];
        
        foreach ($this->product->variations as $variation) {
            $attrValues = [];
            
            // Your model uses 'values()' relation
            foreach ($variation->values as $val) {
                $attrValues[$val->attribute_id] = $val->id;
                $usedAttributeIds[] = $val->attribute_id;
                
                // Pre-fill UI selections
                if (!isset($this->attributeValues[$val->attribute_id])) {
                    $this->attributeValues[$val->attribute_id] = [];
                }
                if (!in_array($val->id, $this->attributeValues[$val->attribute_id])) {
                    $this->attributeValues[$val->attribute_id][] = (string)$val->id;
                }
            }

            $this->variations[] = [
                'id' => $variation->id,
                'attribute_values' => $attrValues,
                'sku' => $variation->sku,
                'price' => $variation->price,
                'stock' => $variation->stock,
            ];
        }

        $this->selectedAttributes = array_unique($usedAttributeIds);
    }

    public function updatedName($value)
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    // --- Variation Generation Logic ---
    public function generateVariations()
    {
        $this->validate([
            'selectedAttributes' => 'required|array|min:1',
            'attributeValues' => 'required|array',
        ]);

        $this->variations = [];
        $arrays = [];

        foreach ($this->selectedAttributes as $attrId) {
            if (!empty($this->attributeValues[$attrId])) {
                $arrays[$attrId] = $this->attributeValues[$attrId];
            }
        }

        if (empty($arrays)) return;

        $combinations = [[]];
        foreach ($arrays as $property => $values) {
            $temp = [];
            foreach ($combinations as $combination) {
                foreach ($values as $value) {
                    $temp[] = $combination + [$property => $value];
                }
            }
            $combinations = $temp;
        }

        foreach ($combinations as $combination) {
            $this->variations[] = [
                'id' => null, 
                'attribute_values' => $combination,
                'sku' => $this->sku . '-' . implode('-', $combination),
                'price' => $this->price ?? 0,
                'stock' => 0,
            ];
        }
    }

    // --- Media Actions ---

    public function deleteExistingImage($index)
    {
        // For JSON array, we just remove the item from the array by index
        if (isset($this->existingGallery[$index])) {
            // Optional: Immediately delete file from storage if you want strict cleanup
            // Storage::disk('public')->delete($this->existingGallery[$index]);
            
            unset($this->existingGallery[$index]);
            // Re-index array to avoid gaps which might cause issues with JSON encoding
            $this->existingGallery = array_values($this->existingGallery);
        }
    }

    public function removeNewImage($index)
    {
        array_splice($this->newImages, $index, 1);
    }

    // --- Navigation & Validation ---

    public function nextStep()
    {
        $this->validateStep($this->currentStep);
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

    public function validateStep($step)
    {
        if ($step == 1) {
            $this->validate([
                'name' => 'required|min:3',
                'slug' => ['required', Rule::unique('products', 'slug')->ignore($this->product->id)],
                // Note: 'sku' is on variations usually, but if your product table has it (based on fillable), validate it here
                'description' => 'nullable',
            ]);
        }
        elseif ($step == 2) {
            $this->validate([
                'selectedCategories' => 'required|array|min:1',
            ]);
        }
        elseif ($step == 3) {
            $this->validate([
                'thumbnail' => 'nullable|image|max:2048', 
                'newImages.*' => 'image|max:2048',
            ]);
        }
    }

    // --- Submit ---

    public function submit()
    {
        $this->validateStep(1);
        $this->validateStep(2);
        
        if (!$this->has_variations) {
            $this->validate([
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
            ]);
        } else {
            $this->validate([
                'variations' => 'required|array|min:1',
                'variations.*.price' => 'required|numeric|min:0',
                'variations.*.stock' => 'required|integer|min:0',
                'variations.*.sku' => 'required',
            ]);
        }

        // 1. Prepare Images (Merge Existing + New)
        $finalImages = $this->existingGallery; // Start with what wasn't deleted

        // Save New Images
        foreach ($this->newImages as $img) {
            $finalImages[] = $img->store('products/gallery', 'public');
        }

        // Handle Thumbnail
        $thumbnailPath = $this->product->thumbnail;
        if ($this->thumbnail) {
             if ($thumbnailPath) Storage::disk('public')->delete($thumbnailPath);
             $thumbnailPath = $this->thumbnail->store('products/thumbnails', 'public');
        }

        // 2. Update Product
        $this->product->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'featured' => $this->featured,
            'status' => $this->status,
            'has_variations' => $this->has_variations,
            'price' => $this->has_variations ? null : $this->price,
            'stock' => $this->has_variations ? 0 : $this->stock,
            'thumbnail' => $thumbnailPath,
            'images' => $finalImages, // Saving the array directly (Eloquent handles JSON encoding)
        ]);

        // 3. Relationships
        $this->product->categories()->sync($this->selectedCategories);
        $this->product->relatedProducts()->sync($this->relatedProducts);

        // 4. Variations Logic
        if ($this->has_variations) {
            // Get IDs of variations submitted
            $submittedIds = collect($this->variations)->pluck('id')->filter()->toArray();
            
            // Delete removed variations
            $this->product->variations()->whereNotIn('id', $submittedIds)->delete();

            foreach ($this->variations as $varData) {
                // Update or Create Variation
                $variation = $this->product->variations()->updateOrCreate(
                    ['id' => $varData['id']], 
                    [
                        'sku' => $varData['sku'],
                        'price' => $varData['price'],
                        'stock' => $varData['stock'],
                    ]
                );

                // Sync Values
                // Using 'values()' relation from ProductVariation model
                $variation->values()->sync(array_values($varData['attribute_values']));
            }
        } else {
            $this->product->variations()->delete();
        }

        session()->flash('success', 'Product updated successfully.');
        return redirect()->route('admin.products.index');
    }

    public function render()
    {
        return view('livewire.admin.product-edit', [
            'categories' => Category::where('parent_id', null)->with('children')->get(),
            'availableProducts' => Product::where('id', '!=', $this->product->id)->pluck('name', 'id'), 
            'productAttributes' => Attribute::with('values')->get(),
        ]);
    }
}
