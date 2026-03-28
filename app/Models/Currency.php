<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

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
        $configuredCode = Setting::where('key', 'currency')->value('value');

        if ($configuredCode) {
            $configuredCurrency = self::where('code', $configuredCode)->first();
            if ($configuredCurrency) {
                return $configuredCurrency;
            }
        }

        return self::where('is_default', true)->first()
            ?? self::where('active', true)->orderByDesc('is_default')->first()
            ?? self::first()
            ?? new self(['code' => 'BDT', 'symbol' => '৳', 'exchange_rate' => 1]);
    }
}
