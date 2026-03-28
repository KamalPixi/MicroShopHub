<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            ResetPassword::createUrlUsing(function ($notifiable, string $token) {
                if ($notifiable instanceof Admin) {
                    return route('admin.password.reset', [
                        'token' => $token,
                        'email' => $notifiable->getEmailForPasswordReset(),
                    ]);
                }

                return route('login');
            });

            $settings = Setting::whereIn('key', [
                'pusher_app_id',
                'pusher_app_key',
                'pusher_app_secret',
                'pusher_app_cluster',
            ])->pluck('value', 'key');

            $pusherId = trim((string) ($settings['pusher_app_id'] ?? ''));
            $pusherKey = trim((string) ($settings['pusher_app_key'] ?? ''));
            $pusherSecret = trim((string) ($settings['pusher_app_secret'] ?? ''));
            $pusherCluster = trim((string) ($settings['pusher_app_cluster'] ?? '')) ?: 'mt1';

            if ($pusherId && $pusherKey && $pusherSecret) {
                config([
                    'broadcasting.default' => 'pusher',
                    'broadcasting.connections.pusher.app_id' => $pusherId,
                    'broadcasting.connections.pusher.key' => $pusherKey,
                    'broadcasting.connections.pusher.secret' => $pusherSecret,
                    'broadcasting.connections.pusher.options.cluster' => $pusherCluster,
                    'services.pusher.app_id' => $pusherId,
                    'services.pusher.key' => $pusherKey,
                    'services.pusher.secret' => $pusherSecret,
                    'services.pusher.cluster' => $pusherCluster,
                ]);
            }

            $navbarCategories = Category::whereNull('parent_id')
                ->with('children')
                ->orderBy('name')
                ->get();
            
            View::share('navbarCategories', $navbarCategories);
        } catch (\Exception $e) {
            // Failsafe in case database isn't ready yet (e.g., during migration)
            View::share('navbarCategories', collect());
        }
    }
}
