<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{    
    protected $fillable = ['name', 'cost', 'estimated_days', 'active'];
    protected $casts = [
        'active' => 'boolean',
    ];
}
