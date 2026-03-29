@php
    $saleProducts = $flashSale->products ?? collect();
    $saleEndsAt = $flashSale->ends_at?->toIso8601String();
@endphp

@if($flashSale && $saleProducts->count() > 0)
<section class="mb-8 rounded-3xl border border-primary/15 bg-gradient-to-br from-primary/10 via-white to-primary/5 p-5 md:p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="space-y-1">
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-primary/80">Flash Sale</p>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">
                    {{ $flashSale->title }}
                </h2>
                @if($saleEndsAt)
                    <div class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-white px-3 py-1.5 shadow-sm">
                        <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-500">Ends in</span>
                        <span class="text-sm font-extrabold text-primary" data-flash-sale-countdown="{{ $saleEndsAt }}">Loading…</span>
                    </div>
                @endif
            </div>
            @if($flashSale->subtitle)
                <p class="mt-1 text-sm text-gray-600">{{ $flashSale->subtitle }}</p>
            @endif
            @if($flashSale->description)
                <p class="mt-2 max-w-3xl text-sm text-gray-500">{{ $flashSale->description }}</p>
            @endif
        </div>

        <div class="flex items-start gap-2">
            <a href="{{ route('store.flash-sale') }}" class="inline-flex items-center rounded-full border border-primary/20 bg-white px-4 py-2 text-sm font-semibold text-primary shadow-sm transition hover:bg-primary hover:text-white">
                View All
            </a>
        </div>
    </div>

    <div class="mt-5 flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-2 no-scrollbar">
        @foreach($saleProducts as $product)
            @php $saleInfo = $flashSaleMap[$product->id] ?? null; @endphp
            @continue(empty($saleInfo))
            <div class="group flex-none w-[220px] sm:w-[240px] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md cursor-pointer snap-start"
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
                        @livewire('store.add-to-cart-button', ['productId' => $product->id], key('flash-sale-'.$product->id))
                    </div>
                    <p class="mt-1 text-xs font-semibold text-green-700">
                        Save {{ $saleInfo['discount_percent'] }}%
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</section>

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
@endif
