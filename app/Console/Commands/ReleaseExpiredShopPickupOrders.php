<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReleaseExpiredShopPickupOrders extends Command
{
    protected $signature = 'orders:release-expired-pickup';

    protected $description = 'Cancel expired Pay at Counter orders and release their reserved stock.';

    public function handle(): int
    {
        $holdHours = max(1, (int) (Setting::where('key', 'shop_pickup_hold_hours')->value('value') ?? 24));
        $cutoff = now()->subHours($holdHours);

        $expiredOrders = Order::query()
            ->with('items')
            ->where('payment_method', 'pay_at_shop')
            ->where('payment_status', 'pending')
            ->whereIn('status', ['pending', 'processing'])
            ->where('created_at', '<=', $cutoff)
            ->get();

        $releasedCount = 0;

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    if ($item->product_variation_id) {
                        ProductVariation::where('id', $item->product_variation_id)
                            ->increment('stock', (int) $item->quantity);
                    } elseif ($item->product_id) {
                        Product::where('id', $item->product_id)
                            ->increment('stock', (int) $item->quantity);
                    }
                }

                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'expired',
                ]);
            });

            $releasedCount++;
        }

        $this->info("Released {$releasedCount} expired Pay at Counter order(s) (hold: {$holdHours}h).");

        return self::SUCCESS;
    }
}
