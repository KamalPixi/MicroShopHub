@extends('store.layouts.app')
@section('title', 'ShopHub - Home')

@section('content')

@php
    $homepageSettings = $homepageSettings ?? [];
@endphp

@if($homepageSettings['home_hero_enabled'] ?? true)
<section
    class="relative overflow-hidden rounded-3xl text-white mb-10 border border-white/10 shadow-[0_20px_50px_rgba(15,23,42,0.18)]"
    style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 60%, var(--color-primary) 100%);"
>
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute -top-20 -right-24 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>
        <div class="absolute -bottom-28 -left-16 h-80 w-80 rounded-full bg-accent blur-3xl opacity-20"></div>
        <div class="absolute left-1/2 top-1/2 h-40 w-40 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/10 blur-2xl"></div>
    </div>

    @if(($homepageSettings['home_banner_type'] ?? 'split') === 'split')
        <div
            x-data="{
                active: 0,
                slides: @js($homeBannerSlides),
                timer: null,
                init() {
                    if (this.slides.length > 1) {
                        this.timer = setInterval(() => {
                            this.active = (this.active + 1) % this.slides.length
                        }, 4500)
                    }
                },
                go(index) { this.active = index }
            }"
            x-init="init()"
            class="relative z-10 grid gap-6 px-6 py-12 md:grid-cols-[1fr_0.95fr] md:px-10 md:py-16"
        >
            <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-white/10 min-h-[24rem] md:min-h-[32rem] backdrop-blur-sm">
                <template x-for="(slide, index) in slides" :key="index">
                    <a
                        x-show="active === index"
                        :href="slide.link_url || '#'"
                        class="absolute inset-0 block"
                        x-cloak
                    >
                        <img :src="slide.image_url" :alt="slide.alt || 'Homepage banner'" class="h-full w-full object-cover">
                    </a>
                </template>

                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent pointer-events-none"></div>

                <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-3">
                    <div class="flex gap-2">
                        <template x-for="(_, index) in slides" :key="index">
                            <button
                                type="button"
                                @click="go(index)"
                                class="h-2.5 rounded-full transition-all duration-300"
                                :class="active === index ? 'w-8 bg-white' : 'w-2.5 bg-white/45'"
                                aria-label="Go to slide"
                            ></button>
                        </template>
                    </div>

                    <div class="flex gap-2" x-show="slides.length > 1" x-cloak>
                        <button type="button" @click="active = active === 0 ? slides.length - 1 : active - 1" class="rounded-full bg-white/15 px-3 py-2 text-xs font-semibold text-white hover:bg-white/25">
                            Prev
                        </button>
                        <button type="button" @click="active = (active + 1) % slides.length" class="rounded-full bg-white/15 px-3 py-2 text-xs font-semibold text-white hover:bg-white/25">
                            Next
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col justify-center">
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight tracking-tight">
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
        </div>
    @else
        <div
            x-data="{
                active: 0,
                slides: @js($homeBannerSlides),
                timer: null,
                init() {
                    if (this.slides.length > 1) {
                        this.timer = setInterval(() => {
                            this.active = (this.active + 1) % this.slides.length
                        }, 4500)
                    }
                },
                go(index) { this.active = index }
            }"
            x-init="init()"
            class="relative z-10 px-4 py-4 md:px-8 md:py-8"
        >
            <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-white/10 min-h-[18rem] md:min-h-[26rem] backdrop-blur-sm">
                <template x-for="(slide, index) in slides" :key="index">
                    <a
                        x-show="active === index"
                        :href="slide.link_url || '#'"
                        class="absolute inset-0 block"
                        x-cloak
                    >
                        <img :src="slide.image_url" :alt="slide.alt || 'Homepage banner'" class="h-full w-full object-cover">
                    </a>
                </template>

                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/25 via-transparent to-transparent pointer-events-none"></div>

                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                    <template x-for="(_, index) in slides" :key="index">
                        <button
                            type="button"
                            @click="go(index)"
                            class="h-2.5 rounded-full transition-all duration-300"
                            :class="active === index ? 'w-8 bg-white' : 'w-2.5 bg-white/45'"
                            aria-label="Go to slide"
                        ></button>
                    </template>
                </div>
            </div>
        </div>
    @endif
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
