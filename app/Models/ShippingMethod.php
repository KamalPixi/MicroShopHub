<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{    
    protected $fillable = ['shipping_zone_id', 'name', 'type', 'provider', 'external_id', 'cost', 'estimated_days', 'active'];
    protected $casts = [
        'active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_shipping_method')->withTimestamps();
    }

    public function getDeliveryModeAttribute(): string
    {
        if (($this->type ?? 'flat_rate') === 'local_pickup') {
            return 'shop_pickup';
        }

        if (($this->provider ?? 'manual') === 'sendcloud') {
            $name = strtolower((string) $this->name);
            if (
                str_contains($name, 'shop2shop')
                || str_contains($name, 'relais')
                || str_contains($name, 'locker')
                || str_contains($name, 'service point')
                || str_contains($name, 'pickup point')
            ) {
                return 'service_point';
            }
        }

        return 'home_delivery';
    }

    public function getDeliveryModeLabelAttribute(): string
    {
        return match ($this->delivery_mode) {
            'shop_pickup' => 'In-Store Pickup',
            'service_point' => 'Pickup Point / Locker',
            default => 'Home Delivery',
        };
    }
}
