<div class="mx-4">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <style>.ck-editor__editable { min-height: 200px; }</style>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Add New Product</h2>
            <p class="text-sm text-gray-500 mt-1">Create a new product listing in your catalog.</p>
        </div>
        <div class="text-xs font-semibold text-gray-600 bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
            Step {{ $currentStep }} of {{ $totalSteps }}
        </div>
    </div>

    <div class="mb-6">
        <div class="flex items-center w-full">
            @foreach([1 => 'Basic Info', 2 => 'Organization', 3 => 'Media', 4 => 'Pricing & Variants'] as $step => $label)
                <div class="flex-1 relative">
                    <div class="flex items-center">
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full border-2 
                            {{ $currentStep >= $step ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 bg-white text-gray-400' }} 
                            font-bold text-xs z-10 transition-all duration-300">
                            @if($currentStep > $step)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                {{ $step }}
                            @endif
                        </div>
                        <div class="flex-1 h-1 {{ $currentStep > $step ? 'bg-blue-600' : 'bg-gray-200' }} mx-2"></div>
                    </div>
                    <span class="absolute top-8 left-1/2 transform -translate-x-1/2 text-xs font-medium {{ $currentStep >= $step ? 'text-blue-700' : 'text-gray-500' }}">
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

    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        
        <form wire:submit.prevent="submit">
            
            @if($currentStep === 1)
                <div class="p-6 space-y-5 animate-fade-in">
                    
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center border-b pb-3 mb-5">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Product Name <span class="text-red-500">*</span></label>
                            <input wire:model.live="name" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="e.g. Wireless Noise Cancelling Headphones">
                            @error('name') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Slug</label>
                            <div class="flex mt-1 shadow-sm rounded-lg">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">/product/</span>
                                <input wire:model="slug" type="text" class="block w-full border border-gray-300 rounded-none rounded-r-lg focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                            </div>
                            @error('slug') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">SKU</label>
                            <input wire:model="sku" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="SKU-12345">
                            @error('sku') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="status" class="mt-1 block w-full bg-white border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                                <option value="1">Active</option>
                                <option value="0">Draft</option>
                            </select>
                        </div>
                        
                         <div class="flex items-center pt-6">
                            <input wire:model.live="featured" type="checkbox" id="featured" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="featured" class="ml-2 block text-sm font-medium text-gray-700">Mark as Featured Product</label>
                        </div>
                    </div>

                    <div wire:ignore>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Description</label>
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
                            <div x-ref="editor" class="rounded-lg border border-gray-300 overflow-hidden"></div>
                        </div>
                    </div>
                    @error('description') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                </div>
            @endif

            @if($currentStep === 2)
                <div class="p-6 space-y-5 animate-fade-in">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center border-b pb-3 mb-5">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Organization & Categorization
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categories <span class="text-red-500">*</span></label>
                            <div class="border border-gray-300 rounded-lg p-4 max-h-[300px] overflow-y-auto bg-gray-50 shadow-sm">
                                @foreach ($categories as $category)
                                    <div class="mb-2">
                                        <label class="inline-flex items-center">
                                            <input wire:model="selectedCategories" value="{{ $category->id }}" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm font-medium text-gray-800">{{ $category->name }}</span>
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
                            @error('selectedCategories') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Related Products</label>
                            <p class="text-xs text-gray-500 mb-2">Select products to recommend (Cross-selling).</p>
                            <select wire:model="relatedProducts" multiple class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 min-h-[200px]">
                                @foreach ($availableProducts as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep === 3)
                <div class="p-6 space-y-6 animate-fade-in" x-data="imageCropper({ target: 'thumbnail', ratio: 1/1, width: 500 })">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center border-b pb-3 mb-5">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Product Images
                    </h3>

                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <div class="flex flex-col md:flex-row gap-6 items-start">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Main Thumbnail <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-3">Required: 1:1 Aspect Ratio (Recommended: 500x500px)</p>
                                
                                <input type="file" accept="image/*" @change="fileChosen" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                                @error('thumbnail') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-32 h-32 bg-white border border-gray-300 rounded-lg flex items-center justify-center overflow-hidden shadow-sm">
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-400 text-xs text-center font-medium">No Image</span>
                                @endif
                            </div>
                        </div>
                        
                        <div x-show="isCropping" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Image</h3>
                                        <div class="relative w-full h-[400px] bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                            <img x-ref="cropImage" class="max-w-full max-h-full block" style="max-width: 100%;">
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                        <button type="button" @click="cropAndUpload" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:w-auto sm:text-sm">
                                            Crop & Use
                                        </button>
                                        <button type="button" @click="uploadOriginal" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                            Use Original
                                        </button>
                                        <button type="button" @click="cancelCrop" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200" x-data="imageCropper({ target: 'tempImage', ratio: 5/4, width: 1000 })">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gallery Images</label>
                        <p class="text-xs text-gray-500 mb-3">Recommended size: <span class="font-medium text-gray-700">1000x800px</span> (5:4 ratio)</p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase tracking-wide">Bulk Upload</label>
                                <input wire:model="images" type="file" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase tracking-wide">Crop Single</label>
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
                                    <div class="relative group aspect-square bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                                        <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <button type="button" wire:click="removeImage({{ $index }})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-md">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @error('images.*') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                        
                        <div x-show="isCropping" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Image</h3>
                                        <div class="relative w-full h-[400px] bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                            <img x-ref="cropImage" class="max-w-full max-h-full block" style="max-width: 100%;">
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                        <button type="button" @click="cropAndUpload" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:w-auto sm:text-sm">
                                            Crop & Use
                                        </button>
                                        <button type="button" @click="uploadOriginal" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                            Use Original
                                        </button>
                                        <button type="button" @click="cancelCrop" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($currentStep === 4)
                <div class="p-6 space-y-6 animate-fade-in">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center border-b pb-3 mb-5">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pricing & Inventory
                    </h3>

                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <label class="flex items-center">
                                <input wire:model.live="has_attributes" type="checkbox" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3 text-sm font-bold text-gray-800">Add Attributes (e.g. Color, Size)</span>
                            </label>
                        </div>

                        @if($has_attributes)
                            <div class="space-y-6 animate-fade-in" x-data="{ isNewAttribute: false }">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase">Selected Attributes</h4>
                                    <button type="button" @click="isNewAttribute = !isNewAttribute" class="text-xs font-bold text-blue-600 hover:text-blue-800 underline">
                                        <span x-show="!isNewAttribute">+ Create New Attribute</span>
                                        <span x-show="isNewAttribute">Cancel & Select Existing</span>
                                    </button>
                                </div>

                                <div x-show="isNewAttribute" class="bg-white p-4 rounded-lg border border-blue-200 shadow-sm space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1">Attribute Name</label>
                                        <input wire:model="newAttribute.name" type="text" class="block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2" placeholder="e.g. Material">
                                        @error('newAttribute.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1">Values</label>
                                        <div class="space-y-2">
                                            @foreach($newAttribute['values'] as $index => $val)
                                                <div class="flex items-center gap-2">
                                                    <input wire:model="newAttribute.values.{{ $index }}" type="text" class="block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2" placeholder="Value">
                                                    @if(count($newAttribute['values']) > 1)
                                                        <button type="button" wire:click="removeAttributeValueField({{ $index }})" class="text-red-500 hover:text-red-700 p-1">&times;</button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" wire:click="addAttributeValueField" class="mt-2 text-xs font-bold text-blue-600 hover:underline">+ Add Value</button>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" wire:click="saveNewAttribute" @click="isNewAttribute = false" class="bg-blue-600 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-blue-700">Save & Use</button>
                                    </div>
                                </div>

                                <div x-show="!isNewAttribute">
                                    <select wire:model.live="selectedAttributes" multiple class="block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2 min-h-[100px]">
                                        @foreach ($productAttributes as $attribute)
                                            <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple.</p>
                                </div>

                                @if(count($selectedAttributes) > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                        @foreach ($selectedAttributes as $attrId)
                                            @php $attr = $productAttributes->find($attrId); @endphp
                                            @if($attr)
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">{{ $attr->name }}</label>
                                                    <select wire:model.live="attributeValues.{{ $attrId }}" multiple class="block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2 h-24">
                                                        @foreach ($attr->values as $value)
                                                            <option value="{{ $value->id }}">{{ $value->value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @if($has_attributes && count($selectedAttributes) > 0)
                            <div class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100">
                                <input wire:model.live="has_variations" type="checkbox" id="has_variations" class="h-5 w-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <div class="ml-3">
                                    <label for="has_variations" class="text-sm font-bold text-gray-900">Enable Variations</label>
                                    <p class="text-xs text-purple-700">Check this to set different prices/stock for each attribute combination.</p>
                                </div>
                            </div>
                        @endif

                        @if($has_variations)
                            <div class="animate-fade-in space-y-4">
                                <div class="text-right">
                                    <button type="button" wire:click="generateVariations" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-black transition shadow-sm">
                                        Generate Variations Table
                                    </button>
                                </div>

                                @if (!empty($variations))
                                    <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold border-b border-gray-200">
                                                <tr>
                                                    <th class="p-3">Variation</th>
                                                    <th class="p-3">SKU</th>
                                                    <th class="p-3 w-32">Price</th>
                                                    <th class="p-3 w-32">Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                @foreach ($variations as $index => $var)
                                                    <tr>
                                                        <td class="p-3">
                                                            @foreach ($var['attribute_values'] as $attrId => $valId)
                                                                <span class="inline-block bg-gray-100 border border-gray-200 rounded px-2 py-1 text-xs text-gray-700 mr-1">
                                                                    {{ $productAttributes->find($attrId)->name }}: <b>{{ $productAttributes->find($attrId)->values->find($valId)->value }}</b>
                                                                </span>
                                                            @endforeach
                                                        </td>
                                                        <td class="p-3">
                                                            <input wire:model="variations.{{ $index }}.sku" type="text" class="block w-full border border-gray-300 rounded shadow-sm text-xs px-2 py-1.5">
                                                            @error("variations.{$index}.sku") <span class="text-red-600 text-[10px] block">{{ $message }}</span> @enderror
                                                        </td>
                                                        <td class="p-3">
                                                            <input wire:model="variations.{{ $index }}.price" type="number" step="0.01" class="block w-full border border-gray-300 rounded shadow-sm text-xs px-2 py-1.5">
                                                            @error("variations.{$index}.price") <span class="text-red-600 text-[10px] block">{{ $message }}</span> @enderror
                                                        </td>
                                                        <td class="p-3">
                                                            <input wire:model="variations.{{ $index }}.stock" type="number" class="block w-full border border-gray-300 rounded shadow-sm text-xs px-2 py-1.5">
                                                            @error("variations.{$index}.stock") <span class="text-red-600 text-[10px] block">{{ $message }}</span> @enderror
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-6 animate-fade-in">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Regular Price ($)</label>
                                    <input wire:model="price" type="number" step="0.01" class="block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2 font-mono">
                                    @error('price') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                                    <input wire:model="stock" type="number" class="block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2 font-mono">
                                    @error('stock') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-gray-50 px-8 py-5 border-t border-gray-200 flex justify-between items-center rounded-b-lg">
                <div>
                    @if($currentStep > 1)
                        <button type="button" wire:click="previousStep" class="px-5 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Back
                        </button>
                    @endif
                </div>
                <div>
                    @if($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep" class="px-5 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md transition flex items-center">
                            Next Step
                        </button>
                    @else
                        <button type="submit" class="px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-md transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Create Product
                        </button>
                    @endif
                </div>
            </div>

        </form>
    </div>

    <script>
        function imageCropper(config) {
        return {
            isCropping: false,
            cropper: null,
            selectedFile: null,
            targetProperty: config.target, 
            aspectRatio: config.ratio || 1,
            outputWidth: config.width || 1000, 

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
                        width: this.outputWidth, 
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
