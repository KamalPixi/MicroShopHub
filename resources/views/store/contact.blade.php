@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Contact Us')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary">Support</p>
            <h1 class="mt-2 text-3xl font-extrabold text-gray-900">Contact us</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-600">Questions about products, delivery, payment, or orders? Send us a message and we’ll respond as soon as possible.</p>
        </div>

        <livewire:store.contact-form />
    </div>
@endsection
