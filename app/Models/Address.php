<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'type', 
        'name', 
        'email', 
        'phone', 
        'address_line1', 
        'address_line2', 
        'city', 
        'state', 
        'postal_code', 
        'country_code',
        'is_default'
    ];

    /**
     * Get the parent addressable model (User, Order, etc.).
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    public function getCountryLabelAttribute(): string
    {
        static $cache = [];

        $value = trim((string) ($this->country_code ?? $this->country ?? ''));

        if ($value === '') {
            return '';
        }

        if (strlen($value) === 2) {
            $code = strtoupper($value);

            if (! array_key_exists($code, $cache)) {
                $cache[$code] = Country::query()->where('code', $code)->value('name') ?: $value;
            }

            return $cache[$code];
        }

        return $value;
    }
}
