<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveChatMessage extends Model
{
    protected $fillable = [
        'session_id',
        'sender',
        'message',
        'product_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(LiveChatSession::class, 'session_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
