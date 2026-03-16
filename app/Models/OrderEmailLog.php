<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderEmailLog extends Model
{
    protected $fillable = [
        'order_id',
        'admin_id',
        'to_email',
        'subject',
        'message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
