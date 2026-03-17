<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOfflinePayment extends Model
{
    protected $fillable = [
        'order_id',
        'method_name',
        'instructions',
        'reference',
        'amount',
        'attachment_path',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
