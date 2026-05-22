<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{    
    protected $fillable = ['shipping_zone_id', 'name', 'type', 'cost', 'estimated_days', 'is_taxable', 'active'];
    protected $casts = [
        'active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
