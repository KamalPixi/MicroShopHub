<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

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
