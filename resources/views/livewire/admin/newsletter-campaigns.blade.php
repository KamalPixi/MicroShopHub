<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Campaign Builder</h3>
                <p class="text-xs text-gray-500">Choose a template, pick products, preview the email, then send or schedule it.</p>
            </div>
            @if($campaignId)
                <button type="button" wire:click="resetForm" class="admin-action-btn admin-action-delete" title="Cancel edit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        @include('admin.includes.message')

        <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500">Total</p>
                <p class="text-lg font-semibold text-gray-900">{{ $totalCount }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500">Scheduled</p>
                <p class="text-lg font-semibold text-gray-900">{{ $scheduledCount }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500">Sent</p>
                <p class="text-lg font-semibold text-gray-900">{{ $sentCount }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500">Subscribers</p>
                <p class="text-lg font-semibold text-gray-900">{{ $subscriberCount }}</p>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.25fr_0.95fr]">
            <div class="space-y-5">
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Template</p>
                            <h4 class="mt-1 text-sm font-semibold text-gray-900">Choose a campaign layout</h4>
                        </div>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-600">{{ data_get($templateOptions, $template_key . '.name', 'Template') }}</span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                        @foreach ($templateOptions as $key => $template)
                            <label class="cursor-pointer rounded-2xl border p-4 transition {{ $template_key === $key ? 'border-black bg-white ring-1 ring-black/10' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                <div class="flex items-start gap-3">
                                    <input type="radio" wire:model.live="template_key" value="{{ $key }}" class="mt-1 h-4 w-4 border-gray-300 text-black focus:ring-black">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-gray-900">{{ $template['name'] }}</span>
                                            @if($template_key === $key)
                                                <span class="rounded-full bg-black px-2 py-0.5 text-[10px] font-semibold text-white">Active</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">{{ $template['description'] }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Campaign Name</label>
                            <input wire:model.live="name" type="text" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-black focus:ring-black" placeholder="Spring launch">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Subject</label>
                            <input wire:model.live="subject" type="text" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-black focus:ring-black" placeholder="New arrivals are here">
                            @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Preheader</label>
                            <input wire:model.live="preheader" type="text" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-black focus:ring-black" placeholder="Short teaser line shown in inboxes">
                            @error('preheader') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Status</label>
                            <div class="relative mt-1">
                                <select wire:model.live="status" class="block w-full appearance-none rounded-xl border border-gray-300 bg-white px-3 py-2 pr-8 text-sm shadow-sm focus:border-black focus:ring-black">
                                    <option value="draft">Draft</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="sent">Sent</option>
                                </select>
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Campaign Copy</label>
                        <textarea wire:model.live="content" rows="6" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-black focus:ring-black" placeholder="Write the main message here..."></textarea>
                        @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">CTA Text</label>
                            <input wire:model.live="button_text" type="text" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-black focus:ring-black" placeholder="Shop now">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">CTA URL</label>
                            <input wire:model.live="button_url" type="url" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-black focus:ring-black" placeholder="https://...">
                            @error('button_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Schedule Time</label>
                            <input wire:model.live="scheduled_at" type="datetime-local" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-black focus:ring-black">
                            @error('scheduled_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-end">
                            <button wire:click="save" wire:loading.attr="disabled" class="admin-action-btn admin-action-view !h-auto !w-full !justify-center gap-2 !px-4 !py-3" title="{{ $campaignId ? 'Update Campaign' : 'Create Campaign' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ $campaignId ? 'Update Campaign' : 'Create Campaign' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                @if($this->templateUsesProducts($template_key))
                    <div class="rounded-2xl border border-gray-200 bg-white p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Products</p>
                                <h4 class="mt-1 text-sm font-semibold text-gray-900">Choose products to feature</h4>
                            </div>
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-600">{{ count($selected_product_ids) }} selected</span>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Search products</label>
                            <input wire:model.live.debounce.250ms="productSearch" type="text" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-black focus:ring-black" placeholder="Search by name or SKU">
                        </div>

                        @if($selectedProducts->isNotEmpty())
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($selectedProducts as $product)
                                    <span class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-700">
                                        {{ $product->name }}
                                        <button type="button" wire:click="toggleProduct({{ $product->id }})" class="text-gray-400 transition hover:text-gray-700">×</button>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4 max-h-[440px] overflow-y-auto pr-1">
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                                @forelse ($products as $product)
                                    <button type="button" wire:click="toggleProduct({{ $product->id }})" class="rounded-2xl border p-3 text-left transition {{ in_array($product->id, $selected_product_ids, true) ? 'border-black bg-gray-50 ring-1 ring-black/10' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                        <div class="flex items-start gap-3">
                                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                                                @if($product->thumbnail)
                                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="line-clamp-2 text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                                                <p class="mt-1 text-xs text-gray-500">{{ $product->currency_symbol }}{{ number_format((float) $product->price, 2) }}</p>
                                                <p class="mt-1 text-[11px] text-gray-400">{{ $product->sku ?: 'No SKU' }}</p>
                                            </div>
                                        </div>
                                    </button>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-5 text-center text-sm text-gray-500 md:col-span-2 xl:col-span-3">
                                        No products found.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-600">
                        This template does not use product blocks.
                    </div>
                @endif
            </div>

            <div class="space-y-4 xl:sticky xl:top-4 self-start">
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Preview</p>
                            <h4 class="mt-1 text-sm font-semibold text-gray-900">Live email preview</h4>
                        </div>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-600">{{ data_get($templateOptions, $template_key . '.name', 'Template') }}</span>
                    </div>
                    <div class="mt-3 rounded-2xl border border-gray-200 bg-gray-50 p-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">Subject</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $previewSummary['subject'] }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="rounded-full border border-gray-200 bg-white px-3 py-1 text-[11px] font-semibold text-gray-700">{{ $previewSummary['template'] }}</span>
                            <span class="rounded-full border border-gray-200 bg-white px-3 py-1 text-[11px] font-semibold text-gray-700">{{ $previewSummary['products'] }} products</span>
                            <span class="rounded-full border border-gray-200 bg-white px-3 py-1 text-[11px] font-semibold text-gray-700">{{ $previewSummary['preheader'] ?: 'No preheader' }}</span>
                        </div>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-[1.75rem] border border-gray-200 bg-gray-100 p-3 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                        <div class="mb-3 flex items-center justify-between rounded-2xl border border-gray-200 bg-white px-4 py-2 text-[11px] text-gray-500">
                            <span>Desktop email preview</span>
                            <span>Rendered layout</span>
                        </div>
                        <div class="max-h-[760px] overflow-auto rounded-[1.5rem] border border-gray-200 bg-white">
                            <div class="min-w-[740px]">
                                {!! $previewHtml !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm table-container">
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Campaigns</h3>
                <p class="text-xs text-gray-500">Review and manage previous campaigns.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-field w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-2 font-medium text-gray-700">Name</th>
                        <th class="p-2 font-medium text-gray-700">Subject</th>
                        <th class="p-2 font-medium text-gray-700">Template</th>
                        <th class="p-2 font-medium text-gray-700">Products</th>
                        <th class="p-2 font-medium text-gray-700">Status</th>
                        <th class="p-2 font-medium text-gray-700">Scheduled</th>
                        <th class="p-2 font-medium text-gray-700">Created</th>
                        <th class="p-2 font-medium text-gray-700 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($campaigns as $campaign)
                        <tr class="border-t">
                            <td class="p-2">{{ $campaign->name }}</td>
                            <td class="p-2">{{ $campaign->subject }}</td>
                            <td class="p-2">
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                    {{ data_get($templateOptions, $campaign->template_key . '.name', ucfirst($campaign->template_key ?: 'announcement')) }}
                                </span>
                            </td>
                            <td class="p-2">
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                    {{ count($campaign->featured_product_ids ?? []) }}
                                </span>
                            </td>
                            <td class="p-2">
                                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium
                                    {{ $campaign->status === 'sent' ? 'bg-green-100 text-green-700' : ($campaign->status === 'scheduled' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td class="p-2">{{ $campaign->scheduled_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="p-2">{{ $campaign->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="p-2 text-end space-x-2">
                                <button wire:click="edit({{ $campaign->id }})" class="admin-action-btn admin-action-edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                @if($campaign->status !== 'sent')
                                    <button wire:click="sendNow({{ $campaign->id }})" wire:confirm="Send this campaign to all active subscribers now?" class="admin-action-btn admin-action-success" title="Send Now">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M13 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @else
                                    <span class="admin-action-btn admin-action-success" title="Sent">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                @endif
                                <button wire:click="delete({{ $campaign->id }})" wire:confirm="Delete this campaign?" class="admin-action-btn admin-action-delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m5 0H6" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-gray-500">No campaigns found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container mt-4">
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
