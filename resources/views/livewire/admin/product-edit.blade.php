<div class="bg-white p-4 rounded-lg shadow form-container mx-auto">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        Edit Product
    </h3>
    <div class="space-y-4">
        <div>
            @include('admin.includes.errors')
        </div>
        <div wire:loading wire:target="submit">
            @include('admin.includes.loading')
        </div>

        <form wire:submit.prevent="submit" class="space-y-5">
            <!-- Product Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input wire:model="name" type="text" id="name" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="Enter product name">
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

            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700">Thumbnail</label>
                <input wire:model="thumbnail" type="file" id="thumbnail" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2">
                @if ($productThumbnail)
                    <img src="{{ Storage::url($productThumbnail) }}" alt="Thumbnail" class="mt-2 w-32 h-32 object-cover rounded">
                @endif
                @error('thumbnail') <span class="error-message">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700">Additional Images</label>
                <input wire:model="images.*" type="file" id="images" multiple class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm text-sm px-3 py-2">
                @if (count($productImages))
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($productImages as $image)
                            <img src="{{ Storage::url($image->image_path) }}" alt="Product Image" class="w-24 h-24 object-cover rounded">
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
            <div class="flex space-x-4">
                <button type="submit" class="form-button bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="form-button bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Product List
                </a>
            </div>
        </form>
    </div>
    
    <style>
        .form-container {
            transition: all 0.3s ease;
        }
        .form-container:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .input-field, .textarea-field, .select-field {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .input-field:focus, .textarea-field:focus, .select-field:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .error-message {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.75rem;
            color: #dc2626;
        }
        .form-button {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .form-button:hover {
            transform: translateY(-1px);
        }
        .form-button:active {
            transform: translateY(0);
        }
        .checkbox-field {
            accent-color: #2563eb;
        }
        .table-field {
            border: 1px solid #e5e7eb;
        }
        .table-field th, .table-field td {
            padding: 0.5rem;
        }
        .table-field input {
            padding: 0.25rem;
        }
        .attribute-display {
            background-color: #f9fafb;
            border-radius: 0.5rem;
            padding: 0.5rem;
        }
    </style>
</div>
