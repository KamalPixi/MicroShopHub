<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'sale_type',
        'sale_value',
        'starts_at',
        'ends_at',
        'active',
        'created_by',
    ];

    protected $casts = [
        'sale_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'flash_sale_product')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function scopeActiveNow(Builder $query): Builder
    {
        return $query
            ->where('active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }
}
