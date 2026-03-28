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
                        <p class="text-sm font-semibold text-gray-800">Storefront Theme</p>
                        <p class="text-xs text-gray-500">Choose the visual layout customers see on the storefront. Brand colors still apply everywhere.</p>
                    </div>
                    <button type="button" wire:click="saveStorefrontTheme" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Save Theme
                    </button>
                </div>

                @php($storefrontThemes = \App\Support\StorefrontTheme::all())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($storefrontThemes as $key => $theme)
                        <label class="cursor-pointer rounded-xl border bg-white p-4 transition {{ ($settings['storefront_theme'] ?? 'default') === $key ? 'border-primary ring-1 ring-primary/20' : 'border-gray-200 hover:border-primary/40' }}">
                            <div class="flex items-start gap-3">
                                <input type="radio" wire:model="settings.storefront_theme" value="{{ $key }}" class="mt-1 h-4 w-4 border-gray-300 text-primary focus:ring-primary">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900">{{ $theme['label'] }}</span>
                                        @if(($settings['storefront_theme'] ?? 'default') === $key)
                                            <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary">Current</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">{{ $theme['description'] }}</p>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Hero / Banner</p>
                        <p class="text-xs text-gray-500">Choose how the storefront banner is presented.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="settings.home_hero_enabled" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                            <span>Show banner</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="settings.home_banner_autoplay_enabled" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                            <span>Auto slide banner images</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Banner Type</label>
                        <div class="relative mt-1">
                        <select wire:model.live="settings.home_banner_type" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-0 focus:border-gray-300">
                            <option value="split">Banner Type 1 - Split banner with slider on the left</option>
                            <option value="slider_only">Banner Type 2 - Full slider banner only</option>
                            <option value="text_only">Banner Type 3 - Text banner only</option>
                        </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Recommended Image Size</label>
                        <div class="mt-1 rounded-lg border border-dashed border-gray-300 bg-white px-3 py-2 text-xs text-gray-600">
                            @if(($settings['home_banner_type'] ?? 'split') === 'split')
                                Use wide rectangle images. Recommended: 1600x900 px.
                            @else
                                Use wide rectangle images. Recommended: 1600x700 px.
                            @endif
                        </div>
                    </div>
                </div>

                @if(in_array(($settings['home_banner_type'] ?? 'split'), ['split', 'text_only'], true))
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
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between gap-3">
                                <label class="block text-xs font-semibold text-gray-600">Banner Chips</label>
                                <div class="flex items-center gap-3">
                                    <button type="button" wire:click="addBannerChip" class="text-xs font-semibold text-primary hover:text-primary">+ Add Chip</button>
                                    <button type="button" wire:click="clearBannerChips" class="text-xs font-semibold text-red-600 hover:text-red-700">Clear All</button>
                                </div>
                            </div>
                            <p class="mt-1 text-[11px] text-gray-500">These show on Banner Type 3. Leave the list empty to hide them.</p>
                            <div class="mt-3 space-y-3">
                                @forelse($bannerChips as $index => $chip)
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1">
                                            <input type="text" wire:model="bannerChips.{{ $index }}.label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Chip label">
                                            @error("bannerChips.$index.label") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <button type="button" wire:click="removeBannerChip({{ $index }})" class="mt-1 inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-red-600 hover:bg-red-50" title="Remove chip">
                                            ×
                                        </button>
                                    </div>
                                @empty
                                    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-3 text-xs text-gray-500">
                                        No chips added yet.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="button" wire:click="saveHeroBanner" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                            Save Banner Settings
                        </button>
                    </div>
                @else
                    <div class="rounded-lg border border-blue-100 bg-blue-50 p-3 text-xs text-blue-700">
                        This banner type uses only sliding banner images. The text content fields below are hidden because they are not shown on the storefront.
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="button" wire:click="saveHeroBanner" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                            Save Banner Settings
                        </button>
                    </div>
                @endif
            </div>

            @if(($settings['home_banner_type'] ?? 'split') !== 'text_only')
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

                @if(($settings['home_banner_type'] ?? 'split') === 'text_only')
                    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-4 text-sm text-gray-600">
                        Text banner only mode does not use slide images.
                    </div>
                @endif

                <div class="space-y-4">
                    @foreach($bannerSlides as $index => $slide)
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Slide {{ $index + 1 }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if(($settings['home_banner_type'] ?? 'split') === 'split')
                                            Recommended: 1600x900 px.
                                        @else
                                            Recommended: 1600x700 px.
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
                <div class="flex items-center justify-end">
                    <button type="button" wire:click="saveBannerSlides" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Save Banner Slides
                    </button>
                </div>
            </div>
            @else
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <p class="text-sm font-semibold text-gray-800">Text Banner Only</p>
                <p class="mt-1 text-xs text-gray-500">This mode does not use banner images or slide controls.</p>
            </div>
            @endif

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
                <div class="flex items-center justify-end">
                    <button type="button" wire:click="saveHomepageSections" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Save Homepage Sections
                    </button>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-4 mt-4">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Footer</p>
                    <p class="text-xs text-gray-500">Edit the storefront footer content shown across the website.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-3 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600">About Title</label>
                            <input type="text" wire:model="settings.footer_about_title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600">About Description</label>
                            <textarea wire:model="settings.footer_about_description" rows="3" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Facebook URL</label>
                                <input type="text" wire:model="settings.footer_social_facebook_url" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">X URL</label>
                                <input type="text" wire:model="settings.footer_social_x_url" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Instagram URL</label>
                                <input type="text" wire:model="settings.footer_social_instagram_url" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-3 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Quick Links Title</label>
                                <input type="text" wire:model="settings.footer_links_title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Support Title</label>
                                <input type="text" wire:model="settings.footer_support_title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Link 1</label>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <input type="text" wire:model="settings.footer_link_1_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Label">
                                    <input type="text" wire:model="settings.footer_link_1_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="URL">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Link 2</label>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <input type="text" wire:model="settings.footer_link_2_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Label">
                                    <input type="text" wire:model="settings.footer_link_2_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="URL">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Link 3</label>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <input type="text" wire:model="settings.footer_link_3_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Label">
                                    <input type="text" wire:model="settings.footer_link_3_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="URL">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Link 4</label>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <input type="text" wire:model="settings.footer_link_4_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Label">
                                    <input type="text" wire:model="settings.footer_link_4_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="URL">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Support Email</label>
                                <input type="text" wire:model="settings.footer_support_email" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Support Phone</label>
                                <input type="text" wire:model="settings.footer_support_phone" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Hours 1</label>
                                <input type="text" wire:model="settings.footer_support_hours_1" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Hours 2</label>
                                <input type="text" wire:model="settings.footer_support_hours_2" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Policies Title</label>
                                <input type="text" wire:model="settings.footer_policy_title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Copyright Text</label>
                                <input type="text" wire:model="settings.footer_copyright_text" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="© {year} ShopHub. All rights reserved.">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Policy 1</label>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <input type="text" wire:model="settings.footer_policy_1_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Label">
                                    <input type="text" wire:model="settings.footer_policy_1_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="URL">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Policy 2</label>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <input type="text" wire:model="settings.footer_policy_2_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Label">
                                    <input type="text" wire:model="settings.footer_policy_2_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="URL">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Policy 3</label>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <input type="text" wire:model="settings.footer_policy_3_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Label">
                                    <input type="text" wire:model="settings.footer_policy_3_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="URL">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Policy 4</label>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <input type="text" wire:model="settings.footer_policy_4_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Label">
                                    <input type="text" wire:model="settings.footer_policy_4_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="URL">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end">
                    <button type="button" wire:click="saveFooter" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Save Footer
                    </button>
                </div>
            </div>
    </section>
</div>
