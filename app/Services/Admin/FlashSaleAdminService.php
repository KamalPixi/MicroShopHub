<?php

namespace App\Services\Admin;

use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Support\Collection;

class FlashSaleAdminService
{
    public function save(array $data, ?int $flashSaleId, int $adminId, array $selectedProductIds): FlashSale
    {
        $flashSale = FlashSale::updateOrCreate(
            ['id' => $flashSaleId],
            [
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?: null,
                'description' => $data['description'] ?: null,
                'sale_type' => $data['saleType'],
                'sale_value' => $data['saleValue'],
                'starts_at' => $data['startsAt'],
                'ends_at' => $data['endsAt'],
                'active' => (bool) $data['active'],
                'created_by' => $adminId,
            ]
        );

        $flashSale->products()->sync($selectedProductIds);

        return $flashSale;
    }

    public function delete(int $id): void
    {
        FlashSale::findOrFail($id)->delete();
    }

    public function searchProducts(string $search, int $limit = 24): Collection
    {
        return Product::query()
            ->where('status', true)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function selectedProducts(array $ids): Collection
    {
        return Product::query()
            ->whereIn('id', $ids ?: [0])
            ->where('status', true)
            ->get()
            ->sortBy(fn ($product) => array_search($product->id, $ids))
            ->values();
    }

    public function stats(): array
    {
        return [
            'totalCount' => FlashSale::count(),
            'activeCount' => FlashSale::activeNow()->count(),
            'scheduledCount' => FlashSale::query()
                ->where('active', true)
                ->where('starts_at', '>', now())
                ->count(),
        ];
    }
}
