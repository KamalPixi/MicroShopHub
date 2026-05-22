<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Currency;
use App\Models\AttributeValue;
use App\Services\FlashSaleService;
use Illuminate\Support\Facades\Storage;

class StoreApiController extends Controller
{
    protected FlashSaleService $flashSaleService;

    public function __construct(FlashSaleService $flashSaleService)
    {
        $this->flashSaleService = $flashSaleService;
    }

    /**
     * Get all homepage settings, sliders, featured products, and active sales.
     */
    public function homepage()
    {
        $activeFlashSale = $this->flashSaleService->currentSale();
        $flashSaleMap = $activeFlashSale ? $this->flashSaleService->productMap($activeFlashSale) : [];

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
                    'image_url' => $this->resolveImageUrl($imagePath),
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

        // 1. Home Categories
        $homeCategories = Category::where('show_on_homepage', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'thumbnail_url' => $this->resolveImageUrl($c->thumbnail),
            ]);

        // 2. Featured Products
        $featuredProductsRaw = Product::where([
                'status' => 1,
                'featured' => true
            ])
            ->inRandomOrder()
            ->take(10)
            ->get();

        $featuredProducts = $featuredProductsRaw->map(fn ($p) => $this->formatProduct($p, $flashSaleMap, $activeFlashSale));

        // 3. New Arrivals
        $newArrivalsRaw = Product::where('status', 1)
            ->latest()
            ->take(10)
            ->get();

        $newArrivals = $newArrivalsRaw->map(fn ($p) => $this->formatProduct($p, $flashSaleMap, $activeFlashSale));

        // Active Currency
        $activeCurrency = Currency::getActive();

        // Flash Sale Format
        $flashSaleData = null;
        if ($activeFlashSale) {
            $flashSaleData = [
                'id' => $activeFlashSale->id,
                'title' => $activeFlashSale->title,
                'subtitle' => $activeFlashSale->subtitle,
                'description' => $activeFlashSale->description,
                'starts_at' => $activeFlashSale->starts_at?->toIso8601String(),
                'ends_at' => $activeFlashSale->ends_at?->toIso8601String(),
            ];
        }

