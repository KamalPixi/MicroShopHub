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
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_encryption',
                'mail_from_address',
                'mail_from_name',
                'pusher_app_id',
                'pusher_app_key',
                'pusher_app_secret',
                'pusher_app_cluster',
            ])->pluck('value', 'key');

            $mailHost = trim((string) ($settings['mail_host'] ?? ''));
            $mailPort = (int) ($settings['mail_port'] ?? 0);
            $mailUsername = trim((string) ($settings['mail_username'] ?? ''));
            $mailPassword = (string) ($settings['mail_password'] ?? '');
            $mailEncryption = trim((string) ($settings['mail_encryption'] ?? 'tls')) ?: 'tls';
            $mailFromAddress = trim((string) ($settings['mail_from_address'] ?? ''));
            $mailFromName = trim((string) ($settings['mail_from_name'] ?? ''));

            if ($mailHost !== '' && $mailPort > 0) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => $mailHost,
                    'mail.mailers.smtp.port' => $mailPort,
                    'mail.mailers.smtp.username' => $mailUsername ?: null,
                    'mail.mailers.smtp.password' => $mailPassword ?: null,
                    'mail.mailers.smtp.encryption' => $mailEncryption !== 'none' ? $mailEncryption : null,
                    'mail.from.address' => $mailFromAddress ?: '',
                    'mail.from.name' => $mailFromName ?: '',
                ]);
            }

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

            $storeMeta = Setting::whereIn('key', ['shop_name', 'site_title'])
                ->pluck('value', 'key');
            $siteStoreName = trim((string) ($storeMeta['shop_name'] ?? '')) ?: config('app.name', 'ShopHub');
            $siteStoreSlogan = trim((string) ($storeMeta['site_title'] ?? ''));
            
            View::share('navbarCategories', $navbarCategories);
            View::share('siteStoreName', $siteStoreName);
            View::share('siteStoreSlogan', $siteStoreSlogan);
        } catch (\Exception $e) {
            // Failsafe in case database isn't ready yet (e.g., during migration)
            View::share('navbarCategories', collect());
            View::share('siteStoreName', config('app.name', 'ShopHub'));
            View::share('siteStoreSlogan', '');
        }
    }
}
