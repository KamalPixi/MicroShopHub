@php
    $thumbnailUrl = null;
    if (!empty($product->thumbnail)) {
        $thumbnailUrl = \Illuminate\Support\Str::startsWith($product->thumbnail, ['http://', 'https://'])
            ? $product->thumbnail
            : Storage::url($product->thumbnail);
    }

    $galleryImages = [];
    if ($thumbnailUrl) {
        $galleryImages[] = $thumbnailUrl;
    }

    if (is_array($product->images) && count($product->images) > 0) {
        foreach ($product->images as $image) {
            $galleryImages[] = \Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])
                ? $image
                : Storage::url($image);
        }
    }

    $galleryImages = collect($galleryImages)->filter()->unique()->values()->toArray();
@endphp

<div class="bg-white rounded-lg shadow p-4 md:p-5 table-container mx-auto" x-data="{
    images: @js($galleryImages),
    index: 0,
    next() { if (this.images.length > 1) this.index = (this.index + 1) % this.images.length; },
    prev() { if (this.images.length > 1) this.index = (this.index - 1 + this.images.length) % this.images.length; },
    set(i) { this.index = i; }
}">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Product Details
        </h3>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Product List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-lg border border-gray-200 p-3">
            <h4 class="text-sm font-semibold text-gray-800 mb-2">Image Gallery</h4>

            @if(count($galleryImages) > 0)
                <div class="relative rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                    <img :src="images[index]" alt="{{ $product->name }}" class="w-full h-72 md:h-80 object-cover">

                    <button type="button" @click="prev"
                            x-show="images.length > 1"
                            class="absolute left-2 top-1/2 -translate-y-1/2 h-8 w-8 rounded-full bg-white/90 border border-gray-200 text-gray-700 hover:bg-white">
                        ‹
                    </button>
                    <button type="button" @click="next"
                            x-show="images.length > 1"
                            class="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8 rounded-full bg-white/90 border border-gray-200 text-gray-700 hover:bg-white">
                        ›
                    </button>
                </div>

                <div class="mt-2 flex gap-2 overflow-x-auto pb-1">
                    <template x-for="(img, i) in images" :key="i">
                        <button type="button"
                                @click="set(i)"
                                class="shrink-0 rounded-md overflow-hidden border-2 transition"
                                :class="index === i ? 'border-blue-500' : 'border-transparent'">
                            <img :src="img" alt="Product thumbnail" class="w-14 h-14 object-cover">
                        </button>
                    </template>
                </div>
            @else
                <div class="h-72 md:h-80 rounded-lg border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-sm text-gray-500">
                    No images available
                </div>
            @endif
        </div>

        <div class="rounded-lg border border-gray-200 p-3">
            <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                <div>
                    <h4 class="text-base font-semibold text-gray-900">{{ $product->name }}</h4>
                    <p class="text-xs text-gray-500">{{ $product->slug }}</p>
                </div>
                @if ($product->status)
                    <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">Active</span>
                @else
                    <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">Inactive</span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-2 mb-3">
                <div class="rounded-md border border-gray-200 p-2">
                    <p class="text-[11px] text-gray-500 uppercase">Base Price</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $product->currency_symbol }}{{ number_format($product->price, 2) }}</p>
                </div>
                <div class="rounded-md border border-gray-200 p-2">
                    <p class="text-[11px] text-gray-500 uppercase">Stock</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $product->stock }}</p>
                </div>
            </div>

            <div class="mb-3">
                <h5 class="text-xs font-semibold text-gray-700 mb-1">Description</h5>
                <div class="text-sm text-gray-600 leading-6 max-h-40 overflow-auto pr-1">
                    {!! $product->description ?: '<span class="text-gray-500">No description available</span>' !!}
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <h5 class="text-xs font-semibold text-gray-700 mb-1">Categories</h5>
                    <div class="flex flex-wrap gap-1">
                        @forelse ($product->categories as $category)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">{{ $category->name }}</span>
                        @empty
                            <span class="text-xs text-gray-500">None</span>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h5 class="text-xs font-semibold text-gray-700 mb-1">Attributes</h5>
                    <div class="flex flex-wrap gap-1">
                        @forelse ($product->attributes as $attribute)
                            @php
                                $selectedValue = $attribute->pivot->value_id
                                    ? $attribute->values->find($attribute->pivot->value_id)?->value
                                    : null;
                            @endphp
                            <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">
                                {{ $attribute->name }}: {{ $selectedValue ?? 'N/A' }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-500">None</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-lg border border-gray-200 p-3">
            <h4 class="text-sm font-semibold text-gray-800 mb-2">Related Products</h4>
            <div class="flex flex-wrap gap-1">
                @forelse ($product->relatedProducts as $related)
                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full max-w-full truncate" title="{{ $related->name }}">
                        {{ $related->name }}
                    </span>
                @empty
                    <span class="text-xs text-gray-500">None</span>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 p-3">
            <h4 class="text-sm font-semibold text-gray-800 mb-2">Variations</h4>
            @if ($product->has_variations && $product->variations->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-2 py-2 font-medium text-gray-700">Options</th>
                                <th class="text-left px-2 py-2 font-medium text-gray-700">SKU</th>
                                <th class="text-left px-2 py-2 font-medium text-gray-700">Price</th>
                                <th class="text-left px-2 py-2 font-medium text-gray-700">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product->variations as $variation)
                                <tr class="border-t border-gray-200">
                                    <td class="px-2 py-2 align-top">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($variation->values as $value)
                                                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-0.5 rounded-full">
                                                    {{ $value->attribute->name }}: {{ $value->value }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 text-gray-700">{{ $variation->sku ?: '-' }}</td>
                                    <td class="px-2 py-2 text-gray-700">{{ $product->currency_symbol }}{{ number_format($variation->price, 2) }}</td>
                                    <td class="px-2 py-2 text-gray-700">{{ $variation->stock }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-xs text-gray-500">No variations</p>
            @endif
        </div>
    </div>
</div>
