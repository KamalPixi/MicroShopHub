<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterCampaign extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'preheader',
        'template_key',
        'content',
        'featured_product_ids',
        'button_text',
        'button_url',
        'status',
        'scheduled_at',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'featured_product_ids' => 'array',
    ];
}
