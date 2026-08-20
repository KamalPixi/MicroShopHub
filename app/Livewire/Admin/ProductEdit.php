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

    // Video
    public $videoFile;                   // New uploaded video (Livewire temp)
    public $videoUrl = '';               // YouTube / external URL
    public $existingVideoPath;           // Currently saved video_path
    public $existingVideoUrl = '';       // Currently saved video_url
    public $videoSource = 'upload';      // 'upload' | 'url' (tab toggle)
    public $removeExistingVideo = false; // Flag to delete stored video on save

    // Measurements / Size Chart
    public $hasMeasurements = false;
    public $measurementUnit = 'cm';
    public $measurementColumns = [];
    public $measurementRows = [];

    // Step 4: Pricing & Inventory
    public $has_variations = false;
    public $imagesToDelete = [];

    protected $listeners = ['refresh' => '$refresh'];
    public $price;
    public $stock;

    // Variation Logic
    public $selectedAttributes = [];
    public $attributeValues = [];
    public $variations = [];
    public $new_attribute_name; 

    public function mount($id)
    {
        if (!$id) {
            abort(404, 'Product ID is missing');
        }

        // Load Product with correct relationships
        // Note: 'values' is the relation name in your ProductVariation model
        $this->product = Product::with(['categories', 'variations.values', 'relatedProducts'])
            ->findOrFail($id);

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

        // Video
        $this->existingVideoPath = $this->product->video_path;
        $this->existingVideoUrl  = $this->product->video_url ?? '';
        $this->videoSource       = $this->existingVideoPath ? 'upload' : ($this->existingVideoUrl ? 'url' : 'upload');

        // Measurements
        $measurements = $this->product->measurements;
        if (!empty($measurements) && !empty($measurements['columns'])) {
            $this->hasMeasurements      = true;
            $this->measurementUnit      = $measurements['unit'] ?? 'cm';
            $this->measurementColumns   = $measurements['columns'];
            $this->measurementRows      = $measurements['rows'] ?? [];
        } else {
            // Default empty scaffold
            $this->measurementColumns = ['Shoulder', 'Bust', 'Waist', 'Length'];
            $this->measurementRows    = [
                ['label' => 'XS', 'values' => ['', '', '', '']],
                ['label' => 'S',  'values' => ['', '', '', '']],
                ['label' => 'M',  'values' => ['', '', '', '']],
            ];
        }

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

    public function updatedSku($value)
    {
        $this->sku = trim((string)$value) !== '' ? trim((string)$value) : null;
        if ($this->sku) {
            $this->validateOnly('sku', [
                'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->product->id)],
            ]);
        }
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
        $this->imagesToDelete[] = $this->existingGallery[$index];
        array_splice($this->existingGallery, $index, 1);
    }

    public function removeThumbnail()
    {
        if ($this->existingThumbnail) {
            $this->imagesToDelete[] = $this->existingThumbnail;
        }
        $this->thumbnail = null;
        $this->existingThumbnail = null;
    }

    public function removeNewImage($index)
    {
        array_splice($this->newImages, $index, 1);
    }

    public function removeExistingVideo()
    {
        $this->removeExistingVideo  = true;
        $this->existingVideoPath    = null;
        $this->existingVideoUrl     = '';
        $this->videoFile            = null;
        $this->videoUrl             = '';
    }

    // --- Measurement / Size Chart Actions ---

    public function addMeasurementColumn()
    {
        $this->measurementColumns[] = '';
        foreach ($this->measurementRows as $i => $_) {
            $this->measurementRows[$i]['values'][] = '';
        }
    }

    public function removeMeasurementColumn($colIndex)
    {
        array_splice($this->measurementColumns, $colIndex, 1);
        foreach ($this->measurementRows as $i => $_) {
            array_splice($this->measurementRows[$i]['values'], $colIndex, 1);
        }
    }

    public function addMeasurementRow()
    {
        $this->measurementRows[] = [
            'label'  => '',
            'values' => array_fill(0, count($this->measurementColumns), ''),
        ];
    }

    public function removeMeasurementRow($rowIndex)
    {
        array_splice($this->measurementRows, $rowIndex, 1);
    }

    private function buildMeasurementsPayload(): ?array
    {
        if (!$this->hasMeasurements) return null;

        $rows = array_values(array_filter($this->measurementRows, fn($r) => trim($r['label'] ?? '') !== ''));
        if (empty($rows) || empty($this->measurementColumns)) return null;

        $colCount = count($this->measurementColumns);
        $rows = array_map(function ($row) use ($colCount) {
            $vals = array_slice(array_pad($row['values'] ?? [], $colCount, ''), 0, $colCount);
            return ['label' => $row['label'], 'values' => $vals];
        }, $rows);

        return [
            'unit'    => $this->measurementUnit,
            'columns' => array_values($this->measurementColumns),
            'rows'    => $rows,
        ];
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
            $this->sku = trim((string)$this->sku) !== '' ? trim((string)$this->sku) : null;
            $this->validate([
                'name' => 'required|min:3',
                'slug' => ['required', Rule::unique('products', 'slug')->ignore($this->product->id)],
                'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->product->id)],
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
                'thumbnail' => 'nullable|image|max:25600', 
                'newImages.*' => 'image|max:25600',
                'videoFile' => 'nullable|mimes:mp4,mov,avi,webm|max:102400',
                'videoUrl'  => 'nullable|url',
            ]);
        }
    }

    // --- Submit ---

    public function submit()
    {
        // 1. Sanitize SKU
        $this->sku = trim((string)$this->sku) !== '' ? trim((string)$this->sku) : null;

        // 2. Validate Step 1
        try {
            $this->validateStep(1);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->currentStep = 1;
            throw $e;
        }

        // 3. Validate Step 2
        try {
            $this->validateStep(2);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->currentStep = 2;
            throw $e;
        }

        // 4. Validate Step 3
        try {
            $this->validateStep(3);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->currentStep = 3;
            throw $e;
        }
        
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

        // Save New Images with SEO Friendly & Collision-Free Names
        foreach ($this->newImages as $index => $img) {
            $name = $this->generateMediaFilename('gallery', $img, count($finalImages) + 1);
            $finalImages[] = $img->storeAs('images/products', $name, config('filesystems.default'));
        }

        // Handle Thumbnail with SEO Friendly Name
        $thumbnailPath = $this->product->thumbnail;
        if ($this->thumbnail) {
             if ($thumbnailPath) Storage::disk()->delete($thumbnailPath);
             $name = $this->generateMediaFilename('thumbnail', $this->thumbnail);
             $thumbnailPath = $this->thumbnail->storeAs('images/products', $name, config('filesystems.default'));
        }

        // Handle Video
        $videoPath = $this->product->video_path;
        $videoUrl  = $this->product->video_url;

        if ($this->removeExistingVideo) {
            // Admin explicitly removed the existing video
            if ($videoPath) {
                Storage::disk(config('filesystems.default'))->delete($videoPath);
            }
            $videoPath = null;
            $videoUrl  = null;
        }

        if ($this->videoSource === 'upload' && $this->videoFile) {
            // Replace with newly uploaded file
            if ($videoPath) {
                Storage::disk(config('filesystems.default'))->delete($videoPath);
            }
            $name      = $this->generateMediaFilename('video', $this->videoFile);
            $videoPath = $this->videoFile->storeAs('videos/products', $name, config('filesystems.default'));
            $videoUrl  = null; // clear any old URL
        } elseif ($this->videoSource === 'url') {
            // Admin switched to URL tab
            $urlValue = trim($this->videoUrl);
            if ($urlValue) {
                // Delete any previously stored file when switching to URL
                if ($videoPath) {
                    Storage::disk(config('filesystems.default'))->delete($videoPath);
                    $videoPath = null;
                }
                $videoUrl = $urlValue;
            }
        }

        // 2. Update Product
        $this->product->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'sku'  => $this->sku,
            'description' => $this->description,
            'featured' => $this->featured,
            'status' => $this->status,
            'has_variations' => $this->has_variations,
            'price' => $this->has_variations ? null : $this->price,
            'stock' => $this->has_variations ? 0 : $this->stock,
            'thumbnail' => $thumbnailPath,
            'images' => $finalImages, // Saving the array directly (Eloquent handles JSON encoding)
            'video_path' => $videoPath,
            'video_url'  => $videoUrl,
            'measurements' => $this->buildMeasurementsPayload(),
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

        // 5. Cleanup Deleted Files
        foreach ($this->imagesToDelete as $path) {
            Storage::disk(config('filesystems.default'))->delete($path);
        }
        $this->imagesToDelete = [];

        session()->flash('success', 'Product updated successfully.');
        return redirect()->route('admin.products.index');
    }

    /**
     * Generate an SEO-friendly, collision-free media filename.
     * Format: {slug}-{type}-{sku?}-{timestamp}-{random}.{ext}
     * e.g., "blue-tshirt-thumbnail-sku-101-1771601234-a1b2c3.jpg"
     * e.g., "blue-tshirt-gallery-1-1771601234-d4e5f6.webp"
     */
    protected function generateMediaFilename(string $type, $file, ?int $index = null): string
    {
        $baseSlug = Str::slug($this->slug ?: $this->name ?: 'product');
        $skuPart  = !empty($this->sku) ? '-' . Str::slug($this->sku) : '';
        $typePart = $index !== null ? "{$type}-{$index}" : $type;
        $timePart = time();
        $randPart = Str::lower(Str::random(6));
        $ext      = Str::lower($file->getClientOriginalExtension() ?: 'jpg');

        return "{$baseSlug}-{$typePart}{$skuPart}-{$timePart}-{$randPart}.{$ext}";
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
