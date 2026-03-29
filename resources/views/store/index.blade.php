@extends('store.layouts.app')
@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Home')

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

    @if(($homepageSettings['home_banner_type'] ?? 'split') === 'text_only')
        <div class="relative z-10 grid gap-3 px-6 py-6 md:grid-cols-[1.15fr_0.85fr] md:px-10 md:py-8">
            <div class="flex flex-col justify-center">
                <h1 class="text-2xl md:text-4xl font-extrabold leading-tight tracking-tight">
                    {{ $homepageSettings['home_hero_title'] ?? 'Find what fits your life' }}
                </h1>
                <p class="mt-2 max-w-3xl text-sm md:text-base leading-5 text-white/82">
                    {{ $homepageSettings['home_hero_subtitle'] ?? 'Curated products, fast delivery, and a storefront built for easy browsing.' }}
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <a href="{{ $homepageSettings['home_hero_cta_url'] ?? route('store.search') }}" class="inline-flex items-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-primary shadow-sm transition hover:opacity-95">
                        {{ $homepageSettings['home_hero_cta_label'] ?? __('store.shop_now') }}
                    </a>
                    <a href="{{ route('store.search') }}" class="inline-flex items-center rounded-xl border border-white/25 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15">
                        {{ __('store.browse_store') }}
                    </a>
                </div>

                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    @foreach(($homepageSettings['home_banner_chips'] ?? []) as $chip)
                        <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5">{{ $chip }}</span>
                    @endforeach
                </div>
            </div>

            <div class="self-center">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-3.5 backdrop-blur-sm">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-white/70">{{ __('store.quick_actions') }}</p>
                    <div class="mt-2.5 space-y-2">
                        <a href="{{ route('store.search') }}" class="group flex items-center justify-between rounded-xl bg-white/10 px-3 py-2.5 transition hover:bg-white/15">
                            <div>
                                <p class="text-sm font-semibold">{{ __('store.browse_categories') }}</p>
                                <p class="mt-0.5 text-[11px] text-white/75">{{ __('store.browse_categories_hint') }}</p>
                            </div>
                            <span class="ml-3 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white text-primary font-bold transition group-hover:translate-x-0.5">→</span>
                        </a>
                        <a href="{{ route('store.search', ['sort' => 'newest']) }}" class="group flex items-center justify-between rounded-xl bg-white/10 px-3 py-2.5 transition hover:bg-white/15">
                            <div>
                                <p class="text-sm font-semibold">{{ __('store.see_new_arrivals') }}</p>
                                <p class="mt-0.5 text-[11px] text-white/75">{{ __('store.see_new_arrivals_hint') }}</p>
                            </div>
                            <span class="ml-3 inline-flex h-7 w-7 items-center justify-center rounded-full bg-accent text-slate-900 font-bold transition group-hover:translate-x-0.5">→</span>
                        </a>
                        <a href="#newsletter" class="group flex items-center justify-between rounded-xl bg-white/10 px-3 py-2.5 transition hover:bg-white/15">
                            <div>
                                <p class="text-sm font-semibold">{{ __('store.stay_updated') }}</p>
                                <p class="mt-0.5 text-[11px] text-white/75">{{ __('store.stay_updated_hint') }}</p>
                            </div>
                            <span class="ml-3 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white text-secondary font-bold transition group-hover:translate-x-0.5">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif(($homepageSettings['home_banner_type'] ?? 'split') === 'split')
        <div
            class="relative z-10 grid gap-4 px-6 py-7 md:grid-cols-[3fr_1fr] md:px-10 md:py-9 js-home-banner-slider"
            data-autoplay="{{ ($homepageSettings['home_banner_autoplay_enabled'] ?? true) ? '1' : '0' }}"
        >
            <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-white/10 aspect-[24/8] md:aspect-[24/8] backdrop-blur-sm">
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

                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent pointer-events-none"></div>

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

            <div class="flex flex-col justify-center md:pl-2">
                <h1 class="text-2xl md:text-[1.7rem] font-extrabold leading-tight tracking-tight">
                    {{ $homepageSettings['home_hero_title'] ?? 'Find what fits your life' }}
                </h1>
                <p class="mt-2 max-w-2xl text-sm md:text-[0.85rem] leading-5 text-white/82">
                    {{ $homepageSettings['home_hero_subtitle'] ?? 'Curated products, fast delivery, and a storefront built for easy browsing.' }}
                </p>

                <div class="mt-3.5 flex flex-wrap items-center gap-3">
                    <a href="{{ $homepageSettings['home_hero_cta_url'] ?? route('store.search') }}" class="inline-flex items-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-primary shadow-sm transition hover:opacity-95">
                        {{ $homepageSettings['home_hero_cta_label'] ?? __('store.shop_now') }}
                    </a>
                    <a href="{{ route('store.search') }}" class="inline-flex items-center rounded-xl border border-white/25 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15">
                        {{ __('store.browse_store') }}
                    </a>
                </div>

                @if(!empty($homepageSettings['home_banner_chips'] ?? []))
                    <div class="mt-2.5 flex flex-wrap gap-2 text-xs">
                        @foreach($homepageSettings['home_banner_chips'] as $chip)
                            <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5">{{ $chip }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @else
        <div
            class="relative z-10 px-4 py-3 md:px-8 md:py-5 js-home-banner-slider"
            data-autoplay="{{ ($homepageSettings['home_banner_autoplay_enabled'] ?? true) ? '1' : '0' }}"
        >
            <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-white/10 aspect-[32/8] backdrop-blur-sm">
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

                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
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
        </div>
    @endif
</section>
@endif

@if(!empty($activeFlashSale) && !empty($flashSaleMap))
    @include('store.partials.flash-sale', ['flashSale' => $activeFlashSale, 'flashSaleMap' => $flashSaleMap])
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
<section id="newsletter" class="bg-primary rounded-lg p-6 text-center text-white mb-8">
    <h2 class="text-2xl font-bold mb-2">{{ $homepageSettings['home_newsletter_title'] ?? __('store.stay_updated') }}</h2>
    <p class="text-white/80 mb-4">{{ $homepageSettings['home_newsletter_subtitle'] ?? __('store.stay_updated_hint') }}</p>
    <livewire:store.newsletter-subscribe />
</section>
@endif

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
