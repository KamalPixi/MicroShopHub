<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $lockPath = storage_path('app/installed.lock');
        $isInstalled = file_exists($lockPath);

        if ($isInstalled) {
            return $next($request);
        }

        if ($request->is('install') || $request->is('install/*')) {
            return $next($request);
        }

        return redirect()->route('install.requirements');
    }
}
