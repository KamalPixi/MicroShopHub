<div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary">Contact us</p>
        <h2 class="mt-2 text-3xl font-extrabold text-gray-900">We’re here to help</h2>
        <p class="mt-3 text-sm leading-6 text-gray-600">Send a message about orders, products, delivery, or anything else. We read every message.</p>

        <div class="mt-6 space-y-4 text-sm text-gray-600">
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Email</p>
                <p class="mt-1 font-medium text-gray-900">{{ $supportEmail ?? 'support@shophub.com' }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Phone</p>
                <p class="mt-1 font-medium text-gray-900">{{ $supportPhone ?? '+1 (555) 123-4567' }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
        <form wire:submit.prevent="submit" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Name</label>
                    <input type="text" wire:model.live="name" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="Your full name">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" wire:model.live="email" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="you@example.com">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Phone</label>
                    <input type="text" wire:model.live="phone" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="Optional">
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Subject</label>
                    <input type="text" wire:model.live="subject" class="mt-1 w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="How can we help?">
                    @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Message</label>
                <textarea wire:model.live="message" rows="7" class="mt-1 w-full rounded-2xl border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-0 focus:border-primary" placeholder="Write your message here..."></textarea>
                @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            @if($successMessage)
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ $successMessage }}</div>
            @endif

            <div class="flex items-center justify-end">
                <button type="submit" class="inline-flex items-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:opacity-95">
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>
