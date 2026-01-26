<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected static $activeCurrencySymbol = null;

    protected $casts = [
        'has_variations' => 'boolean',
        'featured' => 'boolean',
        'status' => 'boolean',
        'images' => 'array',
    ];

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'stock',
        'has_variations',
        'thumbnail',
        'images',
        'featured',
        'status',
    ];

    /**
     * Get the store's active currency symbol.
     * Usage: $product->currency_symbol
     */
    public function getCurrencySymbolAttribute()
    {
        if (self::$activeCurrencySymbol === null) {
            // Fetch once per request
            self::$activeCurrencySymbol = Currency::getActive()->symbol;
        }

        return self::$activeCurrencySymbol;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products', 'product_id', 'category_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes', 'product_id', 'attribute_id')->withPivot('value_id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_relations', 'product_id', 'related_product_id')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', true)->latest();
    }

    // Helper to get average rating (e.g., 4.5)
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?? 0;
    }

    // Helper to get review count
    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }
}
