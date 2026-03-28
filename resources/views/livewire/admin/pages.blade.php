<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Pages</h3>
                <p class="mt-1 text-xs text-gray-500">Manage the legal pages shown in the footer and storefront.</p>
            </div>
        </div>

        @include('admin.includes.message')

        <div class="mt-5 space-y-5">
            <div id="privacy" class="rounded-xl border border-primary ring-1 ring-primary p-4 space-y-4 bg-gray-50">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Privacy Policy</p>
                        <p class="text-xs text-gray-500">Shown on the storefront privacy policy page and footer link.</p>
                    </div>
                    <button type="button" wire:click="savePrivacy" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Save Privacy
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Title</label>
                        <input type="text" wire:model="settings.page_privacy_title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @error('settings.page_privacy_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Content</label>
                        <textarea wire:model="settings.page_privacy_content" rows="12" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                        @error('settings.page_privacy_content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div id="terms" class="rounded-xl border border-primary ring-1 ring-primary p-4 space-y-4 bg-gray-50">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Terms of Service</p>
                        <p class="text-xs text-gray-500">Shown on the storefront terms page and footer link.</p>
                    </div>
                    <button type="button" wire:click="saveTerms" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Save Terms
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Title</label>
                        <input type="text" wire:model="settings.page_terms_title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @error('settings.page_terms_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Content</label>
                        <textarea wire:model="settings.page_terms_content" rows="12" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                        @error('settings.page_terms_content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div id="cookie" class="rounded-xl border border-primary ring-1 ring-primary p-4 space-y-4 bg-gray-50">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Cookie Policy</p>
                        <p class="text-xs text-gray-500">Shown on the storefront cookie policy page and footer link.</p>
                    </div>
                    <button type="button" wire:click="saveCookie" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Save Cookie Policy
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Title</label>
                        <input type="text" wire:model="settings.page_cookie_title" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @error('settings.page_cookie_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Content</label>
                        <textarea wire:model="settings.page_cookie_content" rows="12" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                        @error('settings.page_cookie_content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
