<div class="bg-white p-3 rounded-lg shadow table-container mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-base font-semibold text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-7 5h7"></path>
            </svg>
            Product List
        </h3>
        <div>
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Product
            </a>
        </div>
    </div>
    <div class="mb-3 mt-2">
        <label for="search" class="block text-sm font-medium text-gray-700">Search Products</label>
        <input wire:model.live="search" type="text" id="search" class="input-field mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2" placeholder="Search by name, slug, or category">
    </div>

    {{-- success/failed message --}}
    @include('admin.includes.message')

    <div class="overflow-x-auto">
        <table class="table-field w-full min-w-[1100px] text-left text-xs">
            <thead>
                <tr class="bg-gray-50">
                    <th class="font-medium text-gray-700 px-2 py-2">Name</th>
                    <th class="font-medium text-gray-700 px-2 py-2">Slug</th>
                    <th class="font-medium text-gray-700 px-2 py-2">Categories</th>
                    <th class="font-medium text-gray-700 px-2 py-2">Sold Amount</th>
                    <th class="font-medium text-gray-700 px-2 py-2 w-[240px]">Attributes</th>
                    <th class="font-medium text-gray-700 px-2 py-2 w-[280px]">Variations</th>
                    <th class="font-medium text-gray-700 px-2 py-2 w-[240px]">Related</th>
                    <th class="font-medium text-gray-700 px-2 py-2">Status</th>
                    <th class="font-medium text-gray-700 text-end px-2 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr class="border-t">
                    <td class="px-2 py-2 align-top">
                        <p class="font-medium text-gray-900 leading-5 max-w-[180px] truncate">{{ $product->name }}</p>
                    </td>
                    <td class="px-2 py-2 align-top">
                        <p class="text-gray-600 max-w-[160px] truncate">{{ $product->slug }}</p>
                    </td>
                    <td class="px-2 py-2 align-top w-[240px]">
                        <div class="flex flex-wrap gap-1">
                            @foreach ($product->categories->take(2) as $category)
                                <span class="badge bg-blue-100 text-blue-800">{{ $category->name }}</span>
                            @endforeach
                            @if ($product->categories->count() > 2)
                                <span class="badge bg-gray-100 text-gray-700">+{{ $product->categories->count() - 2 }} more</span>
                            @endif
                            @if ($product->categories->isEmpty())
                                <span class="text-gray-500">None</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-2 py-2 align-top">
                        @php
                            $soldAmount = (float) ($product->sold_amount ?? 0);
                        @endphp
                        <div class="font-semibold text-gray-900">
                            {{ $product->currency_symbol }}{{ number_format($soldAmount, 2) }}
                        </div>
                        <div class="text-[11px] text-gray-500">Total revenue</div>
                    </td>
                    <td class="px-2 py-2 align-top">
                        <div class="flex flex-wrap gap-1">
                            @foreach ($product->attributes->take(2) as $attribute)
                                @php
                                    $selectedValue = $attribute->pivot->value_id
                                        ? $attribute->values->find($attribute->pivot->value_id)?->value
                                        : null;
                                @endphp
                                <span class="badge bg-green-100 text-green-800">
                                    {{ $attribute->name }}: {{ $selectedValue ?? 'N/A' }}
                                </span>
                            @endforeach
                            @if ($product->attributes->count() > 2)
                                <span class="badge bg-gray-100 text-gray-700">+{{ $product->attributes->count() - 2 }} more</span>
                            @endif
                            @if ($product->attributes->isEmpty())
                                <span class="text-gray-500">None</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-2 py-2 align-top w-[280px]">
                        @if ($product->has_variations && $product->variations->isNotEmpty())
                            @php
                                $primaryVariation = $product->variations->first();
                                $moreVariations = $product->variations->count() - 1;
                            @endphp
                            <div class="space-y-1">
                                <p class="text-[11px] font-medium text-purple-700">{{ $product->variations->count() }} variation(s)</p>
                                <p class="text-[11px] text-gray-700 leading-4 truncate" title="SKU: {{ $primaryVariation->sku ?: 'No SKU' }} | {{ $product->currency_symbol }}{{ number_format($primaryVariation->price, 2) }} | Stock {{ $primaryVariation->stock }}">
                                    SKU: {{ $primaryVariation->sku ?: 'No SKU' }} | {{ $product->currency_symbol }}{{ number_format($primaryVariation->price, 2) }} | Stock {{ $primaryVariation->stock }}
                                </p>
                                @if ($moreVariations > 0)
                                    <p class="text-[11px] text-gray-500">+{{ $moreVariations }} more variation(s)</p>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-500">No variations</span>
                        @endif
                    </td>
                    <td class="px-2 py-2 align-top w-[240px]">
                        <div class="flex flex-wrap gap-1">
                            @foreach ($product->relatedProducts->take(2) as $related)
                                <span class="badge bg-yellow-100 text-yellow-800 whitespace-nowrap max-w-[180px] truncate" title="{{ $related->name }}">{{ $related->name }}</span>
                            @endforeach
                            @if ($product->relatedProducts->count() > 2)
                                <span class="badge bg-gray-100 text-gray-700">+{{ $product->relatedProducts->count() - 2 }} more</span>
                            @endif
                            @if ($product->relatedProducts->isEmpty())
                            <span class="text-gray-500">None</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-2 py-2 align-top">
                        @if ($product->status)
                            <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">Active</span>
                        @else
                            <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end space-x-1 px-2 py-2 align-top whitespace-nowrap">
                        <!-- View -->
                        <a href="{{ route('admin.products.show', $product->id) }}" class="inline-flex items-center justify-center h-7 w-7 border border-blue-200 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <!-- Edit -->
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center justify-center h-7 w-7 border border-green-200 text-green-600 hover:text-green-800 hover:bg-green-50 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                        </a>

                        <!-- Delete -->
                        <button wire:click="deleteProduct({{ $product->id }})" wire:loading.attr="disabled" wire:confirm="Are you sure you want to delete this product?" class="inline-flex items-center justify-center h-7 w-7 border border-red-200 text-red-600 hover:text-red-800 hover:bg-red-50 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                            </svg>
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-gray-500 py-4">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4">
        {{ $products->links() }}
    </div>
</div>
