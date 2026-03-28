<?php

namespace App\Http\Middleware;

use App\Models\SiteAnalyticsPageView;
use App\Models\SiteAnalyticsSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class StoreAnalytics
{
    private const PAGE_VIEW_DEDUPE_MINUTES = 5;

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('get')) {
            return $next($request);
        }

        if (! Schema::hasTable('site_analytics_sessions') || ! Schema::hasTable('site_analytics_page_views')) {
            return $next($request);
        }

        $route = $request->route();
        $routeName = $route?->getName();
        $visitorToken = (string) $request->cookie('shophub_visitor_token');
        if ($visitorToken === '') {
            $visitorToken = (string) Str::uuid();
        }

        $sessionToken = (string) $request->cookie('shophub_analytics_session_token');
        $now = now();
        $session = null;
        $requestHost = (string) $request->getHost();

        if ($sessionToken !== '') {
            $session = SiteAnalyticsSession::where('session_token', $sessionToken)->first();
            if ($session && $session->last_seen_at && $session->last_seen_at->diffInMinutes($now) > 30) {
                $session = null;
                $sessionToken = '';
            }
        }

        if (! $session) {
            $sessionToken = (string) Str::uuid();
            $session = SiteAnalyticsSession::create([
                'visitor_token' => $visitorToken,
                'session_token' => $sessionToken,
                'entry_path' => $request->path(),
                'entry_title' => $this->resolvePageLabel($routeName, $request->path(), $route),
                'entry_referrer' => $this->cleanReferrer((string) $request->headers->get('referer')),
                'entry_referrer_host' => $this->extractHost((string) $request->headers->get('referer'), $requestHost),
                'browser' => $this->parseBrowser($request->userAgent()),
                'device' => $this->parseDevice($request->userAgent()),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'page_views_count' => 0,
                'first_seen_at' => $now,
                'last_seen_at' => $now,
            ]);
        }

        $pageLabel = $this->resolvePageLabel($routeName, $request->path(), $route);
        $referrer = $this->cleanReferrer((string) $request->headers->get('referer'));
        $referrerHost = $this->extractHost($referrer, $requestHost);
        $browser = $this->parseBrowser($request->userAgent());
        $device = $this->parseDevice($request->userAgent());
        $fullUrl = $request->fullUrl();

        $recentView = SiteAnalyticsPageView::query()
            ->where('site_analytics_session_id', $session->id)
            ->where('visitor_token', $visitorToken)
            ->where('route_name', $routeName)
            ->where('page_path', '/' . ltrim($request->path(), '/'))
            ->where('full_url', $fullUrl)
            ->latest('created_at')
            ->first();

        if ($recentView && $recentView->created_at && $recentView->created_at->diffInMinutes($now) < self::PAGE_VIEW_DEDUPE_MINUTES) {
            $session->forceFill(['last_seen_at' => $now])->save();

            $response = $next($request);

            return $response
                ->withCookie(cookie('shophub_visitor_token', $visitorToken, 60 * 24 * 365))
                ->withCookie(cookie('shophub_analytics_session_token', $sessionToken, 60 * 24 * 30));
        }

        SiteAnalyticsPageView::create([
            'site_analytics_session_id' => $session->id,
            'visitor_token' => $visitorToken,
            'route_name' => $routeName,
            'page_title' => $pageLabel,
            'page_path' => '/' . ltrim($request->path(), '/'),
            'full_url' => $fullUrl,
            'referrer_url' => $referrer,
            'referrer_host' => $referrerHost,
            'browser' => $browser,
            'device' => $device,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $session->increment('page_views_count');
        $session->forceFill(['last_seen_at' => $now])->save();

        $response = $next($request);

        return $response
            ->withCookie(cookie('shophub_visitor_token', $visitorToken, 60 * 24 * 365))
            ->withCookie(cookie('shophub_analytics_session_token', $sessionToken, 60 * 24 * 30));
    }

    protected function resolvePageLabel(?string $routeName, string $path, $route): string
    {
        if ($routeName === 'store.index') {
            return 'Home';
        }

        if ($routeName === 'store.search') {
            return 'Search Results';
        }

        if ($routeName === 'store.cart.index') {
            return 'Cart';
        }

        if ($routeName === 'customer.dashboard') {
            return 'Customer Dashboard';
        }

        if ($routeName === 'store.product.show') {
            $slug = (string) ($route?->parameter('slug') ?? '');

            return $slug !== '' ? 'Product: '.$slug : 'Product Details';
        }

        if (str_starts_with((string) $path, 'privacy-policy')) {
            return 'Privacy Policy';
        }

        if (str_starts_with((string) $path, 'terms')) {
            return 'Terms of Service';
        }

        if (str_starts_with((string) $path, 'cookie-policy')) {
            return 'Cookie Policy';
        }

        if ($routeName) {
            return Str::title(str_replace(['.', '-', '_'], ' ', $routeName));
        }

        return Str::title(str_replace(['/', '-', '_'], ' ', trim($path, '/'))) ?: 'Unknown Page';
    }

    protected function cleanReferrer(string $referrer): string
    {
        return trim($referrer);
    }

    protected function extractHost(string $url, ?string $requestHost = null): string
    {
        if ($url === '') {
            return '';
        }

        $host = parse_url($url, PHP_URL_HOST);
        $host = is_string($host) ? strtolower($host) : '';

        if ($host === '') {
            return '';
        }

        if ($requestHost && strtolower($requestHost) === $host) {
            return '';
        }

        return $host;
    }

    protected function parseBrowser(?string $userAgent): string
    {
        $ua = strtolower((string) $userAgent);

        return match (true) {
            $ua === '' => 'Unknown',
            str_contains($ua, 'edg/') || str_contains($ua, 'edge/') => 'Edge',
            str_contains($ua, 'opr/') || str_contains($ua, 'opera') => 'Opera',
            str_contains($ua, 'chrome/') && ! str_contains($ua, 'edg/') && ! str_contains($ua, 'opr/') => 'Chrome',
            str_contains($ua, 'firefox/') => 'Firefox',
            str_contains($ua, 'safari/') && ! str_contains($ua, 'chrome/') => 'Safari',
            str_contains($ua, 'msie') || str_contains($ua, 'trident/') => 'Internet Explorer',
            str_contains($ua, 'bot') || str_contains($ua, 'crawl') || str_contains($ua, 'spider') => 'Bot',
            default => 'Other',
        };
    }

    protected function parseDevice(?string $userAgent): string
    {
        $ua = strtolower((string) $userAgent);

        return match (true) {
            $ua === '' => 'Unknown',
            str_contains($ua, 'tablet') || str_contains($ua, 'ipad') => 'Tablet',
            str_contains($ua, 'mobi') || str_contains($ua, 'android') || str_contains($ua, 'iphone') => 'Mobile',
            default => 'Desktop',
        };
    }
}
