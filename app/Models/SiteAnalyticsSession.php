<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteAnalyticsSession extends Model
{
    protected $fillable = [
        'visitor_token',
        'session_token',
        'entry_path',
        'entry_title',
        'entry_referrer',
        'entry_referrer_host',
        'browser',
        'device',
        'ip_address',
        'user_agent',
        'page_views_count',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function pageViews()
    {
        return $this->hasMany(SiteAnalyticsPageView::class, 'site_analytics_session_id');
    }
}
