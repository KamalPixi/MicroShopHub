<div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary">{{ __('store.contact_form') }}</p>
            <h2 class="mt-2 text-3xl font-extrabold text-gray-900">{{ __('store.we_re_here_to_help') }}</h2>
            <p class="mt-3 text-sm leading-6 text-gray-600">{{ __('store.support_intro') }}</p>
        </div>
    </div>

    <form wire:submit.prevent="submit" class="mt-6 space-y-4">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-semibold text-gray-700">{{ __('store.name') }} <span class="text-red-500">*</span></label>
                <input type="text" wire:model.live="name" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="{{ __('store.full_name') }}">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">{{ __('store.email') }} <span class="text-red-500">*</span></label>
                <input type="email" wire:model.live="email" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="you@example.com">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-semibold text-gray-700">{{ __('store.phone') }}</label>
                <input type="text" wire:model.live="phone" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="{{ __('store.optional') }}">
                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">{{ __('store.subject') }} <span class="text-red-500">*</span></label>
                <input type="text" wire:model.live="subject" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="{{ __('store.how_can_we_help') }}">
                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('store.message') }} <span class="text-red-500">*</span></label>
            <textarea wire:model.live="message" rows="7" class="mt-1 w-full rounded-2xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="{{ __('store.write_message') }}"></textarea>
            @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        @if($successMessage)
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ $successMessage }}</div>
        @endif

        <div class="flex items-center justify-end">
            <button type="submit" class="inline-flex items-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:opacity-95">
                {{ __('store.send_message') }}
            </button>
        </div>
    </form>
</div>
