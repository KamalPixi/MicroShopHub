<div class="bg-white p-4 rounded-lg shadow table-container mx-auto">
    <div class="flex justify-between">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-7 5h7"></path>
            </svg>
            Product List
        </h3>
        <div>
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Product
            </a>
        </div>
    </div>
    <div class="mb-4">
        <label for="search" class="block text-sm font-medium text-gray-700">Search Products</label>
        <input wire:model.live="search" type="text" id="search" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="Search by name, slug, or category">
    </div>
    <div class="overflow-x-auto">
        <table class="table-field w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="font-medium text-gray-700">Name</th>
                    <th class="font-medium text-gray-700">Slug</th>
                    <th class="font-medium text-gray-700">Categories</th>
                    <th class="font-medium text-gray-700">Attributes</th>
                    <th class="font-medium text-gray-700">Variations</th>
                    <th class="font-medium text-gray-700">Related Products</th>
                    <th class="font-medium text-gray-700">Status</th>
                    <th class="font-medium text-gray-700 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr class="border-t">
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->slug }}</td>
                    <td>
                        @foreach ($product->categories as $category)
                        <span class="badge bg-blue-100 text-blue-800">{{ $category->name }}</span>
                        @endforeach
                        @if ($product->categories->isEmpty())
                        <span class="text-gray-500">None</span>
                        @endif
                    </td>
                    <td>
                        @foreach ($product->attributes as $attribute)
                        <span class="badge bg-green-100 text-green-800">
                            {{ $attribute->name }}: {{ $attribute->pivot->value_id ? $attribute->values->find($attribute->pivot->value_id)->value : 'N/A' }}
                        </span>
                        @endforeach
                        @if ($product->attributes->isEmpty())
                        <span class="text-gray-500">None</span>
                        @endif
                    </td>
                    <td>
                        @if ($product->has_variations)
                        @foreach ($product->variations as $variation)
                        <div class="mb-1">
                            @foreach ($variation->values as $value)
                            <span class="badge bg-purple-100 text-purple-800">
                                {{ $value->attribute->name }}: {{ $value->value }}
                            </span>
                            @endforeach
                            <span class="text-xs text-gray-600">(SKU: {{ $variation->sku }}, Price: ${{ $variation->price }}, Stock: {{ $variation->stock }})</span>
                        </div>
                        @endforeach
                        @else
                        <span class="text-gray-500">No variations</span>
                        @endif
                    </td>
                    <td>
                        @foreach ($product->relatedProducts as $related)
                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2.5 py-0.5 rounded-full">{{ $related->name }}</span>
                        @endforeach
                        @if ($product->relatedProducts->isEmpty())
                            <span class="text-gray-500">None</span>
                        @endif
                    </td>
                    <td>
                        @if ($product->status)
                            <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">Active</span>
                        @else
                            <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end space-x-1">
                        <!-- View -->
                        <a href="{{ route('admin.products.show', ['product_id' => $product->id]) }}" class="inline-flex items-center py-1 text-blue-600 hover:text-blue-800 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <!-- Edit -->
                        <a href="{{ route('admin.products.edit', ['product_id' => $product->id]) }}" class="inline-flex items-center py-1 text-green-600 hover:text-green-800 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                        </a>

                        <!-- Delete -->
                        <button wire:click="deleteProduct({{ $product->id }})" wire:loading.attr="disabled" wire:confirm="Are you sure you want to delete this product?" class="inline-flex items-center py-1 text-red-600 hover:text-red-800 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                            </svg>
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-500 py-4">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4">
        {{ $products->links() }}
    </div>
</div>
