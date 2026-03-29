<?php

namespace App\Services;

use App\Models\FlashSale;
use App\Models\Product;

class FlashSaleService
{
    public function currentSale(): ?FlashSale
    {
        return FlashSale::query()
            ->activeNow()
            ->with(['products' => function ($query) {
                $query->where('status', true)->orderBy('name');
            }])
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->first();
    }

    public function productMap(?FlashSale $sale = null): array
    {
        $sale = $sale ?: $this->currentSale();

        if (! $sale) {
            return [];
        }

        $sale->loadMissing('products');

        $map = [];
        foreach ($sale->products as $product) {
            $info = $this->saleInfoForProduct($product, $sale, (float) $product->price);
            if ($info) {
                $map[$product->id] = $info;
            }
        }

        return $map;
    }

    public function saleInfoForProduct(Product $product, ?FlashSale $sale = null, ?float $basePrice = null): ?array
    {
        $sale = $sale ?: $this->currentSale();

        if (! $sale || ! $sale->products->contains('id', $product->id)) {
            return null;
        }

        $basePrice = $basePrice ?? $product->price;
        if ((! is_numeric($basePrice) || (float) $basePrice <= 0) && $product->has_variations) {
            $basePrice = $product->variations->min('price') ?? 0;
        }

        $basePrice = (float) ($basePrice ?? 0);
        $salePrice = $this->applySale($basePrice, $sale->sale_type, (float) $sale->sale_value);
        $salePrice = max(0, round($salePrice, 2));
        $discountAmount = max(0, round($basePrice - $salePrice, 2));
        $discountPercent = $basePrice > 0 ? (int) round(($discountAmount / $basePrice) * 100) : 0;

        if ($salePrice >= $basePrice) {
            return null;
        }

        return [
            'sale_id' => $sale->id,
            'title' => $sale->title,
            'subtitle' => $sale->subtitle,
            'description' => $sale->description,
            'sale_type' => $sale->sale_type,
            'sale_value' => (float) $sale->sale_value,
            'original_price' => $basePrice,
            'sale_price' => $salePrice,
            'discount_amount' => $discountAmount,
            'discount_percent' => $discountPercent,
            'starts_at' => $sale->starts_at,
            'ends_at' => $sale->ends_at,
            'product_ids' => $sale->products->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
        ];
    }

    public function salePriceForProduct(Product $product, ?FlashSale $sale = null, ?float $basePrice = null): ?float
    {
        $info = $this->saleInfoForProduct($product, $sale, $basePrice);

        return $info['sale_price'] ?? null;
    }

    public function applySale(float $basePrice, string $saleType, float $saleValue): float
    {
        return match ($saleType) {
            'fixed' => $basePrice - $saleValue,
            default => $basePrice - (($basePrice * $saleValue) / 100),
        };
    }
}
