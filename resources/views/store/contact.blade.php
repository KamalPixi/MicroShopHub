@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Contact Us')

@section('content')
    <div class="space-y-4 py-4 md:py-6 pb-6 md:pb-8">
        <div class="grid gap-4 lg:grid-cols-[0.85fr_1.15fr]">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary">Support</p>
                <h1 class="mt-2 text-3xl font-extrabold text-gray-900">Contact us</h1>
                <p class="mt-3 text-sm leading-6 text-gray-600">Questions about products, delivery, payment, or orders? Send us a message and we’ll respond as soon as possible.</p>

                <div class="mt-6 space-y-3 text-sm">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Shop Email</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $supportEmail ?? 'support@shophub.com' }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Phone Number</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $supportPhone ?? '+1 (555) 123-4567' }}</p>
                    </div>
                </div>
            </div>

            <livewire:store.contact-form />
        </div>
    </div>
@endsection
