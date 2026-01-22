<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class StoreController extends Controller
{
    /**
     * Show the main storefront/index screen.
     */
    public function index()
    {
        // 1. Shop By Category
        $homeCategories = Category::where('show_on_homepage', true)
            ->orderBy('created_at', 'desc')
            ->take(10) 
            ->get();

        // 2. Featured Products
        $featuredProducts = Product::where('status', 1) 
            ->inRandomOrder()
            ->take(10) 
            ->get();

        // 3. New Arrivals
        $newArrivals = Product::where('status', 1)
            ->latest() 
            ->take(10)
            ->get();

        // Pointing to 'store.index' instead of 'home.index'
        return view('store.index', compact('homeCategories', 'featuredProducts', 'newArrivals'));
    }
}
