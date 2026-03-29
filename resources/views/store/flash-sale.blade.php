@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Flash Sale')

@section('content')
@php
    $saleProducts = $activeFlashSale->products ?? collect();
    $saleEndsAt = $activeFlashSale->ends_at?->toIso8601String();
@endphp

<div class="pb-8">
    @if($activeFlashSale && $saleProducts->count() > 0)
        <section class="mb-8 rounded-3xl border border-primary/15 bg-gradient-to-br from-primary/10 via-white to-primary/5 p-5 md:p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-white/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-primary/90">
                        <svg class="h-3.5 w-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13.5 2.25 4.5 14.25H11l-1.5 7.5 9-12H12l1.5-7.5Z"></path>
                        </svg>
                        <span>Flash Sale</span>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                        <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">
                            {{ $activeFlashSale->title }}
                        </h1>
                        @if($saleEndsAt)
                            <div class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-white px-3 py-1.5 shadow-sm">
                                <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">Ends in</span>
                                <span class="text-sm font-extrabold text-primary" data-flash-sale-countdown="{{ $saleEndsAt }}">Loading…</span>
                            </div>
                        @endif
                    </div>
                    @if($activeFlashSale->subtitle)
                        <p class="mt-1 text-sm text-gray-600">{{ $activeFlashSale->subtitle }}</p>
                    @endif
                    @if($activeFlashSale->description)
                        <p class="mt-2 max-w-3xl text-sm text-gray-500">{{ $activeFlashSale->description }}</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="mb-8">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">All Flash Sale Items</h2>
                    <p class="text-sm text-gray-500">{{ $saleProducts->count() }} products on sale right now</p>
                </div>
                <a href="{{ route('store.index') }}" class="inline-flex items-center rounded-full border border-primary/20 bg-white px-4 py-2 text-sm font-semibold text-primary shadow-sm transition hover:bg-primary hover:text-white">
                    Back Home
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($saleProducts as $product)
                    @php $saleInfo = $flashSaleMap[$product->id] ?? null; @endphp
                    @continue(empty($saleInfo))
                    <div class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer"
                         role="link"
                         tabindex="0"
                         onclick="if (!event.target.closest('button, a, [wire\\:click]')) window.location='{{ route('store.product.show', $product->slug) }}'"
                         onkeydown="if ((event.key === 'Enter' || event.key === ' ') && !event.target.closest('button, a, [wire\\:click]')) { event.preventDefault(); window.location='{{ route('store.product.show', $product->slug) }}'; }">
                        <div class="relative aspect-square overflow-hidden bg-gray-100">
                            @php
                                $imageUrl = 'https://placehold.co/500x500?text=No+Image';
                                if ($product->thumbnail) {
                                    $imageUrl = \Illuminate\Support\Str::startsWith($product->thumbnail, ['http://', 'https://'])
                                        ? $product->thumbnail
                                        : \Illuminate\Support\Facades\Storage::url($product->thumbnail);
                                }
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute left-3 top-3 rounded-full bg-rose-500 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white shadow-sm">
                                Flash Sale
                            </div>
                        </div>

                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-900 transition group-hover:text-primary leading-snug line-clamp-2">
                                {{ $product->name }}
                            </h3>
                            <div class="mt-2 flex items-end justify-between gap-2">
                                <div class="flex flex-col">
                                    @if($product->has_variations && empty($product->price))
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-gray-500">From</span>
                                    @endif
                                    <span class="text-sm font-semibold text-gray-400 line-through">
                                        {{ $product->currency_symbol }}{{ number_format($saleInfo['original_price'], 2) }}
                                    </span>
                                    <span class="text-lg font-extrabold text-primary">
                                        {{ $product->currency_symbol }}{{ number_format($saleInfo['sale_price'], 2) }}
                                    </span>
                                </div>
                                @livewire('store.add-to-cart-button', ['productId' => $product->id], key('flash-sale-page-'.$product->id))
                            </div>
                            <p class="mt-1 text-xs font-semibold text-green-700">
                                Save {{ $saleInfo['discount_percent'] }}%
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @else
        <section class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center shadow-sm">
            <div class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-white/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-primary/90">
                <svg class="h-3.5 w-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13.5 2.25 4.5 14.25H11l-1.5 7.5 9-12H12l1.5-7.5Z"></path>
                </svg>
                <span>Flash Sale</span>
            </div>
            <h1 class="mt-2 text-2xl font-extrabold text-gray-900">No active flash sale right now</h1>
            <p class="mx-auto mt-2 max-w-xl text-sm text-gray-600">
                Check back later for limited-time deals and special product offers.
            </p>
            <a href="{{ route('store.index') }}" class="mt-5 inline-flex items-center rounded-full bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                Back to Home
            </a>
        </section>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-flash-sale-countdown]').forEach((node) => {
            const endsAt = new Date(node.dataset.flashSaleCountdown);
            const pad = (value) => String(value).padStart(2, '0');

            const tick = () => {
                const diff = endsAt.getTime() - Date.now();

                if (diff <= 0) {
                    node.textContent = 'Ended';
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                node.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            };

            tick();
            window.setInterval(tick, 1000);
        });
    });
</script>
@endsection
