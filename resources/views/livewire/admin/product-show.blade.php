<div class="bg-white p-6 rounded-lg shadow card-container mx-auto">
    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Product Details
    </h3>
    <div class="space-y-6">
        <!-- Product Images -->
        <div>
            <h4 class="section-title">Product Images</h4>
            <div class="section-content image-container flex overflow-x-auto space-x-2">
                @if ($product->thumbnail)
                    <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }} Thumbnail" class="thumbnail-image w-24 h-24 rounded-lg object-cover border border-gray-200">
                @else
                    <div class="w-24 h-24 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 text-gray-500 text-sm">
                        No thumbnail
                    </div>
                @endif
                @if ($product->images->count())
                    @foreach ($product->images as $image)
                        <img src="{{ Storage::url($image->image_path) }}" alt="{{ $product->name }} Image" class="additional-image w-24 h-24 rounded-lg object-cover border border-gray-200">
                    @endforeach
                @else
                    <div class="w-24 h-24 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 text-gray-500 text-sm">
                        No images
                    </div>
                @endif
            </div>
        </div>

        <!-- Name -->
        <div>
            <h4 class="section-title">Name</h4>
            <p class="section-content">{{ $product->name }}</p>
        </div>

        <!-- Slug -->
        <div>
            <h4 class="section-title">Slug</h4>
            <p class="section-content">{{ $product->slug }}</p>
        </div>

        <!-- Description -->
        <div>
            <h4 class="section-title">Description</h4>
            <p class="section-content">{{ $product->description ?? 'No description available' }}</p>
        </div>

        <!-- Categories -->
        <div>
            <h4 class="section-title">Categories</h4>
            <div class="section-content flex flex-wrap gap-2">
                @foreach ($product->categories as $category)
                    <span class="badge bg-blue-100 text-blue-800">{{ $category->name }}</span>
                @endforeach
                @if ($product->categories->isEmpty())
                    <span class="text-gray-500">None</span>
                @endif
            </div>
        </div>

        <!-- Attributes -->
        <div>
            <h4 class="section-title">Attributes</h4>
            <div class="section-content flex flex-wrap gap-2">
                @foreach ($product->attributes as $attribute)
                    <span class="badge bg-green-100 text-green-800">
                        {{ $attribute->name }}: {{ $attribute->pivot->value_id ? $attribute->values->find($attribute->pivot->value_id)->value : 'N/A' }}
                    </span>
                @endforeach
                @if ($product->attributes->isEmpty())
                    <span class="text-gray-500">None</span>
                @endif
            </div>
        </div>

        <!-- Variations -->
        <div>
            <h4 class="section-title">Variations</h4>
            <div class="section-content">
                @if ($product->has_variations)
                    @foreach ($product->variations as $variation)
                        <div class="mb-2">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($variation->values as $value)
                                    <span class="badge bg-purple-100 text-purple-800">
                                        {{ $value->attribute->name }}: {{ $value->value }}
                                    </span>
                                @endforeach
                            </div>
                            <p class="variation-details mt-1">
                                SKU: {{ $variation->sku }}, Price: {{$product->currency_symbol}}{{ number_format($variation->price, 2) }}, Stock: {{ $variation->stock }}
                            </p>
                        </div>
                    @endforeach
                @else
                    <span class="text-gray-500">No variations</span>
                @endif
            </div>
        </div>

        <!-- Related Products -->
        <div>
            <h4 class="section-title">Related Products</h4>
            <div class="section-content flex flex-wrap gap-2">
                @foreach ($product->relatedProducts as $related)
                    <span class="badge bg-yellow-100 text-yellow-800">{{ $related->name }}</span>
                @endforeach
                @if ($product->relatedProducts->isEmpty())
                    <span class="text-gray-500">None</span>
                @endif
            </div>
        </div>

        <!-- Status -->
        <div>
            <h4 class="section-title">Status</h4>
            @if ($product->status)
                <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">Active</span>
            @else
                <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">Inactive</span>
            @endif
        </div>

        <!-- Stock & Price -->
        <div>
            <h4 class="section-title">Stock & Price</h4>
            <div class="section-content">
                @if ($product->has_variations)
                    <table class="price-table">
                        <thead>
                            <tr>
                                <th>Variation</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product->variations as $variation)
                                <tr>
                                    <td>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($variation->values as $value)
                                                <span class="badge bg-purple-100 text-purple-800">
                                                    {{ $value->attribute->name }}: {{ $value->value }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>{{ $variation->sku }}</td>
                                    <td>{{$product->currency_symbol}}{{ number_format($variation->price, 2) }}</td>
                                    <td>{{ $variation->stock }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <table class="price-table">
                        <thead>
                            <tr>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->stock }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- Back Button -->
        <div>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Product List
            </a>
        </div>
    </div>

<style>
        .card-container {
            transition: all 0.3s ease;
        }
        .card-container:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .section-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
        }
        .section-content {
            font-size: 0.875rem;
            color: #4b5563;
        }
        .variation-details {
            font-size: 0.75rem;
            color: #6b7280;
        }
        .image-container {
            display: flex;
            overflow-x-auto;
            gap: 0.5rem;
        }
        .thumbnail-image, .additional-image {
            object-fit: cover;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
        }
        .price-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            overflow: hidden;
        }
        .price-table th, .price-table td {
            padding: 0.75rem;
            text-align: left;
            font-size: 0.875rem;
        }
        .price-table th {
            background-color: #f9fafb;
            font-weight: 500;
            color: #374151;
        }
        .price-table td {
            border-top: 1px solid #e5e7eb;
        }
        .price-table tr:hover {
            background-color: #f3f4f6;
        }
    </style>
</div>