        return response()->json([
            'settings' => $homepageSettings,
            'banners' => $homeBannerSlides,
            'categories' => $homeCategories,
            'featured_products' => $featuredProducts,
            'new_arrivals' => $newArrivals,
            'flash_sale' => $flashSaleData,
            'currency' => [
                'code' => $activeCurrency?->code ?? 'USD',
                'symbol' => $activeCurrency?->symbol ?? '$',
                'exchange_rate' => (float) ($activeCurrency?->exchange_rate ?? 1.0),
            ]
        ]);
    }

    /**
     * Get categories list tree structure.
     */
    public function categories()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'thumbnail_url' => $this->resolveImageUrl($c->thumbnail),
                    'children' => $c->children->map(fn ($sub) => [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'thumbnail_url' => $this->resolveImageUrl($sub->thumbnail),
                    ])
                ];
            });

        return response()->json($categories);
    }

    /**
     * Products listing with search query filters and sorting.
     */
    public function products(Request $request)
    {
        $activeFlashSale = $this->flashSaleService->currentSale();
        $flashSaleMap = $activeFlashSale ? $this->flashSaleService->productMap($activeFlashSale) : [];

        $query = $request->input('query');
        $categoryId = $request->input('category');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sort = $request->input('sort', 'newest');
        $categoryFilterIds = [];

        if ($categoryId) {
            $categoryFilterIds = $this->resolveCategoryFilterIds((int) $categoryId);
        }

        $productsPaginator = Product::where('status', 1)
            ->when($query, function ($q) use ($query) {
                return $q->where(function($subQ) use ($query) {
                    $subQ->where('name', 'like', '%' . $query . '%')
                         ->orWhere('description', 'like', '%' . $query . '%');
                });
            })
            ->when(! empty($categoryFilterIds), function ($q) use ($categoryFilterIds) {
                return $q->whereHas('categories', function ($catQ) use ($categoryFilterIds) {
                    $catQ->whereIn('categories.id', $categoryFilterIds);
                });
            })
            ->when($minPrice, function ($q) use ($minPrice) {
                return $q->where('price', '>=', $minPrice);
            })
            ->when($maxPrice, function ($q) use ($maxPrice) {
                return $q->where('price', '<=', $maxPrice);
            })
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
            ->paginate(12);

        $formattedProducts = collect($productsPaginator->items())->map(fn ($p) => $this->formatProduct($p, $flashSaleMap, $activeFlashSale));

        return response()->json([
            'products' => $formattedProducts,
            'pagination' => [
                'current_page' => $productsPaginator->currentPage(),
                'last_page' => $productsPaginator->lastPage(),
                'per_page' => $productsPaginator->perPage(),
                'total' => $productsPaginator->total(),
            ],
            'categories' => Category::whereNull('parent_id')->with('children')->get()->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'children' => $c->children->map(fn ($sub) => ['id' => $sub->id, 'name' => $sub->name])
            ])
        ]);
    }

    /**
     * Get single product detail by slug.
     */
    public function productDetail($slug)
    {
        $activeFlashSale = $this->flashSaleService->currentSale();
        $flashSaleMap = $activeFlashSale ? $this->flashSaleService->productMap($activeFlashSale) : [];

        $product = Product::where('slug', $slug)
            ->where('status', 1)
            ->with(['categories', 'attributes', 'variations.values.attribute'])
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Build option list
        $productOptions = [];
        if ($product->has_variations) {
            $allValues = $product->variations->flatMap(fn($v) => $v->values);
        } else {
            $valueIds = $product->attributes->pluck('pivot.value_id')->unique();
            $allValues = AttributeValue::with('attribute')->whereIn('id', $valueIds)->get();
        }

        if ($allValues->isNotEmpty()) {
            $productOptions = $allValues->groupBy('attribute_id')->map(function ($values) {
                $first = $values->first();
                return [
                    'id' => $first->attribute_id,
                    'name' => $first->attribute->name,
                    'values' => $values->unique('id')->map(fn($v) => [
                        'id' => $v->id,
                        'value' => $v->value,
                    ])->values()->all()
                ];
            })->values()->toArray();
        }

        // Format Variations
        $variations = [];
        if ($product->has_variations) {
            foreach ($product->variations as $var) {
                $varSalePrice = null;
                if ($activeFlashSale && isset($flashSaleMap[$product->id])) {
                    $computedSale = $this->flashSaleService->applySale(
                        (float) $var->price,
                        (string) $activeFlashSale->sale_type,
                        (float) $activeFlashSale->sale_value
                    );
                    $computedSale = max(0, round($computedSale, 2));
                    if ($computedSale < $var->price) {
                        $varSalePrice = $computedSale;
                    }
                }

                $variations[] = [
                    'id' => $var->id,
                    'sku' => $var->sku,
                    'price' => (float) $var->price,
                    'sale_price' => $varSalePrice,
                    'stock' => $var->stock,
                    'value_ids' => $var->values->pluck('id')->map(fn($id) => (int)$id)->all(),
                ];
            }
        }

        // Related Products
        $relatedProductsRaw = Product::where('status', 1)
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->take(4)
            ->get();

        $relatedProducts = $relatedProductsRaw->map(fn ($p) => $this->formatProduct($p, $flashSaleMap, $activeFlashSale));

        // Get reviews
        $reviews = $product->reviews()->take(10)->get()->map(fn($r) => [
            'id' => $r->id,
            'customer_name' => $r->user_id ? ($r->user->name ?? 'Verified Buyer') : 'Anonymous',
            'rating' => (int) $r->rating,
            'comment' => $r->comment,
            'created_at' => $r->created_at?->diffForHumans(),
        ]);

        return response()->json([
            'product' => $this->formatProduct($product, $flashSaleMap, $activeFlashSale),
            'options' => $productOptions,
            'variations' => $variations,
            'related_products' => $relatedProducts,
            'reviews' => $reviews,
            'flash_sale' => $activeFlashSale ? [
                'id' => $activeFlashSale->id,
                'title' => $activeFlashSale->title,
                'subtitle' => $activeFlashSale->subtitle,
                'starts_at' => $activeFlashSale->starts_at?->toIso8601String(),
                'ends_at' => $activeFlashSale->ends_at?->toIso8601String(),
            ] : null
        ]);
    }

    /**
     * Get dynamic flash sale page data.
     */
    public function flashSale()
    {
        $activeFlashSale = $this->flashSaleService->currentSale();
        if (! $activeFlashSale) {
            return response()->json([
                'flash_sale' => null,
                'products' => []
            ]);
        }

        $flashSaleMap = $this->flashSaleService->productMap($activeFlashSale);
        $productsRaw = $activeFlashSale->products()->where('status', true)->get();
        $formattedProducts = $productsRaw->map(fn ($p) => $this->formatProduct($p, $flashSaleMap, $activeFlashSale));

        return response()->json([
            'flash_sale' => [
                'id' => $activeFlashSale->id,
                'title' => $activeFlashSale->title,
                'subtitle' => $activeFlashSale->subtitle,
                'description' => $activeFlashSale->description,
                'starts_at' => $activeFlashSale->starts_at?->toIso8601String(),
                'ends_at' => $activeFlashSale->ends_at?->toIso8601String(),
            ],
            'products' => $formattedProducts
        ]);
    }

    /**
     * Resolves localized cart state with latest pricing and stock details.
     */
    public function resolveCart(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.variation_id' => 'nullable|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.selected_attributes' => 'nullable|array',
        ]);

        $activeFlashSale = $this->flashSaleService->currentSale();
        $flashSaleMap = $activeFlashSale ? $this->flashSaleService->productMap($activeFlashSale) : [];

        $resolvedItems = [];
        foreach ($request->input('items') as $cartItem) {
            $product = Product::where('status', 1)->find($cartItem['product_id']);
            if (! $product) {
                continue;
            }

            $price = (float) $product->price;
            $stock = $product->stock;
            $variationName = '';
            $selectedAttributes = $cartItem['selected_attributes'] ?? [];

            // If has variations and variation_id is passed, load variation
            if ($product->has_variations && ! empty($cartItem['variation_id'])) {
                $variation = $product->variations()->find($cartItem['variation_id']);
                if ($variation) {
                    $price = (float) $variation->price;
                    $stock = $variation->stock;
                    
                    // Format variation names
                    $variation->load('values.attribute');
                    $variationName = $variation->values->map(fn($v) => "{$v->attribute->name}: {$v->value}")->join(', ');
                }
            }

            // Apply flash sale pricing if applicable
            $salePrice = null;
            if ($activeFlashSale && isset($flashSaleMap[$product->id])) {
                $computedSale = $this->flashSaleService->applySale(
                    (float) $price,
                    (string) $activeFlashSale->sale_type,
                    (float) $activeFlashSale->sale_value
                );
                $computedSale = max(0, round($computedSale, 2));
                if ($computedSale < $price) {
                    $salePrice = $computedSale;
                }
            }

            $resolvedItems[] = [
                'product_id' => $product->id,
                'variation_id' => $cartItem['variation_id'] ?? null,
                'name' => $product->name,
                'slug' => $product->slug,
                'variation_name' => $variationName,
                'quantity' => (int) $cartItem['quantity'],
                'price' => $salePrice !== null ? $salePrice : $price,
                'original_price' => $price,
                'currency_symbol' => $product->currency_symbol,
                'thumbnail_url' => $this->resolveImageUrl($product->thumbnail),
                'stock' => $stock,
                'in_stock' => $stock > 0,
                'selected_attributes' => $selectedAttributes,
            ];
        }

        return response()->json($resolvedItems);
    }

    /**
     * Get content of static pages dynamically.
     */
    public function staticPage($slug)
    {
        $map = [
            'about' => ['title_key' => 'page_about_title', 'content_key' => 'page_about_content', 'fallback' => 'About Us', 'intro' => 'Learn more about our store.'],
            'faq' => ['title_key' => 'page_faq_title', 'content_key' => 'page_faq_content', 'fallback' => 'FAQ', 'intro' => 'Find answers to common questions.'],
            'privacy-policy' => ['title_key' => 'page_privacy_title', 'content_key' => 'page_privacy_content', 'fallback' => 'Privacy Policy', 'intro' => 'We handle your data carefully.'],
            'terms' => ['title_key' => 'page_terms_title', 'content_key' => 'page_terms_content', 'fallback' => 'Terms of Service', 'intro' => 'Please read terms before purchasing.'],
            'refund-policy' => ['title_key' => 'page_refund_title', 'content_key' => 'page_refund_content', 'fallback' => 'Refund Policy', 'intro' => 'How refunds are processed.'],
            'shipping' => ['title_key' => 'page_shipping_title', 'content_key' => 'page_shipping_content', 'fallback' => 'Shipping Info', 'intro' => 'How shipping and deliveries work.'],
            'cookie-policy' => ['title_key' => 'page_cookie_title', 'content_key' => 'page_cookie_content', 'fallback' => 'Cookie Policy', 'intro' => 'This page explains how we use cookies.'],
        ];

        if (! isset($map[$slug])) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        $config = $map[$slug];
        $pageSettings = Setting::whereIn('key', [$config['title_key'], $config['content_key']])
            ->pluck('value', 'key')
            ->toArray();

        return response()->json([
            'title' => $pageSettings[$config['title_key']] ?? $config['fallback'],
            'content' => $pageSettings[$config['content_key']] ?? $config['intro'],
        ]);
    }

    /**
     * Subscribe to newsletter.
     */
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = strtolower(trim($request->input('email')));
        
        $sub = \App\Models\NewsletterSubscription::firstOrCreate(
            ['email' => $email],
            ['active' => true]
        );

        if (! $sub->active) {
            $sub->active = true;
            $sub->save();
        }

        return response()->json(['message' => 'Subscribed successfully! Thank you.']);
    }

    /**
     * Helper to format generic product data.
     */
    protected function formatProduct($product, $flashSaleMap = [], $activeFlashSale = null)
    {
        $salePrice = null;
        if ($activeFlashSale && isset($flashSaleMap[$product->id])) {
            $salePrice = (float) $flashSaleMap[$product->id]['sale_price'];
        } else {
            $salePrice = $this->flashSaleService->salePriceForProduct($product);
        }

        $imageUrls = [];
        if ($product->images && is_array($product->images)) {
            foreach ($product->images as $img) {
                $imageUrls[] = $this->resolveImageUrl($img);
            }
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'description' => strip_tags((string) $product->description),
            'price' => (float) $product->price,
            'sale_price' => $salePrice !== null ? (float) $salePrice : null,
            'discount_amount' => $salePrice !== null ? round((float) $product->price - $salePrice, 2) : 0,
            'discount_percentage' => $salePrice !== null && $product->price > 0 ? (int) round((((float) $product->price - $salePrice) / (float) $product->price) * 100) : 0,
            'stock' => $product->stock,
            'has_variations' => (bool) $product->has_variations,
            'featured' => (bool) $product->featured,
            'status' => (bool) $product->status,
            'thumbnail_url' => $this->resolveImageUrl($product->thumbnail),
            'image_urls' => $imageUrls,
            'average_rating' => $product->average_rating,
            'review_count' => $product->review_count,
            'currency_symbol' => $product->currency_symbol,
        ];
    }

    /**
     * Helper to resolve paths to fully qualified URLs.
     */
    protected function resolveImageUrl(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        return url(Storage::url($path));
    }

    /**
     * Private helper to resolve a category and all descendants.
     */
    private function resolveCategoryFilterIds(int $categoryId): array
    {
        $categories = Category::select('id', 'parent_id')->get();
        $childrenByParent = [];

        foreach ($categories as $category) {
            $parentKey = $category->parent_id ?? 0;
            $childrenByParent[$parentKey][] = (int) $category->id;
        }

        $ids = [];
        $stack = [$categoryId];

        while (! empty($stack)) {
            $currentId = array_pop($stack);

            if (in_array($currentId, $ids, true)) {
                continue;
            }

            $ids[] = $currentId;

            foreach ($childrenByParent[$currentId] ?? [] as $childId) {
                $stack[] = (int) $childId;
            }
        }

        return $ids;
    }
}
