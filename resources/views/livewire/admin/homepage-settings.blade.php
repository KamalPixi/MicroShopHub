<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Homepage Settings</h3>
                <p class="mt-1 text-xs text-gray-500">Control how the storefront homepage looks and which sections appear.</p>
            </div>
        </div>

        @include('admin.includes.message')

        <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-4">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Hero Banner</p>
                    <p class="text-xs text-gray-500">This is the first section customers see on the storefront home page.</p>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="settings.home_hero_enabled" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span>Show hero banner</span>
                </label>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Title</label>
                    <input type="text" wire:model="settings.home_hero_title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600">Subtitle</label>
                    <textarea wire:model="settings.home_hero_subtitle" rows="3" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Button Label</label>
                        <input type="text" wire:model="settings.home_hero_cta_label" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Button URL</label>
                        <input type="text" wire:model="settings.home_hero_cta_url" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
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
