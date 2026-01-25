<div class="mx-4">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <style>.ck-editor__editable { min-height: 200px; }</style>

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Add New Product</h2>
            <p class="text-sm text-gray-500">Follow the steps to create a new product listing.</p>
        </div>
        <div class="text-sm font-medium text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
            Step {{ $currentStep }} of {{ $totalSteps }}
        </div>
    </div>

    <div class="mb-8">
        <div class="flex items-center w-full">
            @foreach([1 => 'Basic Info', 2 => 'Organization', 3 => 'Media', 4 => 'Pricing & Variants'] as $step => $label)
                <div class="flex-1 relative">
                    <div class="flex items-center">
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full border-2 
                            {{ $currentStep >= $step ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 bg-white text-gray-400' }} 
                            font-bold text-sm z-10 transition-all duration-300">
                            @if($currentStep > $step)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                {{ $step }}
                            @endif
                        </div>
                        <div class="flex-1 h-1 {{ $currentStep > $step ? 'bg-blue-600' : 'bg-gray-200' }} mx-2"></div>
                    </div>
                    <span class="absolute top-10 left-1/2 transform -translate-x-1/2 text-xs font-medium {{ $currentStep >= $step ? 'text-blue-700' : 'text-gray-500' }}">
                        {{ $label }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-6">
        @include('admin.includes.message')
        @include('admin.includes.errors')
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        
        <form wire:submit.prevent="submit">
            
            @if($currentStep === 1)
                <div class="p-8 space-y-6 animate-fade-in">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                            <input wire:model.live="name" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5" placeholder="e.g. Wireless Noise Cancelling Headphones">
                            <p class="text-xs text-gray-500 mt-1">The main title displayed on your store.</p>
                            @error('name') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">URL Slug</label>
                            <div class="flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">/products/</span>
                                <input wire:model="slug" type="text" class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 bg-gray-50 cursor-not-allowed" readonly>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Auto-generated from name. Used for SEO.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                            <select wire:model="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                <option value="1">Active (Visible)</option>
                                <option value="0">Draft (Hidden)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Control visibility in the store.</p>
                        </div>
                    </div>

                    <div wire:ignore>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Product Description</label>
                        <div x-data="{
                            description: @entangle('description'),
                            init() {
                                ClassicEditor.create(this.$refs.editor)
                                    .then(editor => {
                                        editor.setData(this.description || '');
                                        editor.model.document.on('change:data', () => { this.description = editor.getData(); });
                                    })
                                    .catch(error => { console.error(error); });
                            }
                        }">
                            <div x-ref="editor"></div>
                        </div>
                    </div>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    
                    <div class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <input wire:model.live="featured" type="checkbox" id="featured" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div class="ml-3">
                            <label for="featured" class="text-sm font-bold text-gray-900">Mark as Featured Product</label>
                            <p class="text-xs text-blue-700">Featured products appear on the homepage carousel.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep === 2)
                <div class="p-8 space-y-6 animate-fade-in">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Organization & Categorization</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Categories <span class="text-red-500">*</span></label>
                            <div class="border border-gray-200 rounded-lg p-4 max-h-[300px] overflow-y-auto bg-gray-50">
                                @foreach ($categories as $category)
                                    <div class="mb-2">
                                        <label class="inline-flex items-center">
                                            <input wire:model="selectedCategories" value="{{ $category->id }}" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm font-bold text-gray-800">{{ $category->name }}</span>
                                        </label>
                                        @if($category->children->count() > 0)
                                            <div class="ml-6 mt-1 space-y-1 border-l-2 border-gray-200 pl-2">
                                                @foreach($category->children as $child)
                                                    <label class="flex items-center">
                                                        <input wire:model="selectedCategories" value="{{ $child->id }}" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                        <span class="ml-2 text-sm text-gray-600">{{ $child->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                @if($categories->isEmpty()) <p class="text-sm text-gray-500 text-center">No categories found.</p> @endif
                            </div>
                            @error('selectedCategories') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Related Products</label>
                            <p class="text-xs text-gray-500 mb-2">Select products to recommend (Cross-selling).</p>
                            <select wire:model="relatedProducts" multiple class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 min-h-[150px]">
                                @foreach ($availableProducts as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep === 3)
                <div class="p-8 space-y-8 animate-fade-in" x-data="imageCropper({ target: 'thumbnail', ratio: 1/1 })">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Product Images</h3>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                        <div class="flex flex-col md:flex-row gap-6 items-start">
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Main Thumbnail <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-3">Square (1:1), Recommended 1000x1000px.</p>
                                <input type="file" accept="image/*" @change="fileChosen" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                                @error('thumbnail') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-32 h-32 bg-white border border-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-400 text-xs text-center">No Image</span>
                                @endif
                            </div>
                        </div>
                        @include('admin.includes.cropper-modal')
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200" x-data="imageCropper({ target: 'tempImage', ratio: 5/4 })">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Gallery Images</label>
                        <p class="text-xs text-gray-500 mb-3">Additional images for product detail page.</p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-600 mb-1">Bulk Upload (No Crop)</label>
                                <input wire:model="images" type="file" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-600 mb-1">Crop & Upload Single</label>
                                <label class="cursor-pointer inline-flex items-center justify-center w-full px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Select Image to Crop
                                    <input type="file" accept="image/*" @change="fileChosen" class="hidden">
                                </label>
                            </div>
                        </div>

                        @if ($images)
                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-4">
                                @foreach ($images as $index => $image)
                                    <div class="relative group aspect-square bg-white rounded-lg border border-gray-200 overflow-hidden">
                                        <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <button type="button" wire:click="removeImage({{ $index }})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-md">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @error('images.*') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        @include('admin.includes.cropper-modal')
                    </div>
                </div>
            @endif

            @if($currentStep === 4)
                <div class="p-8 space-y-8 animate-fade-in">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Pricing & Inventory</h3>

                    <div class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100 mb-6">
                        <input wire:model.live="has_variations" type="checkbox" id="has_variations" class="h-5 w-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <div class="ml-3">
                            <label for="has_variations" class="text-sm font-bold text-gray-900">This product has options</label>
                            <p class="text-xs text-purple-700">Enable this if the product comes in different sizes, colors, etc.</p>
                        </div>
                    </div>

                    @if(!$has_variations)
                        <div class="grid grid-cols-2 gap-6 animate-fade-in">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Regular Price ($)</label>
                                <input wire:model="price" type="number" step="0.01" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 text-lg font-mono">
                                @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Stock Quantity</label>
                                <input wire:model="stock" type="number" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 text-lg font-mono">
                                @error('stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @else
                        <div class="space-y-6 animate-fade-in">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h4 class="text-sm font-bold text-gray-800 mb-3">1. Choose Attributes</h4>
                                <select wire:model.live="selectedAttributes" multiple class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 p-2">
                                    @foreach ($productAttributes as $attribute)
                                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedAttributes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            @if(count($selectedAttributes) > 0)
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <h4 class="text-sm font-bold text-gray-800 mb-3">2. Select Values</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($selectedAttributes as $attrId)
                                            @php $attr = $productAttributes->find($attrId); @endphp
                                            @if($attr)
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-600 mb-1">{{ $attr->name }}</label>
                                                    <select wire:model.live="attributeValues.{{ $attrId }}" multiple class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2 h-24">
                                                        @foreach ($attr->values as $value)
                                                            <option value="{{ $value->id }}">{{ $value->value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error("attributeValues.{$attrId}") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="mt-4 text-right">
                                        <button type="button" wire:click="generateVariations" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 shadow-md">
                                            Generate Variations Table
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($variations))
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-bold">
                                            <tr>
                                                <th class="p-3">Variation</th>
                                                <th class="p-3">SKU</th>
                                                <th class="p-3 w-32">Price</th>
                                                <th class="p-3 w-32">Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach ($variations as $index => $var)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="p-3">
                                                        @foreach ($var['attribute_values'] as $attrId => $valId)
                                                            <span class="inline-block bg-white border border-gray-200 rounded px-1.5 py-0.5 text-xs text-gray-600 mr-1">
                                                                {{ $productAttributes->find($attrId)->name }}: <b>{{ $productAttributes->find($attrId)->values->find($valId)->value }}</b>
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                    <td class="p-3">
                                                        <input wire:model="variations.{{ $index }}.sku" type="text" class="w-full border-gray-300 rounded text-xs py-1 px-2 focus:ring-blue-500 focus:border-blue-500">
                                                        @error("variations.{$index}.sku") <span class="text-red-500 text-[10px] block">{{ $message }}</span> @enderror
                                                    </td>
                                                    <td class="p-3">
                                                        <input wire:model="variations.{{ $index }}.price" type="number" step="0.01" class="w-full border-gray-300 rounded text-xs py-1 px-2 focus:ring-blue-500 focus:border-blue-500">
                                                        @error("variations.{$index}.price") <span class="text-red-500 text-[10px] block">{{ $message }}</span> @enderror
                                                    </td>
                                                    <td class="p-3">
                                                        <input wire:model="variations.{{ $index }}.stock" type="number" class="w-full border-gray-300 rounded text-xs py-1 px-2 focus:ring-blue-500 focus:border-blue-500">
                                                        @error("variations.{$index}.stock") <span class="text-red-500 text-[10px] block">{{ $message }}</span> @enderror
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-gray-50 px-8 py-5 border-t border-gray-200 flex justify-between items-center rounded-b-xl">
                <div>
                    @if($currentStep > 1)
                        <button type="button" wire:click="previousStep" class="px-6 py-2 border border-gray-300 shadow-sm text-sm font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Back
                        </button>
                    @endif
                </div>
                <div>
                    @if($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep" class="px-6 py-2 border border-transparent text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md transition">
                            Next Step
                        </button>
                    @else
                        <button type="submit" class="px-8 py-2 border border-transparent text-sm font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-md transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Create Product
                        </button>
                    @endif
                </div>
            </div>

        </form>
    </div>

    <script>
        // Keep your existing imageCropper JS logic here
        // ... (Paste the JS from your original file) ...
        function imageCropper(config) {
        return {
            isCropping: false,
            cropper: null,
            selectedFile: null,
            targetProperty: config.target, 
            aspectRatio: config.ratio || 1, 

            fileChosen(event) {
                this.selectedFile = event.target.files[0];
                if (this.selectedFile) {
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        if(this.$refs.cropImage) {
                            this.$refs.cropImage.src = e.target.result;
                            this.isCropping = true;
                            
                            if (this.cropper) {
                                this.cropper.destroy();
                            }

                            this.$nextTick(() => {
                                this.cropper = new Cropper(this.$refs.cropImage, {
                                    aspectRatio: this.aspectRatio,
                                    viewMode: 1,
                                    autoCropArea: 1,
                                });
                            });
                        }
                    };
                    reader.readAsDataURL(this.selectedFile);
                }
            },

            cropAndUpload() {
                if (this.cropper) {
                    this.cropper.getCroppedCanvas({
                        width: 1000, 
                    }).toBlob((blob) => {
                        @this.upload(this.targetProperty, blob, (uploadedFilename) => {
                            this.cancelCrop();
                        }, () => {
                            alert('Upload failed');
                        });
                    }, 'image/jpeg', 0.85);
                }
            },

            uploadOriginal() {
                if (this.selectedFile) {
                    @this.upload(this.targetProperty, this.selectedFile, (uploadedFilename) => {
                        this.cancelCrop();
                    }, () => {
                        alert('Upload failed');
                    });
                }
            },

            cancelCrop() {
                this.isCropping = false;
                if (this.cropper) {
                    this.cropper.destroy();
                    this.cropper = null;
                }
                if(this.$el.querySelector('input[type="file"]')) {
                    this.$el.querySelector('input[type="file"]').value = '';
                }
            }
        }
    }
    </script>
</div>
