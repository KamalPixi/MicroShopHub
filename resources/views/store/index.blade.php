@extends('store.layouts.app')
@section('title', 'ShopHub - Home')

@section('content')

@php
    $homepageSettings = $homepageSettings ?? [];
@endphp

@if($homepageSettings['home_hero_enabled'] ?? true)
<section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary via-primary to-slate-900 text-white mb-8">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>
        <div class="absolute -bottom-28 -left-16 h-80 w-80 rounded-full bg-accent blur-3xl opacity-20"></div>
    </div>
    <div class="relative z-10 px-6 py-10 md:px-10 md:py-14 max-w-4xl">
        <p class="text-sm uppercase tracking-[0.2em] text-white/75 mb-3">Storefront Overview</p>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight">
            {{ $homepageSettings['home_hero_title'] ?? 'Find what fits your life' }}
        </h1>
        <p class="mt-4 max-w-2xl text-sm md:text-base text-white/80">
            {{ $homepageSettings['home_hero_subtitle'] ?? 'Curated products, fast delivery, and a storefront built for easy browsing.' }}
        </p>
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <a href="{{ $homepageSettings['home_hero_cta_url'] ?? route('store.search') }}" class="inline-flex items-center rounded-lg bg-white px-5 py-3 text-sm font-semibold text-primary hover:bg-white/95 transition">
                {{ $homepageSettings['home_hero_cta_label'] ?? 'Shop Now' }}
            </a>
            <a href="{{ route('store.search') }}" class="inline-flex items-center rounded-lg border border-white/25 bg-white/10 px-5 py-3 text-sm font-semibold text-white hover:bg-white/15 transition">
                Browse Store
            </a>
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
