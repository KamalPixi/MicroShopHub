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
    public $sku = '';
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
    public $videoFile;       // Uploaded video file (Livewire temp)
    public $videoUrl = '';   // YouTube / external video URL
    public $videoSource = 'upload'; // 'upload' | 'url' (tab toggle)

    // Step 3: Measurements / Size Chart
    public $hasMeasurements = false;
    public $measurementUnit = 'cm';
    public $measurementColumns = ['Shoulder', 'Bust', 'Waist', 'Length'];
    public $measurementRows = [
        ['label' => 'XS', 'values' => ['', '', '', '']],
        ['label' => 'S',  'values' => ['', '', '', '']],
        ['label' => 'M',  'values' => ['', '', '', '']],
    ];

    // Step 4: Inventory & Variations
    public $price;
    public $stock;
    public $has_attributes = false; // Controls visibility of Attribute Section
    public $has_variations = false; // Controls visibility of Variation Table
    public $selectedAttributes = [];
    public $attributeValues = [];
    public $variations = [];
    
    // Data Sources
    public $categories = [];
    public $productAttributes = [];
    public $availableProducts = [];
    
    // Attribute Management
    public $newAttribute = ['name' => '', 'values' => ['']];

    public function mount()
    {
        $this->categories = Category::whereNull('parent_id')->with('children')->get();
        $this->productAttributes = Attribute::with('values')->get();
        $this->availableProducts = Product::pluck('name', 'id')->toArray();
    }

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

    public function validateStep()
    {
        if ($this->currentStep == 1) {
            $this->sku = trim((string)$this->sku) !== '' ? trim((string)$this->sku) : null;
            $this->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:products,slug',
                'sku' => 'nullable|string|max:100|unique:products,sku',
                'description' => 'nullable|string',
                'status' => 'required|integer|in:0,1',
            ]);
        }
        elseif ($this->currentStep == 2) {
            $this->validate([
                'selectedCategories' => 'required|array|min:1',
            ]);
        }
        elseif ($this->currentStep == 3) {
            $this->validate([
                'thumbnail' => 'nullable|image|max:25600',
                'images.*' => 'image|max:25600',
                'videoFile' => 'nullable|mimes:mp4,mov,avi,webm|max:102400',
                'videoUrl'  => 'nullable|url',
            ]);
        }
        elseif ($this->currentStep == 4) {
            // Logic Fix: Only require variations IF has_variations is checked
            if ($this->has_variations) {
                $this->validate([
                    'variations' => 'required|array|min:1',
                    'variations.*.sku' => 'required|string|distinct',
                    'variations.*.price' => 'required|numeric|min:0',
                    'variations.*.stock' => 'required|integer|min:0',
                ]);
            } else {
                // Simple Product (even with attributes) needs base price/stock
                $this->validate([
                    'price' => 'required|numeric|min:0',
                    'stock' => 'required|integer|min:0',
                ]);
            }
        }
    }

    // --- Logic Handlers ---

    public function updatedSku($value)
    {
        $this->sku = trim((string)$value) !== '' ? trim((string)$value) : null;
        if ($this->sku) {
            $this->validateOnly('sku', [
                'sku' => 'nullable|string|max:100|unique:products,sku',
            ]);
        }
    }

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

    public function removeThumbnail()
    {
        $this->thumbnail = null;
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images); 
    }

    // --- Measurement / Size Chart Actions ---

    public function addMeasurementColumn()
    {
        $this->measurementColumns[] = '';
        // Append an empty value slot to every existing row
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

    // Toggle Handlers
    public function updatedHasAttributes()
    {
        if (!$this->has_attributes) {
            $this->selectedAttributes = [];
            $this->attributeValues = [];
            $this->has_variations = false; // Disable variations if attributes are off
            $this->variations = [];
        }
    }

    public function updatedHasVariations()
    {
        if (!$this->has_variations) {
            $this->variations = [];
            // We do NOT clear price/stock here, so user can revert to simple product easily
        } else {
            $this->price = null;
            $this->stock = null;
        }
    }

    public function addAttributeValueField() { $this->newAttribute['values'][] = ''; }
    
    public function removeAttributeValueField($index) 
    { 
        unset($this->newAttribute['values'][$index]); 
        $this->newAttribute['values'] = array_values($this->newAttribute['values']); 
    }
    
    public function saveNewAttribute()
    {
        $this->validate([ 
            'newAttribute.name' => 'required|string|max:255', 
            'newAttribute.values.*' => 'required|string|max:255' 
        ]);
        
        $attribute = Attribute::create(['name' => $this->newAttribute['name']]);
        foreach ($this->newAttribute['values'] as $value) {
            AttributeValue::create(['attribute_id' => $attribute->id, 'value' => $value]);
        }
        
        // Refresh & Select
        $this->productAttributes = Attribute::with('values')->get();
        if (!in_array($attribute->id, $this->selectedAttributes)) {
            $this->selectedAttributes[] = $attribute->id;
        }
        // Pre-select all new values
        $this->attributeValues[$attribute->id] = $attribute->values->pluck('id')->map(fn($id)=>(string)$id)->toArray();
        
        $this->newAttribute = ['name' => '', 'values' => ['']];
    }

    public function generateVariations()
    {
        if (!$this->has_variations) return;
        $this->validate(['selectedAttributes' => 'required|array|min:1']);
        
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
                // Safely find value name for SKU
                $attr = $this->productAttributes->find($attrId);
                $val = $attr ? $attr->values->find($valId) : null;
                if ($val) $valueNames[] = Str::slug($val->value);
            }
            
            $this->variations[] = [
                'attribute_values' => $combo,
                'sku' => Str::slug($this->name) . '-' . implode('-', $valueNames),
                'price' => $this->price, // Optional pre-fill
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

    public function submit()
    {
        // 1. Sanitize SKU
        $this->sku = trim((string)$this->sku) !== '' ? trim((string)$this->sku) : null;

        // 2. Validate Step 1 (Basic Info & unique SKU)
        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:products,slug',
                'sku' => 'nullable|string|max:100|unique:products,sku',
                'status' => 'required|integer|in:0,1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->currentStep = 1;
            throw $e;
        }

        // 3. Validate Step 2 (Categories)
        try {
            $this->validate([
                'selectedCategories' => 'required|array|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->currentStep = 2;
            throw $e;
        }

        // 4. Validate Step 3 (Media & Video)
        try {
            $this->validate([
                'thumbnail' => 'nullable|image|max:25600',
                'images.*' => 'image|max:25600',
                'videoFile' => 'nullable|mimes:mp4,mov,avi,webm|max:102400',
                'videoUrl'  => 'nullable|url',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->currentStep = 3;
            throw $e;
        }

        // 5. Validate Step 4 (Pricing / Variations)
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
        
        // SEO Friendly & Collision-Free Filenames
        $disk = config('filesystems.default');
        $thumbnailPath = null;
        if ($this->thumbnail) {
            $name = $this->generateMediaFilename('thumbnail', $this->thumbnail);
            $thumbnailPath = $this->thumbnail->storeAs('images/products', $name, $disk);
        }

        $galleryPaths = [];
        foreach ($this->images as $index => $image) {
            $name = $this->generateMediaFilename('gallery', $image, $index + 1);
            $galleryPaths[] = $image->storeAs('images/products', $name, $disk);
        }

        // Video: prefer uploaded file; fall back to URL
        $videoPath = null;
        $videoUrl   = null;
        if ($this->videoSource === 'upload' && $this->videoFile) {
            $name      = $this->generateMediaFilename('video', $this->videoFile);
            $videoPath = $this->videoFile->storeAs('videos/products', $name, $disk);
        } elseif ($this->videoSource === 'url' && $this->videoUrl) {
            $videoUrl = $this->videoUrl;
        }

        $product = Product::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'price' => $this->has_variations ? null : $this->price,
            'stock' => $this->has_variations ? null : $this->stock,
            'has_variations' => $this->has_variations,
            'featured' => $this->featured,
            'status' => $this->status,
            'thumbnail' => $thumbnailPath,
            'images' => $galleryPaths,
            'video_path' => $videoPath,
            'video_url'  => $videoUrl,
            'measurements' => $this->buildMeasurementsPayload(),
        ]);

        $product->categories()->sync($this->selectedCategories);
        $product->relatedProducts()->sync($this->relatedProducts);
        
        // Save Attributes (Sync regardless of variations)
        // Format: [attr_id => ['value_id' => null]] or handling multiple values logic if needed.
        // Usually product_attributes pivot needs adjustment if a product has multiple values for one attribute but is NOT a variation.
        // For simplicity here, we attach the selected values.
        
        // Note: Standard pivot usually holds one value per attribute for Simple Products.
        // If your DB design allows multiple, iterate. Assuming standard pivot here:
        $syncData = [];
        foreach ($this->attributeValues as $attrId => $valIds) {
            foreach ($valIds as $valId) {
                // We append to sync data. 
                // Note: belongsToMany->sync() expects unique keys usually. 
                // If your pivot table has an ID, use attach() instead loop.
                // For safety in this specific snippet, we'll assume the primary value or handle strictly.
                // If Pivot is (product_id, attribute_id, value_id), we can't use standard sync easily with dupes.
                // We will use attach to be safe.
                $product->attributes()->attach($attrId, ['value_id' => $valId]);
            }
        }

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
        return redirect()->route('admin.products.index');
    }

    private function buildMeasurementsPayload(): ?array
    {
        if (!$this->hasMeasurements) return null;

        // Filter out entirely-empty rows
        $rows = array_values(array_filter($this->measurementRows, fn($r) => trim($r['label'] ?? '') !== ''));
        if (empty($rows) || empty($this->measurementColumns)) return null;

        // Ensure every row has the right number of value slots
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
        return view('livewire.admin.product-add');
    }
}
