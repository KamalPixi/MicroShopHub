<div class="bg-white p-4 rounded-lg shadow form-container mx-auto">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add New Product
    </h3>

    <div class="space-y-4">
    
        <div>
            {{-- success/failed message --}}
            @include('admin.includes.message')
            @include('admin.includes.errors')
        </div>

        <form wire:submit.prevent="submit" class="space-y-5">
            <!-- Product Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input wire:model.live="name" type="text" id="name" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="Enter product name">
                @error('name') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                <input wire:model="slug" type="text" id="slug" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="Enter product slug">
                @error('slug') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model="description" id="description" rows="4" class="textarea-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="Describe the product"></textarea>
                @error('description') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Thumbnail -->
            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700">Thumbnail (Main Image)</label>
                <p class="text-xs text-gray-500 mb-2">
                    Recommended size: <span class="font-medium">500x500px</span> (1:1 ratio)
                </p>
                <input wire:model="thumbnail" type="file" id="thumbnail" accept="image/*" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2">
                <p class="mt-1 text-xs text-gray-500">Upload the main product image (JPEG, PNG).</p>
                @if ($thumbnail)
                    <div class="image-preview">
                        <img src="{{ $thumbnail->temporaryUrl() }}" alt="Thumbnail Preview" class="w-24 h-24 rounded-lg object-cover border border-gray-200">
                    </div>
                @endif
                @error('thumbnail') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Additional Images -->
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">Additional Images</label>
                <p class="text-xs text-gray-500 mb-2">
                    Recommended size: <span class="font-medium">1000x800px</span> (5:4 ratio)
                </p>
                <input wire:model="images" type="file" id="images" accept="image/*" multiple class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2">
                <p class="mt-1 text-xs text-gray-500">Upload additional product images (JPEG, PNG).</p>
                @if ($images)
                    <div class="image-preview flex overflow-x-auto space-x-2 mt-2">
                        @foreach ($images as $image)
                            <img src="{{ $image->temporaryUrl() }}" alt="Image Preview" class="w-24 h-24 rounded-lg object-cover border border-gray-200">
                        @endforeach
                    </div>
                @endif
                @error('images.*') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Categories -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Categories</label>
                <select wire:model="selectedCategories" multiple class="select-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                    @foreach ($categoryOptions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Select one or more categories (subcategories are indented).</p>
                @error('selectedCategories') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Related Products -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Related Products</label>
                <select wire:model="relatedProducts" multiple class="select-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                    @foreach ($availableProducts as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Select products for cross-sells or upsells (e.g., "You may also like").</p>
                @error('relatedProducts') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Has Attributes Checkbox -->
            <div class="flex items-center">
                <input wire:model.live="has_attributes" type="checkbox" id="has_attributes" class="checkbox-field h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="has_attributes" class="ml-2 block text-sm font-medium text-gray-700">Has Attributes</label>
            </div>

            @if ($has_attributes)
                <div>
                    <!-- Add New Attribute -->
                    <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200 mb-3">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Add New Attribute</h4>
                        <div class="space-y-2">
                            <div>
                                <label for="newAttributeName" class="block text-sm font-medium text-gray-700">Attribute Name:</label>
                                <input wire:model="newAttribute.name" type="text" id="newAttributeName" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="e.g., Color">
                                @error('newAttribute.name') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Attribute Values:</label>
                                @foreach ($newAttribute['values'] as $index => $value)
                                <div class="flex items-center space-x-2 mt-1">
                                    <input wire:model="newAttribute.values.{{ $index }}" type="text" class="input-field flex-1 block border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm px-3 py-2" placeholder="e.g., Red">
                                    <button type="button" wire:click="removeAttributeValueField({{ $index }})" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-3 py-1 rounded-md transition">×</button>
                                </div>
                                @error("newAttribute.values.{$index}") <span class="error-message">{{ $message }}</span> @enderror
                                @endforeach
                                <button type="button" wire:click="addAttributeValueField" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-3 py-1 rounded-md mt-2 transition">
                                    + Add Value
                                </button>
                            </div>

                            <button type="button" wire:click="saveNewAttribute" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md mt-3 transition">
                                💾 Save Attribute
                            </button>
                        </div>
                    </div>

                    <!-- Select Attributes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Attributes</label>
                        <select wire:model.live="selectedAttributes" multiple class="select-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                            @foreach ($productAttributes as $attribute)
                            <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedAttributes') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <!-- Attribute Values -->
                    @foreach ($selectedAttributes as $attrId)
                    @php $attr = $productAttributes->find($attrId); @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ $attr->name }} Values</label>
                        <select wire:model.live="attributeValues.{{ $attrId }}" multiple class="select-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                            @foreach ($attr->values as $value)
                            <option value="{{ $value->id }}">{{ $value->value }}</option>
                            @endforeach
                        </select>
                        @error("attributeValues.{$attrId}") <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    @endforeach

                    <!-- Display Selected Attributes and Values -->
                    @if (!empty($selectedAttributesDisplay))
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Attributes</h4>
                        <div class="attribute-display p-4">
                            @foreach ($selectedAttributesDisplay as $attr)
                            <div class="mb-2">
                                <span class="font-medium text-gray-800">{{ $attr['name'] }}:</span>
                                <span class="text-gray-600">{{ $attr['values'] ?: 'No values selected' }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @endif


            <!-- Has Variations Checkbox -->
            <div class="flex items-center">
                <input wire:model.live="has_variations" type="checkbox" id="has_variations" class="checkbox-field h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="has_variations" class="ml-2 block text-sm font-medium text-gray-700">Has Variations</label>
            </div>

            <!-- Generate Variations Button (Conditional) -->
            @if ($has_variations)
            <button type="button" wire:click="generateVariations" class="form-button bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">Generate Variations</button>

            <!-- Variations Table -->
            @if (!empty($variations))
            <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200">
                <div class="mt-1">
                    <h3 class="text-base font-semibold text-gray-800 mb-3">Variations</h3>
                    <table class="table-field w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-2 font-medium text-gray-700">Combination</th>
                                <th class="p-2 font-medium text-gray-700">SKU</th>
                                <th class="p-2 font-medium text-gray-700">Price</th>
                                <th class="p-2 font-medium text-gray-700">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($variations as $index => $var)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-2">
                                    @foreach ($var['attribute_values'] as $attrId => $valId)
                                    {{ $productAttributes->find($attrId)->name }}: {{ $productAttributes->find($attrId)->values->find($valId)->value }}<br>
                                    @endforeach
                                </td>
                                <td class="p-2">
                                    <input wire:model="variations.{{ $index }}.sku" type="text" class="input-field w-full border border-gray-300 rounded-md text-sm">
                                    @error("variations.{$index}.sku") <span class="error-message">{{ $message }}</span> @enderror
                                </td>
                                <td class="p-2">
                                    <input wire:model="variations.{{ $index }}.price" type="number" step="0.01" class="input-field w-full border border-gray-300 rounded-md text-sm">
                                    @error("variations.{$index}.price") <span class="error-message">{{ $message }}</span> @enderror
                                </td>
                                <td class="p-2">
                                    <input wire:model="variations.{{ $index }}.stock" type="number" class="input-field w-full border border-gray-300 rounded-md text-sm">
                                    @error("variations.{$index}.stock") <span class="error-message">{{ $message }}</span> @enderror
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @else
            <!-- Conditional Fields for No Variations -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input wire:model="price" type="number" step="0.01" id="price" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="0.00">
                    @error('price') <span class="error-message">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                    <input wire:model="stock" type="number" id="stock" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="0">
                    @error('stock') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>
            @endif
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model="status" class="form-input mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                @error('status') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="form-button bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Product
            </button>
        </form>
    </div>
</div>
