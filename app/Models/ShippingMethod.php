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
}
