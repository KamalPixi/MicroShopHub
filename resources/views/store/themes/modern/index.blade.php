@extends('store.layouts.app')
@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Home')

@section('content')

@php
    $homepageSettings = $homepageSettings ?? [];
@endphp

<div class="space-y-8">
@if($homepageSettings['home_hero_enabled'] ?? true)
<section class="rounded-[2rem] border border-gray-200 bg-white shadow-sm overflow-hidden">
    @if(($homepageSettings['home_banner_type'] ?? 'split') === 'text_only')
        <div class="grid gap-6 px-6 py-6 lg:grid-cols-[1.25fr_0.75fr] lg:px-10 lg:py-8">
            <div class="flex flex-col justify-center">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary/80">Modern storefront</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 lg:text-5xl">
                    {{ $homepageSettings['home_hero_title'] ?? 'Find what fits your life' }}
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-gray-600 lg:text-base">
                    {{ $homepageSettings['home_hero_subtitle'] ?? 'Curated products, fast delivery, and a storefront built for easy browsing.' }}
                </p>

                @if(!empty($homepageSettings['home_banner_chips'] ?? []))
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($homepageSettings['home_banner_chips'] as $chip)
                            <span class="rounded-full border border-primary/15 bg-primary/5 px-3 py-1.5 text-xs font-semibold text-primary">{{ $chip }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <a href="{{ $homepageSettings['home_hero_cta_url'] ?? route('store.search') }}" class="inline-flex items-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                        {{ $homepageSettings['home_hero_cta_label'] ?? 'Shop Now' }}
                    </a>
                    <a href="{{ route('store.search') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-primary/20 hover:text-primary">
                        Browse Store
                    </a>
                </div>
            </div>

            <div class="grid gap-3 self-center">
                <a href="{{ route('store.search') }}" class="rounded-2xl border border-gray-200 bg-gray-50 p-4 transition hover:border-primary/25 hover:bg-primary/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Quick action</p>
                    <p class="mt-2 text-lg font-bold text-gray-900">Browse categories</p>
                    <p class="mt-1 text-sm text-gray-600">Jump into the catalog and explore by category.</p>
                </a>
                <a href="{{ route('store.search', ['sort' => 'newest']) }}" class="rounded-2xl border border-gray-200 bg-gray-50 p-4 transition hover:border-primary/25 hover:bg-primary/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Quick action</p>
                    <p class="mt-2 text-lg font-bold text-gray-900">See new arrivals</p>
                    <p class="mt-1 text-sm text-gray-600">View the newest items added to the store.</p>
                </a>
                <a href="#newsletter" class="rounded-2xl border border-gray-200 bg-gray-50 p-4 transition hover:border-primary/25 hover:bg-primary/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Quick action</p>
                    <p class="mt-2 text-lg font-bold text-gray-900">Stay updated</p>
                    <p class="mt-1 text-sm text-gray-600">Go straight to the subscribe section for offers and updates.</p>
                </a>
            </div>
        </div>
    @elseif(($homepageSettings['home_banner_type'] ?? 'split') === 'split')
        <div class="grid gap-6 px-6 py-6 lg:grid-cols-[1.35fr_0.65fr] lg:px-10 lg:py-8">
            <div class="space-y-4">
                <div class="relative overflow-hidden rounded-[1.75rem] border border-gray-200 bg-gray-50 aspect-[24/8]">
                    <div class="absolute inset-0 js-home-banner-slider" data-autoplay="{{ ($homepageSettings['home_banner_autoplay_enabled'] ?? true) ? '1' : '0' }}">
                        @foreach($homeBannerSlides as $index => $slide)
                            <a
                                href="{{ $slide['link_url'] ?: '#' }}"
                                data-home-banner-slide
                                data-slide-index="{{ $index }}"
                                class="absolute inset-0 block transition-opacity duration-700 ease-out {{ $index === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}"
                            >
                                <img src="{{ $slide['image_url'] }}" alt="{{ $slide['alt'] ?? 'Homepage banner' }}" class="h-full w-full object-cover">
                            </a>
                        @endforeach

                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/25 via-transparent to-transparent pointer-events-none"></div>

                        <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-3">
                            <div class="flex gap-2">
                                @foreach($homeBannerSlides as $index => $slide)
                                    <button
                                        type="button"
                                        data-home-banner-dot
                                        data-slide-index="{{ $index }}"
                                        class="h-2.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 bg-white' : 'w-2.5 bg-white/45' }}"
                                        aria-label="Go to slide {{ $index + 1 }}"
                                    ></button>
                                @endforeach
                            </div>

                            @if(count($homeBannerSlides) > 1)
                                <div class="flex gap-2">
                                    <button type="button" data-home-banner-prev class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25" aria-label="Previous slide">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" data-home-banner-next class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25" aria-label="Next slide">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if(!empty($homepageSettings['home_banner_chips'] ?? []))
                    <div class="flex flex-wrap gap-2">
                        @foreach($homepageSettings['home_banner_chips'] as $chip)
                            <span class="rounded-full border border-primary/15 bg-primary/5 px-3 py-1.5 text-xs font-semibold text-primary">{{ $chip }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex flex-col justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary/80">Modern storefront</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 lg:text-[2.6rem]">
                        {{ $homepageSettings['home_hero_title'] ?? 'Find what fits your life' }}
                    </h1>
                    <p class="mt-3 text-sm leading-6 text-gray-600 lg:text-base">
                        {{ $homepageSettings['home_hero_subtitle'] ?? 'Curated products, fast delivery, and a storefront built for easy browsing.' }}
                    </p>
                </div>

                <div class="grid gap-3">
                    <a href="{{ $homepageSettings['home_hero_cta_url'] ?? route('store.search') }}" class="rounded-2xl bg-primary px-5 py-3 text-center text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                        {{ $homepageSettings['home_hero_cta_label'] ?? 'Shop Now' }}
                    </a>
                    <a href="{{ route('store.search') }}" class="rounded-2xl border border-gray-200 bg-white px-5 py-3 text-center text-sm font-semibold text-gray-700 transition hover:border-primary/20 hover:text-primary">
                        Browse Store
                    </a>
                </div>

                <div class="grid gap-3">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-900">Fast checkout</p>
                        <p class="mt-1 text-sm text-gray-600">Keep the flow simple from product selection to payment.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-900">Live support</p>
                        <p class="mt-1 text-sm text-gray-600">Chat with the store team while you browse and compare products.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-5 px-6 py-6 lg:grid-cols-[1.3fr_0.7fr] lg:px-10 lg:py-8">
            <div class="relative overflow-hidden rounded-[1.75rem] border border-gray-200 bg-gray-50 aspect-[36/8] js-home-banner-slider" data-autoplay="{{ ($homepageSettings['home_banner_autoplay_enabled'] ?? true) ? '1' : '0' }}">
                @foreach($homeBannerSlides as $index => $slide)
                    <a
                        href="{{ $slide['link_url'] ?: '#' }}"
                        data-home-banner-slide
                        data-slide-index="{{ $index }}"
                        class="absolute inset-0 block transition-opacity duration-700 ease-out {{ $index === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}"
                    >
                        <img src="{{ $slide['image_url'] }}" alt="{{ $slide['alt'] ?? 'Homepage banner' }}" class="h-full w-full object-cover">
                    </a>
                @endforeach
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/25 via-transparent to-transparent pointer-events-none"></div>
                <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
                    @foreach($homeBannerSlides as $index => $slide)
                        <button
                            type="button"
                            data-home-banner-dot
                            data-slide-index="{{ $index }}"
                            class="h-2.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 bg-white' : 'w-2.5 bg-white/45' }}"
                            aria-label="Go to slide {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col justify-center rounded-[1.75rem] border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary/80">Modern storefront</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">
                    {{ $homepageSettings['home_hero_title'] ?? 'Find what fits your life' }}
                </h1>
                <p class="mt-3 text-sm leading-6 text-gray-600">
                    {{ $homepageSettings['home_hero_subtitle'] ?? 'Curated products, fast delivery, and a storefront built for easy browsing.' }}
                </p>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ $homepageSettings['home_hero_cta_url'] ?? route('store.search') }}" class="inline-flex items-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                        {{ $homepageSettings['home_hero_cta_label'] ?? 'Shop Now' }}
                    </a>
                    <a href="{{ route('store.search') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-primary/20 hover:text-primary">
                        Browse Store
                    </a>
                </div>

                @if(!empty($homepageSettings['home_banner_chips'] ?? []))
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($homepageSettings['home_banner_chips'] as $chip)
                            <span class="rounded-full border border-primary/15 bg-primary/5 px-3 py-1.5 text-xs font-semibold text-primary">{{ $chip }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-900">Fresh listings</p>
                        <p class="mt-1 text-sm text-gray-600">Keep the homepage moving with new products and curated highlights.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-900">Brand colors</p>
                        <p class="mt-1 text-sm text-gray-600">Every theme still respects your configured primary color.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
@endif

@if($homepageSettings['home_shop_by_category_enabled'] ?? true)
    @include('store.partials.category-home', ['homepageSettings' => $homepageSettings, 'homeCategories' => $homeCategories])
@endif
@if($homepageSettings['home_featured_products_enabled'] ?? true)
    @include('store.partials.featured-products', ['homepageSettings' => $homepageSettings, 'featuredProducts' => $featuredProducts])
@endif
@if($homepageSettings['home_new_arrivals_enabled'] ?? true)
    @include('store.partials.new-arrivals', ['homepageSettings' => $homepageSettings, 'newArrivals' => $newArrivals])
@endif

@if($homepageSettings['home_newsletter_enabled'] ?? true)
<section id="newsletter" class="rounded-3xl bg-primary p-6 text-center text-white shadow-sm md:p-8">
    <h2 class="text-2xl font-bold mb-2">{{ $homepageSettings['home_newsletter_title'] ?? 'Stay Updated' }}</h2>
    <p class="text-white/80 mb-4">{{ $homepageSettings['home_newsletter_subtitle'] ?? 'Subscribe for new arrivals, exclusive offers, and restock alerts.' }}</p>
    <livewire:store.newsletter-subscribe />
</section>
@endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.js-home-banner-slider').forEach((slider) => {
            const slides = Array.from(slider.querySelectorAll('[data-home-banner-slide]'));
            const dots = Array.from(slider.querySelectorAll('[data-home-banner-dot]'));
            const prev = slider.querySelector('[data-home-banner-prev]');
            const next = slider.querySelector('[data-home-banner-next]');
            const autoplay = slider.dataset.autoplay === '1';

            if (!slides.length) {
                return;
            }

            let active = 0;
            let timer = null;

            const showSlide = (index) => {
                active = (index + slides.length) % slides.length;
                slides.forEach((slide, slideIndex) => {
                    const visible = slideIndex === active;
                    slide.classList.toggle('opacity-100', visible);
                    slide.classList.toggle('opacity-0', !visible);
                    slide.classList.toggle('pointer-events-none', !visible);
                });
                dots.forEach((dot, dotIndex) => {
                    const visible = dotIndex === active;
                    dot.classList.toggle('w-8', visible);
                    dot.classList.toggle('w-2.5', !visible);
                    dot.classList.toggle('bg-white', visible);
                    dot.classList.toggle('bg-white/45', !visible);
                });
            };

            const nextSlide = () => showSlide(active + 1);
            const prevSlide = () => showSlide(active - 1);

            dots.forEach((dot, dotIndex) => {
                dot.addEventListener('click', () => showSlide(dotIndex));
            });

            if (next) {
                next.addEventListener('click', nextSlide);
            }

            if (prev) {
                prev.addEventListener('click', prevSlide);
            }

            if (autoplay && slides.length > 1) {
                timer = window.setInterval(nextSlide, 3800);

                slider.addEventListener('mouseenter', () => {
                    if (timer) {
                        window.clearInterval(timer);
                        timer = null;
                    }
                });

                slider.addEventListener('mouseleave', () => {
                    if (!timer && slides.length > 1) {
                        timer = window.setInterval(nextSlide, 3800);
                    }
                });
            }

            showSlide(0);
        });
    });
</script>

@endsection
