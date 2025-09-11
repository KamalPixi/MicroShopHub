<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'has_variations' => 'boolean'
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'has_variations',
        'thumbnail',
        'status',
    ];

    /**
     * Many-to-Many relationship with Category
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products', 'product_id', 'category_id');
    }

    /**
     * Many-to-Many relationship with Attribute (e.g., Size, Color)
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes', 'product_id', 'attribute_id')->withPivot('value_id');
    }

    /**
     * One-to-Many relationship with ProductVariation
     */
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    /**
     * Many-to-Many relationship for Related Products (self-referential)
     */
    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_relations', 'product_id', 'related_product_id')->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
