<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Homepage Settings</h3>
                <p class="mt-1 text-xs text-gray-500">Control how the storefront homepage looks and which sections appear.</p>
            </div>
        </div>

        @include('admin.includes.message')

        <div class="mt-5 space-y-5">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Hero / Banner</p>
                        <p class="text-xs text-gray-500">Choose how the storefront banner is presented.</p>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" wire:model="settings.home_hero_enabled" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                        <span>Show banner</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Banner Type</label>
                        <select wire:model.live="settings.home_banner_type" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white">
                            <option value="split">Banner Type 1 - Split banner with slider on the left</option>
                            <option value="slider_only">Banner Type 2 - Full slider banner only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Recommended Image Size</label>
                        <div class="mt-1 rounded-lg border border-dashed border-gray-300 bg-white px-3 py-2 text-xs text-gray-600">
                            @if(($settings['home_banner_type'] ?? 'split') === 'split')
                                Use portrait or tall images. Recommended: 900x1100 px.
                            @else
                                Use wide banner images. Recommended: 1600x600 px.
                            @endif
                        </div>
                    </div>
                </div>

                @if(($settings['home_banner_type'] ?? 'split') === 'split')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Title</label>
                            <input type="text" wire:model="settings.home_hero_title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Button Label</label>
                            <input type="text" wire:model="settings.home_hero_cta_label" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600">Subtitle</label>
                            <textarea wire:model="settings.home_hero_subtitle" rows="3" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600">Button URL</label>
                            <input type="text" wire:model="settings.home_hero_cta_url" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                    </div>
                @else
                    <div class="rounded-lg border border-blue-100 bg-blue-50 p-3 text-xs text-blue-700">
                        This banner type uses only sliding banner images. The text content fields below are hidden because they are not shown on the storefront.
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Banner Slides</p>
                        <p class="text-xs text-gray-500">Add the images that will slide in the hero area.</p>
                    </div>
                    <button type="button" wire:click="addBannerSlide" class="text-xs font-semibold text-primary hover:text-primary">
                        + Add Slide
                    </button>
                </div>

                @error('bannerSlides') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                <div class="space-y-4">
                    @foreach($bannerSlides as $index => $slide)
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Slide {{ $index + 1 }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if(($settings['home_banner_type'] ?? 'split') === 'split')
                                            Recommended: 900x1100 px.
                                        @else
                                            Recommended: 1600x600 px.
                                        @endif
                                    </p>
                                </div>
                                <button type="button" wire:click="removeBannerSlide({{ $index }})" class="text-xs font-semibold text-red-600 hover:text-red-700">
                                    Remove
                                </button>
                            </div>

                            <div class="mt-4 grid grid-cols-1 lg:grid-cols-[180px_1fr] gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600">Image</label>
                                    <input type="file" accept="image/*" wire:model="bannerSlides.{{ $index }}.image_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @error("bannerSlides.$index.image_file") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                                    <div class="mt-3 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-2">
                                        @php
                                            $previewUrl = '';
                                            if (!empty($slide['image_file'])) {
                                                $previewUrl = $slide['image_file']->temporaryUrl();
                                            } elseif (!empty($slide['image'])) {
                                                $previewUrl = \Illuminate\Support\Facades\Storage::url($slide['image']);
                                            }
                                        @endphp
                                        @if($previewUrl)
                                            <img src="{{ $previewUrl }}" alt="Banner preview" class="h-32 w-full rounded object-cover">
                                        @else
                                            <div class="flex h-32 items-center justify-center text-xs text-gray-400">No image selected</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600">Link URL (optional)</label>
                                        <input type="text" wire:model="bannerSlides.{{ $index }}.link_url" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="https://... or /search">
                                        @error("bannerSlides.$index.link_url") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600">Alt Text (optional)</label>
                                        <input type="text" wire:model="bannerSlides.{{ $index }}.alt" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Describe the banner image">
                                        @error("bannerSlides.$index.alt") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-4">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Homepage Sections</p>
                    <p class="text-xs text-gray-500">Turn sections on or off and rename them for the storefront.</p>
                </div>

                <div class="space-y-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-3">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" wire:model="settings.home_shop_by_category_enabled" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                            <span>Show Shop by Category</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Controls the category carousel on the homepage.</p>
                        <input type="text" wire:model="settings.home_shop_by_category_title" class="mt-3 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Section title">
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-3">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" wire:model="settings.home_featured_products_enabled" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                            <span>Show Featured Products</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Controls the featured products row on the homepage.</p>
                        <input type="text" wire:model="settings.home_featured_products_title" class="mt-3 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Section title">
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-3">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" wire:model="settings.home_new_arrivals_enabled" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                            <span>Show New Arrivals</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Controls the newest products row on the homepage.</p>
                        <input type="text" wire:model="settings.home_new_arrivals_title" class="mt-3 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Section title">
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-3">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" wire:model="settings.home_newsletter_enabled" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                            <span>Show Newsletter Block</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Controls the subscribe block near the bottom of the homepage.</p>
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" wire:model="settings.home_newsletter_title" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Section title">
                            <input type="text" wire:model="settings.home_newsletter_subtitle" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Section subtitle">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-end gap-3">
            <button wire:click="save" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                Save Homepage Settings
            </button>
        </div>
    </section>
</div>
