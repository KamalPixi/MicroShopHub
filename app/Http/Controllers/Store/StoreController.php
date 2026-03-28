<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Support\StorefrontTheme;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    /**
     * Show the main storefront/index screen.
     */
    public function index()
    {
        $homepageSettings = Setting::whereIn('key', [
                'home_hero_enabled',
                'home_banner_type',
                'home_banner_autoplay_enabled',
                'home_hero_title',
                'home_hero_subtitle',
                'home_hero_cta_label',
                'home_hero_cta_url',
                'home_banner_chips',
                'home_banner_slides',
                'home_shop_by_category_enabled',
                'home_shop_by_category_title',
                'home_featured_products_enabled',
                'home_featured_products_title',
                'home_new_arrivals_enabled',
                'home_new_arrivals_title',
                'home_newsletter_enabled',
                'home_newsletter_title',
                'home_newsletter_subtitle',
            ])
            ->pluck('value', 'key')
            ->toArray();

        $homepageSettings['home_hero_enabled'] = filter_var($homepageSettings['home_hero_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $homepageSettings['home_banner_type'] = $homepageSettings['home_banner_type'] ?? 'split';
        $homepageSettings['home_banner_autoplay_enabled'] = filter_var($homepageSettings['home_banner_autoplay_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $homepageSettings['home_shop_by_category_enabled'] = filter_var($homepageSettings['home_shop_by_category_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $homepageSettings['home_featured_products_enabled'] = filter_var($homepageSettings['home_featured_products_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $homepageSettings['home_new_arrivals_enabled'] = filter_var($homepageSettings['home_new_arrivals_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $homepageSettings['home_newsletter_enabled'] = filter_var($homepageSettings['home_newsletter_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $rawSlides = $homepageSettings['home_banner_slides'] ?? '[]';
        $bannerSlides = is_string($rawSlides) ? json_decode($rawSlides, true) : $rawSlides;
        $homeBannerSlides = collect(is_array($bannerSlides) ? $bannerSlides : [])
            ->map(function ($slide) {
                $imagePath = $slide['image'] ?? '';

                return [
                    'image_url' => $this->resolveSettingImageUrl($imagePath),
                    'link_url' => $slide['link_url'] ?? '',
                    'alt' => $slide['alt'] ?? 'Homepage banner',
                ];
            })
            ->filter(fn ($slide) => ! empty($slide['image_url']))
            ->values()
            ->all();

        $rawChips = $homepageSettings['home_banner_chips'] ?? '[]';
        $bannerChips = is_string($rawChips) ? json_decode($rawChips, true) : $rawChips;
        $homepageSettings['home_banner_chips'] = collect(is_array($bannerChips) ? $bannerChips : [])
            ->map(fn ($chip) => trim((string) ($chip['label'] ?? '')))
            ->filter()
            ->values()
            ->all();

        if (empty($homeBannerSlides)) {
            $homeBannerSlides = [
                [
                    'image_url' => 'https://placehold.co/1600x600?text=Banner+Slide+1',
                    'link_url' => '',
                    'alt' => 'Banner slide',
                ],
            ];
        }

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

        return view(StorefrontTheme::homepageView(), compact('homeCategories', 'featuredProducts', 'newArrivals', 'homepageSettings', 'homeBannerSlides'));
    }

    protected function resolveSettingImageUrl(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        return Storage::url($path);
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

    public function sitemap()
    {
        $baseUrl = rtrim(url('/'), '/');

        $urls = collect([
            ['loc' => $baseUrl . '/', 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => $baseUrl . '/about', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => $baseUrl . '/faq', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/contact', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/privacy-policy', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => $baseUrl . '/terms', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => $baseUrl . '/refund-policy', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => $baseUrl . '/shipping', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => $baseUrl . '/cookie-policy', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => $baseUrl . '/search', 'changefreq' => 'weekly', 'priority' => '0.7'],
        ]);

        $categoryUrls = Category::select('id', 'updated_at')
            ->orderBy('name')
            ->get()
            ->map(function ($category) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/search?category=' . $category->id,
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                    'lastmod' => optional($category->updated_at)?->toAtomString(),
                ];
            });

        $productUrls = Product::select('slug', 'updated_at')
            ->where('status', 1)
            ->orderByDesc('updated_at')
            ->take(500)
            ->get()
            ->map(function ($product) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/product/' . $product->slug,
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                    'lastmod' => optional($product->updated_at)?->toAtomString(),
                ];
            });

        $urls = $urls->merge($categoryUrls)->merge($productUrls);

        $xml = view('store.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function about()
    {
        return $this->renderStaticPage(
            'page_about_title',
            'page_about_content',
            'About Us',
            'Learn more about our store, values, and commitment to customers.'
        );
    }

    public function faq()
    {
        return $this->renderStaticPage(
            'page_faq_title',
            'page_faq_content',
            'FAQ',
            'Find answers to common questions about shopping with us.'
        );
    }

    public function contact()
    {
        $supportEmail = Setting::where('key', 'footer_support_email')->value('value')
            ?: Setting::where('key', 'email')->value('value');
        $supportPhone = Setting::where('key', 'footer_support_phone')->value('value')
            ?: Setting::where('key', 'phone')->value('value');

        return view('store.contact', compact('supportEmail', 'supportPhone'));
    }

    public function privacyPolicy()
    {
        return $this->renderStaticPage(
            'page_privacy_title',
            'page_privacy_content',
            'Privacy Policy',
            'We respect your privacy and handle your information carefully.'
        );
    }

    public function terms()
    {
        return $this->renderStaticPage(
            'page_terms_title',
            'page_terms_content',
            'Terms of Service',
            'Please read these terms before placing an order.'
        );
    }

    public function refundPolicy()
    {
        return $this->renderStaticPage(
            'page_refund_title',
            'page_refund_content',
            'Refund Policy',
            'Read how refunds are handled and when they may apply.'
        );
    }

    public function shippingInfo()
    {
        return $this->renderStaticPage(
            'page_shipping_title',
            'page_shipping_content',
            'Shipping Info',
            'Read how shipping, delivery, and tracking work in our store.'
        );
    }

    public function cookiePolicy()
    {
        return $this->renderStaticPage(
            'page_cookie_title',
            'page_cookie_content',
            'Cookie Policy',
            'This page explains how we use cookies and similar tools.'
        );
    }

    protected function renderStaticPage(string $titleKey, string $contentKey, string $fallbackTitle, string $fallbackIntro)
    {
        $pageSettings = Setting::whereIn('key', [$titleKey, $contentKey])
            ->pluck('value', 'key')
            ->toArray();

        return view('store.page', [
            'pageTitle' => $pageSettings[$titleKey] ?? $fallbackTitle,
            'pageContent' => $pageSettings[$contentKey] ?? $fallbackIntro,
        ]);
    }
}
