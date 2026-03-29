<div class="space-y-6">
    @php
        $activeCurrency = \App\Models\Currency::getActive();
        $currencyCode = $activeCurrency?->code ?? 'BDT';
        $currencySymbol = $activeCurrency?->symbol ?? '৳';
    @endphp
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Flash Sales</h3>
                <p class="mt-1 text-xs text-gray-500">Create timed sales and choose which products join the offer.</p>
            </div>
            @if($flashSaleId)
                <button type="button" wire:click="resetForm" class="text-xs font-semibold text-gray-600 hover:text-gray-800">Cancel edit</button>
            @endif
        </div>

        @include('admin.includes.message')

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-lg border border-gray-200 p-3">
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-lg font-semibold text-gray-900">{{ $totalCount }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-3">
                <p class="text-xs text-gray-500">Active Now</p>
                <p class="text-lg font-semibold text-green-600">{{ $activeCount }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-3">
                <p class="text-xs text-gray-500">Scheduled</p>
                <p class="text-lg font-semibold text-amber-600">{{ $scheduledCount }}</p>
            </div>
        </div>

        <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Flash Sale Title</label>
                    <input type="text" wire:model.live="title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Weekend Flash Sale">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Subtitle</label>
                    <input type="text" wire:model.live="subtitle" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Limited-time deals on top products">
                    @error('subtitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600">Description</label>
                    <textarea wire:model.live="description" rows="3" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Short promotional text shown on the storefront"></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Sale Type</label>
                    <div class="relative mt-1">
                        <select wire:model.live="saleType" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-0 focus:border-gray-300">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('saleType') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Sale Value {{ $saleType === 'percentage' ? '(%)' : "({$currencyCode})" }}</label>
                    <input type="number" step="0.01" wire:model.live="saleValue" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    @error('saleValue') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Starts At</label>
                    <input type="datetime-local" wire:model.live="startsAt" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white">
                    @error('startsAt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Ends At</label>
                    <input type="datetime-local" wire:model.live="endsAt" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white">
                    @error('endsAt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" wire:model="active" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                        <span>Enable flash sale</span>
                    </label>
                </div>
            </div>

            <div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Choose Products</p>
                        <p class="text-xs text-gray-500">Search active products and tick the items that should be in this sale.</p>
                    </div>
                    <div class="w-full max-w-sm">
                        <input type="text" wire:model.live.debounce.300ms="search" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Search by name, slug, or SKU">
                    </div>
                </div>

                @error('selectedProductIds') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror

                <div class="mt-4 rounded-xl border border-gray-200 bg-white p-4">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Selected</span>
                        <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">{{ count($selectedProductIds) }} products</span>
                    </div>
                    @if($selectedProducts->isNotEmpty())
                        <div class="mb-4 flex flex-wrap gap-2">
                            @foreach($selectedProducts as $selectedProduct)
                                <span class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-primary/5 px-3 py-1 text-xs font-semibold text-primary">
                                    {{ $selectedProduct->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                        <div class="max-h-[360px] overflow-y-auto pr-1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                                @forelse($products as $product)
                                <label class="cursor-pointer rounded-xl border p-3 transition {{ in_array($product->id, $selectedProductIds) ? 'border-primary ring-1 ring-primary/20 bg-primary/5' : 'border-gray-200 hover:border-primary/30' }}">
                                        <div class="flex items-start gap-3">
                                            <input type="checkbox" wire:model.live="selectedProductIds" value="{{ $product->id }}" class="mt-1 h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="truncate text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                                                    @if($product->has_variations)
                                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Variations</span>
                                                    @endif
                                                </div>
                                                <p class="mt-1 truncate text-xs text-gray-500">{{ $product->sku ?? 'No SKU' }}</p>
                                                <p class="mt-1 text-sm font-semibold text-primary">{{ $product->currency_symbol }}{{ number_format((float) $product->price, 2) }}</p>
                                            </div>
                                        </div>
                                    </label>
                                @empty
                                    <div class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white p-4 text-sm text-gray-500">
                                        No products found.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    {{ $flashSaleId ? 'Update Flash Sale' : 'Create Flash Sale' }}
                </button>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Flash Sale List</h3>
                <p class="mt-1 text-xs text-gray-500">Manage existing flash sale campaigns.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-2 font-medium text-gray-700">Title</th>
                        <th class="p-2 font-medium text-gray-700">Type</th>
                        <th class="p-2 font-medium text-gray-700">Value</th>
                        <th class="p-2 font-medium text-gray-700">Products</th>
                        <th class="p-2 font-medium text-gray-700">Starts</th>
                        <th class="p-2 font-medium text-gray-700">Ends</th>
                        <th class="p-2 font-medium text-gray-700">Status</th>
                        <th class="p-2 font-medium text-gray-700 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flashSales as $sale)
                        <tr class="border-t">
                            <td class="p-2">
                                <div class="font-semibold text-gray-900">{{ $sale->title }}</div>
                                @if($sale->subtitle)
                                    <div class="text-xs text-gray-500">{{ $sale->subtitle }}</div>
                                @endif
                            </td>
                            <td class="p-2 text-gray-700">{{ ucfirst($sale->sale_type) }}</td>
                            <td class="p-2 text-gray-700">
                                {{ $sale->sale_type === 'percentage' ? $sale->sale_value.'%' : $currencySymbol . number_format((float) $sale->sale_value, 2) }}
                            </td>
                            <td class="p-2 text-gray-700">{{ $sale->products_count }}</td>
                            <td class="p-2 text-gray-700">{{ $sale->starts_at?->format('Y-m-d H:i') }}</td>
                            <td class="p-2 text-gray-700">{{ $sale->ends_at?->format('Y-m-d H:i') }}</td>
                            <td class="p-2">
                                @if($sale->active && $sale->starts_at <= now() && $sale->ends_at >= now())
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Active</span>
                                @elseif($sale->active && $sale->starts_at > now())
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Scheduled</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="p-2 text-end space-x-2">
                                <button wire:click="edit({{ $sale->id }})" class="admin-action-btn admin-action-edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $sale->id }})" wire:confirm="Delete this flash sale?" class="admin-action-btn admin-action-delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-gray-500">No flash sales found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $flashSales->links() }}
        </div>
    </section>
</div>
