<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductShow extends Component
{   
    public $product_id;
    protected $queryString = ['product_id'];

    public function mount($id)
    {
        if (!$id) {
            abort(404, 'Product ID is missing');
        }

        $this->product_id = $id;
    }

    public function render()
    {
        $product = Product::with(['categories', 'attributes.values', 'variations.values', 'relatedProducts'])
            ->findOrFail($this->product_id);

        $now = Carbon::now();
        $last7 = $now->copy()->subDays(7);
        $last30 = $now->copy()->subDays(30);
        $prev30Start = $now->copy()->subDays(60);
        $prev30End = $last30->copy();

        $itemsBase = OrderItem::query()->where('product_id', $this->product_id);
        $totalUnits = (clone $itemsBase)->sum('quantity');
        $totalRevenue = (clone $itemsBase)->select(DB::raw('SUM(quantity * price) as revenue'))->value('revenue') ?? 0;

        $last7Units = (clone $itemsBase)->whereHas('order', function ($q) use ($last7, $now) {
            $q->whereBetween('created_at', [$last7, $now]);
        })->sum('quantity');
        $last7Revenue = (clone $itemsBase)->whereHas('order', function ($q) use ($last7, $now) {
            $q->whereBetween('created_at', [$last7, $now]);
        })->select(DB::raw('SUM(quantity * price) as revenue'))->value('revenue') ?? 0;

        $last30Units = (clone $itemsBase)->whereHas('order', function ($q) use ($last30, $now) {
            $q->whereBetween('created_at', [$last30, $now]);
        })->sum('quantity');
        $last30Revenue = (clone $itemsBase)->whereHas('order', function ($q) use ($last30, $now) {
            $q->whereBetween('created_at', [$last30, $now]);
        })->select(DB::raw('SUM(quantity * price) as revenue'))->value('revenue') ?? 0;

        $prev30Revenue = (clone $itemsBase)->whereHas('order', function ($q) use ($prev30Start, $prev30End) {
            $q->whereBetween('created_at', [$prev30Start, $prev30End]);
        })->select(DB::raw('SUM(quantity * price) as revenue'))->value('revenue') ?? 0;

        $revenueChange = 0;
        if ($prev30Revenue > 0) {
            $revenueChange = (($last30Revenue - $prev30Revenue) / $prev30Revenue) * 100;
        } elseif ($last30Revenue > 0) {
            $revenueChange = 100;
        }

        $recentSales = OrderItem::with(['order.user'])
            ->where('product_id', $this->product_id)
            ->orderByDesc('id')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'order_id' => $item->order_id,
                    'customer' => $item->order?->user?->name ?? 'Guest',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                    'status' => $item->order?->status ?? 'unknown',
                    'created_at' => optional($item->order?->created_at)->format('Y-m-d H:i') ?? '-',
                ];
            });

        return view('livewire.admin.product-show', [
            'product' => $product,
            'salesSummary' => [
                'total_units' => $totalUnits,
                'total_revenue' => (float) $totalRevenue,
                'last7_units' => $last7Units,
                'last7_revenue' => (float) $last7Revenue,
                'last30_units' => $last30Units,
                'last30_revenue' => (float) $last30Revenue,
                'revenue_change' => $revenueChange,
            ],
            'recentSales' => $recentSales,
        ]);
    }
}
