<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'code', 
        'name', 
        'symbol', 
        'exchange_rate', 
        'active', 
        'is_default'
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_default' => 'boolean',
        'exchange_rate' => 'decimal:4',
    ];

    // Helper to get the store's main currency
    public static function getActive()
    {
        return self::where('is_default', true)->first() 
            ?? self::first() // Fallback
            ?? new self(['code' => 'USD', 'symbol' => '$', 'exchange_rate' => 1]); // Hard fallback
    }
}
