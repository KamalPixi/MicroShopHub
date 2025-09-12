<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{    
    protected $fillable = [
        'code',
        'type',
        'value', 
        'min_order_amount', 
        'usage_limit', 
        'per_user_limit', 
        'starts_at', 
        'expires_at', 
        'active',
    ];
    protected $casts = [
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'discount_order')->withPivot('applied_value')->withTimestamps();
    }
}
