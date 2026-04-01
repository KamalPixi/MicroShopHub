<div>
    <form wire:submit.prevent="subscribe" class="max-w-md mx-auto flex flex-col sm:flex-row gap-3">
        <input type="email" wire:model.live="email" required placeholder="{{ __('store.enter_your_email') }}" class="flex-1 rounded-lg border border-white/20 bg-white px-4 py-3 text-gray-900 placeholder:text-gray-500 shadow-sm focus:border-white focus:outline-none focus:ring-2 focus:ring-white/25">
        <button type="submit" class="rounded-lg bg-white px-6 py-3 font-semibold text-primary shadow-sm transition hover:bg-gray-100">{{ __('store.subscribe') }}</button>
    </form>
    @error('email')
        <p class="mt-2 text-sm text-white/90">{{ $message }}</p>
    @enderror
    @if($errorMessage)
        <p class="mt-2 text-sm text-white/90">{{ $errorMessage }}</p>
    @endif
    @if($successMessage)
        <p class="mt-3 text-sm text-white/90">{{ $successMessage }}</p>
    @endif
    <p class="mt-2 text-xs text-white/70">{{ __('store.no_spam') }}</p>
</div>
