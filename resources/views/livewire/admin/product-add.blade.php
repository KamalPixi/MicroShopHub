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
                            <input wire:model.blur="sku" type="text" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="SKU-12345">
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
                <div class="p-6 space-y-6 animate-fade-in" x-data="imageCropper({ target: 'thumbnail', ratio: 1/1, width: 1000 })">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center border-b pb-3 mb-5">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Product Images
                    </h3>

                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <div class="flex flex-col md:flex-row gap-6 items-start">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Main Thumbnail <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-3">Required: 1:1 Aspect Ratio (Recommended: High quality 1000x1000px)</p>
                                
                                <input type="file" accept="image/*" @change="fileChosen" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                                @error('thumbnail') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-32 h-32 bg-white border border-gray-300 rounded-lg flex items-center justify-center overflow-hidden shadow-sm relative group">
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <button type="button" wire:click="removeThumbnail" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-md">
                                        &times;
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs text-center font-medium">No Image</span>
                                @endif
                            </div>
                        </div>
                        
                        <div x-show="isCropping" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-[100] overflow-y-auto" 
                             style="display: none;">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="fixed inset-0 bg-black bg-opacity-60 transition-opacity" @click="cancelCrop"></div>
                                
                                <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full overflow-hidden transform transition-all">
                                    <div class="bg-white px-6 py-4 border-b flex justify-between items-center">
                                        <h3 class="text-lg font-bold text-gray-900">Crop Product Thumbnail</h3>
                                        <button type="button" @click="cancelCrop" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    
                                    <div class="p-6 bg-gray-50">
                                        <div class="relative w-full aspect-square max-h-[50vh] bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                                            <img x-ref="cropImage" class="max-w-full max-h-full block">
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white px-6 py-4 border-t flex flex-col sm:flex-row-reverse gap-3">
                                        <button type="button" @click="cropAndUpload" :disabled="isUploading" class="inline-flex justify-center items-center px-6 py-2.5 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition disabled:opacity-50">
                                            <svg x-show="isUploading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <span x-text="isUploading ? 'Uploading...' : 'Crop & Done'"></span>
                                        </button>
                                        <button type="button" @click="uploadOriginal" :disabled="isUploading" class="inline-flex justify-center items-center px-6 py-2.5 bg-gray-900 text-white font-bold text-sm rounded-xl hover:bg-black shadow-lg shadow-gray-200 transition disabled:opacity-50">
                                            Use Original
                                        </button>
                                        <button type="button" @click="cancelCrop" :disabled="isUploading" class="inline-flex justify-center items-center px-6 py-2.5 bg-white text-gray-700 border border-gray-300 font-bold text-sm rounded-xl hover:bg-gray-50 transition disabled:opacity-50">
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
                        
                        <div x-show="isCropping" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-[100] overflow-y-auto" 
                             style="display: none;">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="fixed inset-0 bg-black bg-opacity-60 transition-opacity" @click="cancelCrop"></div>
                                
                                <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden transform transition-all">
                                    <div class="bg-white px-6 py-4 border-b flex justify-between items-center">
                                        <h3 class="text-lg font-bold text-gray-900">Crop Gallery Image</h3>
                                        <button type="button" @click="cancelCrop" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    
                                    <div class="p-6 bg-gray-50">
                                        <div class="relative w-full aspect-video max-h-[60vh] bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                                            <img x-ref="cropImage" class="max-w-full max-h-full block">
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white px-6 py-4 border-t flex flex-col sm:flex-row-reverse gap-3">
                                        <button type="button" @click="cropAndUpload" :disabled="isUploading" class="inline-flex justify-center items-center px-6 py-2.5 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition disabled:opacity-50">
                                            <svg x-show="isUploading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <span x-text="isUploading ? 'Uploading...' : 'Crop & Done'"></span>
                                        </button>
                                        <button type="button" @click="uploadOriginal" :disabled="isUploading" class="inline-flex justify-center items-center px-6 py-2.5 bg-gray-900 text-white font-bold text-sm rounded-xl hover:bg-black shadow-lg shadow-gray-200 transition disabled:opacity-50">
                                            Use Original
                                        </button>
                                        <button type="button" @click="cancelCrop" :disabled="isUploading" class="inline-flex justify-center items-center px-6 py-2.5 bg-white text-gray-700 border border-gray-300 font-bold text-sm rounded-xl hover:bg-gray-50 transition disabled:opacity-50">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Video Section --}}
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200"
                         x-data="{ videoTab: @entangle('videoSource') }">
                        <h4 class="text-sm font-semibold text-gray-800 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Product Video <span class="text-gray-400 font-normal text-xs">(optional)</span>
                        </h4>
                        <p class="text-xs text-gray-500 mb-4">Attach a demo or promo video to the product page.</p>

                        {{-- Tab switcher --}}
                        <div class="flex rounded-lg border border-gray-200 overflow-hidden mb-5 w-fit bg-white shadow-sm">
                            <button type="button"
                                    @click="videoTab = 'upload'; $wire.set('videoSource', 'upload')"
                                    :class="videoTab === 'upload' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                    class="px-4 py-2 text-xs font-semibold transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Upload File
                            </button>
                            <button type="button"
                                    @click="videoTab = 'url'; $wire.set('videoSource', 'url')"
                                    :class="videoTab === 'url' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                    class="px-4 py-2 text-xs font-semibold transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                YouTube / URL
                            </button>
                        </div>

                        {{-- Upload tab --}}
                        <div x-show="videoTab === 'upload'" x-transition>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                Video File <span class="text-gray-400">(mp4, mov, avi, webm &mdash; max 100 MB)</span>
                            </label>
                            <input wire:model="videoFile" type="file" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                            @error('videoFile') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror

                            @if ($videoFile)
                                <div class="mt-3">
                                    <p class="text-xs text-gray-500 mb-1">Preview:</p>
                                    <video controls class="w-full max-h-48 rounded-lg border border-gray-200 shadow-sm bg-black">
                                        <source src="{{ $videoFile->temporaryUrl() }}">
                                    </video>
                                </div>
                            @endif
                        </div>

                        {{-- URL tab --}}
                        <div x-show="videoTab === 'url'" x-transition>
                            <label class="block text-xs font-medium text-gray-700 mb-1">YouTube or External Video URL</label>
                            <input wire:model.live="videoUrl" type="url"
                                   placeholder="https://www.youtube.com/watch?v=..."
                                   class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                            @error('videoUrl') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror

                            @if ($videoUrl)
                                <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    URL set: {{ $videoUrl }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Measurements / Size Chart Section --}}
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="p-1.5 bg-blue-100 text-blue-700 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                </span>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800">Measurements & Size Guide <span class="text-gray-400 font-normal text-xs">(optional)</span></h4>
                                    <p class="text-xs text-gray-500">Provide dimensions (e.g., Shoulder, Bust, Length, Waist) for each product variation/size.</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="hasMeasurements" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        @if ($hasMeasurements)
                            <div class="mt-5 pt-4 border-t border-gray-200 space-y-4 animate-fade-in">
                                {{-- Measurement Unit Selector --}}
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-semibold text-gray-700">Measurement Unit:</span>
                                    <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm">
                                        <button type="button" 
                                                wire:click="$set('measurementUnit', 'cm')"
                                                class="px-3 py-1 text-xs font-medium rounded-md transition-all {{ $measurementUnit === 'cm' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                                            cm
                                        </button>
                                        <button type="button" 
                                                wire:click="$set('measurementUnit', 'inch')"
                                                class="px-3 py-1 text-xs font-medium rounded-md transition-all {{ $measurementUnit === 'inch' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                                            inches (in)
                                        </button>
                                    </div>
                                </div>

                                {{-- Columns Configuration --}}
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-xs space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                                            1. Define Measurement Parameters (Columns)
                                        </label>
                                        <button type="button" wire:click="addMeasurementColumn" 
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Add Parameter
                                        </button>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($measurementColumns as $colIndex => $colName)
                                            <div class="inline-flex items-center bg-gray-50 border border-gray-300 rounded-lg overflow-hidden focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 shadow-xs">
                                                <input type="text" 
                                                       wire:model="measurementColumns.{{ $colIndex }}"
                                                       placeholder="e.g. Bust"
                                                       class="text-xs border-0 bg-transparent py-1.5 px-2.5 text-gray-800 placeholder-gray-400 focus:outline-none w-28 font-medium">
                                                <button type="button" 
                                                        wire:click="removeMeasurementColumn({{ $colIndex }})"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 transition border-l border-gray-200"
                                                        title="Remove column">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Size Rows Table --}}
                                <div class="bg-white rounded-lg border border-gray-200 shadow-xs overflow-hidden">
                                    <div class="p-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                                            2. Size Values Grid
                                        </label>
                                        <button type="button" wire:click="addMeasurementRow" 
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Add Size Row
                                        </button>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs text-left">
                                            <thead class="bg-gray-100 text-gray-700 font-semibold border-b border-gray-200">
                                                <tr>
                                                    <th class="py-2.5 px-3 w-28">Size / Tag</th>
                                                    @foreach ($measurementColumns as $colName)
                                                        <th class="py-2.5 px-3 min-w-[90px]">
                                                            {{ $colName ?: 'Param' }} ({{ $measurementUnit }})
                                                        </th>
                                                    @endforeach
                                                    <th class="py-2.5 px-3 w-10 text-center"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @forelse ($measurementRows as $rowIndex => $row)
                                                    <tr class="hover:bg-gray-50/70 transition">
                                                        <td class="p-2">
                                                            <input type="text" 
                                                                   wire:model="measurementRows.{{ $rowIndex }}.label"
                                                                   placeholder="e.g. S / 38"
                                                                   class="w-full text-xs font-bold text-gray-900 border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                                        </td>
                                                        @foreach ($measurementColumns as $colIndex => $_)
                                                            <td class="p-2">
                                                                <input type="text" 
                                                                       wire:model="measurementRows.{{ $rowIndex }}.values.{{ $colIndex }}"
                                                                       placeholder="0"
                                                                       class="w-full text-xs text-gray-700 border border-gray-200 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                                            </td>
                                                        @endforeach
                                                        <td class="p-2 text-center">
                                                            <button type="button" 
                                                                    wire:click="removeMeasurementRow({{ $rowIndex }})"
                                                                    class="text-gray-400 hover:text-red-600 transition p-1 rounded hover:bg-red-50"
                                                                    title="Remove row">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ count($measurementColumns) + 2 }}" class="p-4 text-center text-gray-400 text-xs italic">
                                                            No size rows added yet. Click "Add Size Row" to start adding size dimensions.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
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
            isUploading: false,
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
                    this.isUploading = true;
                    this.cropper.getCroppedCanvas({
                        width: this.outputWidth, 
                    }).toBlob((blob) => {
                        const file = new File([blob], 'cropped-image.jpg', { type: 'image/jpeg' });
                        @this.upload(this.targetProperty, file, (uploadedFilename) => {
                            this.isUploading = false;
                            this.cancelCrop();
                        }, () => {
                            this.isUploading = false;
                            alert('Upload failed');
                        });
                    }, 'image/jpeg', 0.85);
                }
            },

            uploadOriginal() {
                if (this.selectedFile) {
                    this.isUploading = true;
                    @this.upload(this.targetProperty, this.selectedFile, (uploadedFilename) => {
                        this.isUploading = false;
                        this.cancelCrop();
                    }, () => {
                        this.isUploading = false;
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
