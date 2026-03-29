<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class StoreLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = Setting::whereIn('key', [
            'store_default_locale',
            'store_language_en_enabled',
            'store_language_bn_enabled',
        ])->pluck('value', 'key');

        $defaultLocale = in_array(($settings['store_default_locale'] ?? 'en'), ['en', 'bn'], true)
            ? $settings['store_default_locale']
            : 'en';

        $availableLocales = [
            'en' => [
                'code' => 'en',
                'label' => 'English',
                'native' => 'EN',
            ],
            'bn' => [
                'code' => 'bn',
                'label' => 'বাংলা',
                'native' => 'BN',
            ],
        ];

        $enabledLocales = [];
        if (filter_var($settings['store_language_en_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
            $enabledLocales['en'] = $availableLocales['en'];
        }
        if (filter_var($settings['store_language_bn_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
            $enabledLocales['bn'] = $availableLocales['bn'];
        }

        if (empty($enabledLocales)) {
            $enabledLocales = ['en' => $availableLocales['en']];
        }

        $locale = session('store_locale', $defaultLocale);
        if (! array_key_exists($locale, $enabledLocales)) {
            $locale = array_key_first($enabledLocales) ?: 'en';
        }

        app()->setLocale($locale);
        View::share('storeLocale', $locale);
        View::share('storeLocales', $enabledLocales);
        View::share('storeDefaultLocale', $defaultLocale);

        session()->put('store_locale', $locale);

        return $next($request);
    }
}
