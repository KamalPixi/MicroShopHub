<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\View;

class StorefrontTheme
{
    protected static ?array $settingsCache = null;

    public static function all(): array
    {
        return [
            'default' => [
                'label' => 'Default',
                'description' => 'The current storefront experience with the existing product, cart, and homepage layout.',
            ],
            'modern' => [
                'label' => 'Modern',
                'description' => 'A cleaner editorial storefront with lighter shells and a fresh homepage presentation.',
            ],
        ];
    }

    public static function currentKey(): string
    {
        $theme = trim((string) (self::settings()['storefront_theme'] ?? 'default'));

        return array_key_exists($theme, self::all()) ? $theme : 'default';
    }

    public static function current(): array
    {
        return self::all()[self::currentKey()] ?? self::all()['default'];
    }

    public static function partial(string $partial): string
    {
        $theme = self::currentKey();

        if ($theme !== 'default') {
            $candidate = "store.layouts.themes.{$theme}.partials.{$partial}";
            if (View::exists($candidate)) {
                return $candidate;
            }
        }

        return "store.partials.{$partial}";
    }

    public static function homepageView(): string
    {
        $theme = self::currentKey();

        if ($theme !== 'default') {
            $candidate = "store.themes.{$theme}.index";
            if (View::exists($candidate)) {
                return $candidate;
            }
        }

        return 'store.index';
    }

    protected static function settings(): array
    {
        if (self::$settingsCache !== null) {
            return self::$settingsCache;
        }

        self::$settingsCache = Setting::where('key', 'storefront_theme')
            ->pluck('value', 'key')
            ->toArray();

        return self::$settingsCache;
    }
}
