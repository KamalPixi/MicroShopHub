<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_order')->withPivot('applied_value')->withTimestamps();
    }
}
