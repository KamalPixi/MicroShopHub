<div>
    <form wire:submit.prevent="subscribe" class="max-w-md mx-auto flex flex-col sm:flex-row gap-3">
        <input type="email" wire:model.live="email" required placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none">
        <button type="submit" class="bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">Subscribe</button>
    </form>
    @error('email')
        <p class="mt-2 text-sm text-white/90">{{ $message }}</p>
    @enderror
    @if($successMessage)
        <p class="mt-3 text-sm text-white/90">{{ $successMessage }}</p>
    @endif
    <p class="mt-2 text-xs text-white/70">No spam. Unsubscribe anytime.</p>
</div>
