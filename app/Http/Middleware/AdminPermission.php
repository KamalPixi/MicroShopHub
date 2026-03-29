<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        $routeName = $request->route()?->getName();
        $routeMap = config('admin_permissions.route_map', []);
        $requiredPermission = $permission;

        if ($routeName) {
            foreach ($routeMap as $permissionKey => $patterns) {
                foreach ((array) $patterns as $pattern) {
                    if (Str::is($pattern, $routeName)) {
                        $requiredPermission = $permissionKey;
                        break 2;
                    }
                }
            }
        }

        if ($admin->hasPermission($requiredPermission)) {
            return $next($request);
        }

        return redirect()
            ->route('admin.profile')
            ->with('failed', 'You do not have permission to access this section.');
    }
}
