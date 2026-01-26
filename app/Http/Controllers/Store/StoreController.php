<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
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
        $featuredProducts = Product::where([
                'status' => 1,
                'featured' => true
            ]) 
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

    /**
     * Show the search results page with advanced filtering.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $categoryId = $request->input('category');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sort = $request->input('sort', 'newest'); // Default to newest

        $products = Product::where('status', 1)
            // 1. Text Search
            ->when($query, function ($q) use ($query) {
                return $q->where(function($subQ) use ($query) {
                    $subQ->where('name', 'like', '%' . $query . '%')
                         ->orWhere('description', 'like', '%' . $query . '%');
                });
            })
            // 2. Category Filter
            ->when($categoryId, function ($q) use ($categoryId) {
                return $q->whereHas('categories', function ($catQ) use ($categoryId) {
                    $catQ->where('categories.id', $categoryId);
                });
            })
            // 3. Price Range
            ->when($minPrice, function ($q) use ($minPrice) {
                return $q->where('price', '>=', $minPrice);
            })
            ->when($maxPrice, function ($q) use ($maxPrice) {
                return $q->where('price', '<=', $maxPrice);
            })
            // 4. Sorting
            ->when($sort, function ($q) use ($sort) {
                switch ($sort) {
                    case 'price_low':
                        return $q->orderBy('price', 'asc');
                    case 'price_high':
                        return $q->orderBy('price', 'desc');
                    case 'oldest':
                        return $q->orderBy('created_at', 'asc');
                    default: // newest
                        return $q->orderBy('created_at', 'desc');
                }
            })
            ->paginate(10) // 15 items to fit 5-column layout nicely (3 rows)
            ->withQueryString(); // Keep search params in pagination links

        $categories = Category::whereNull('parent_id')->with('children')->get();

        return view('store.search', compact('products', 'categories', 'query', 'categoryId', 'minPrice', 'maxPrice', 'sort'));
    }

    /**
     * Show the single product details page.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 1)
            ->with(['categories', 'attributes', 'variations']) 
            ->firstOrFail();

        // Get related products (same category)
        $relatedProducts = Product::where('status', 1)
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->take(4)
            ->get();

        return view('store.product', compact('product', 'relatedProducts'));
    }

    public function cart()
    {
        return view('store.cart');
    }
}
