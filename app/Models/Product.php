<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'has_variations' => 'boolean',
        'featured' => 'boolean',
        'status' => 'boolean',
        'images' => 'array',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'has_variations',
        'thumbnail',
        'images',
        'featured',
        'status',
    ];

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
}
