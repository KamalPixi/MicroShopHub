<div class="space-y-6">
    <section class="rounded-xl border border-primary ring-1 ring-primary bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-gray-800">{{ $pageLabel }}</h3>
                <p class="mt-1 text-xs text-gray-500">Edit the dedicated storefront page content.</p>
            </div>
            <button type="button" wire:click="save" wire:loading.attr="disabled" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                Save {{ $pageLabel }}
            </button>
        </div>

        @include('admin.includes.message')

        <div class="mt-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600">Title</label>
                <input type="text" wire:model="settings.{{ $titleKey }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                @error('settings.' . $titleKey) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600">Content</label>
                <textarea wire:model="settings.{{ $contentKey }}" rows="16" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                @error('settings.' . $contentKey) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>
</div>
