<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveChatSession extends Model
{
    protected $fillable = [
        'session_token',
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(LiveChatMessage::class, 'session_id');
    }
}
