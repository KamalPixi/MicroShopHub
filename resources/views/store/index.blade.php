@extends('store.layouts.app')
@section('title', 'ShopHub - Home')

@section('content')

@php
    $homepageSettings = $homepageSettings ?? [];
@endphp

@if($homepageSettings['home_hero_enabled'] ?? true)
<section
    class="relative overflow-hidden rounded-3xl text-white mb-2 border border-white/10 shadow-[0_20px_50px_rgba(15,23,42,0.18)]"
    style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 60%, var(--color-primary) 100%);"
>
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute -top-20 -right-24 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>
        <div class="absolute -bottom-28 -left-16 h-80 w-80 rounded-full bg-accent blur-3xl opacity-20"></div>
        <div class="absolute left-1/2 top-1/2 h-40 w-40 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/10 blur-2xl"></div>
    </div>
    <div class="relative z-10 grid gap-8 px-6 py-12 md:grid-cols-[1.35fr_0.9fr] md:px-10 md:py-16">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/85">
                <span class="h-2 w-2 rounded-full bg-accent"></span>
                Storefront Overview
            </div>
            <h1 class="mt-4 text-3xl md:text-5xl font-extrabold leading-tight tracking-tight">
                {{ $homepageSettings['home_hero_title'] ?? 'Find what fits your life' }}
            </h1>
            <p class="mt-4 max-w-2xl text-sm md:text-base leading-7 text-white/82">
                {{ $homepageSettings['home_hero_subtitle'] ?? 'Curated products, fast delivery, and a storefront built for easy browsing.' }}
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ $homepageSettings['home_hero_cta_url'] ?? route('store.search') }}" class="inline-flex items-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-primary shadow-sm transition hover:opacity-95">
                    {{ $homepageSettings['home_hero_cta_label'] ?? 'Shop Now' }}
                </a>
                <a href="{{ route('store.search') }}" class="inline-flex items-center rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                    Browse Store
                </a>
            </div>

            <div class="mt-6 flex flex-wrap gap-2 text-xs">
                <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5">Primary brand color</span>
                <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5">Fast checkout</span>
                <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5">Live support</span>
            </div>
        </div>

        <div class="grid gap-3 self-center">
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                <p class="text-[11px] uppercase tracking-[0.18em] text-white/70">Shop smarter</p>
                <div class="mt-3 grid gap-3">
                    <div class="flex items-start gap-3 rounded-xl bg-white/10 p-3">
                        <div class="mt-0.5 h-9 w-9 rounded-lg bg-white text-primary flex items-center justify-center font-bold">01</div>
                        <div>
                            <p class="text-sm font-semibold">Browse by category</p>
                            <p class="text-xs text-white/75">Clear sections help customers find products faster.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl bg-white/10 p-3">
                        <div class="mt-0.5 h-9 w-9 rounded-lg bg-accent text-slate-900 flex items-center justify-center font-bold">02</div>
                        <div>
                            <p class="text-sm font-semibold">Pick featured items</p>
                            <p class="text-xs text-white/75">Highlight what matters most on the homepage.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl bg-white/10 p-3">
                        <div class="mt-0.5 h-9 w-9 rounded-lg bg-white text-secondary flex items-center justify-center font-bold">03</div>
                        <div>
                            <p class="text-sm font-semibold">Keep it on-brand</p>
                            <p class="text-xs text-white/75">All colors follow the admin brand settings.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Shop by Category -->
@if($homepageSettings['home_shop_by_category_enabled'] ?? true)
    @include('store.partials.category-home', ['homepageSettings' => $homepageSettings, 'homeCategories' => $homeCategories])
@endif
@if($homepageSettings['home_featured_products_enabled'] ?? true)
    @include('store.partials.featured-products', ['homepageSettings' => $homepageSettings, 'featuredProducts' => $featuredProducts])
@endif
@if($homepageSettings['home_new_arrivals_enabled'] ?? true)
    @include('store.partials.new-arrivals', ['homepageSettings' => $homepageSettings, 'newArrivals' => $newArrivals])
@endif

<!-- Newsletter -->
@if($homepageSettings['home_newsletter_enabled'] ?? true)
<section class="bg-primary rounded-lg p-6 text-center text-white mb-8">
    <h2 class="text-2xl font-bold mb-2">{{ $homepageSettings['home_newsletter_title'] ?? 'Stay Updated' }}</h2>
    <p class="text-white/80 mb-4">{{ $homepageSettings['home_newsletter_subtitle'] ?? 'Subscribe for new arrivals, exclusive offers, and restock alerts.' }}</p>
    <livewire:store.newsletter-subscribe />
</section>
@endif

@endsection
