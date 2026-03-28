<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteAnalyticsPageView extends Model
{
    protected $fillable = [
        'site_analytics_session_id',
        'visitor_token',
        'route_name',
        'page_title',
        'page_path',
        'full_url',
        'referrer_url',
        'referrer_host',
        'browser',
        'device',
        'ip_address',
        'user_agent',
    ];

    public function session()
    {
        return $this->belongsTo(SiteAnalyticsSession::class, 'site_analytics_session_id');
    }
}
