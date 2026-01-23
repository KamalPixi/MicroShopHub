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
        'country',
        'is_default'
    ];

    /**
     * Get the parent addressable model (User, Order, etc.).
     */
    public function addressable()
    {
        return $this->morphTo();
    }
}
