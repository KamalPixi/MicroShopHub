<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_order')->withPivot('applied_value')->withTimestamps();
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    // Helper to get specifically the billing address
    public function billingAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->where('type', 'billing');
    }

    // Helper to get specifically the shipping address
    public function shippingAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->where('type', 'shipping');
    }
}
