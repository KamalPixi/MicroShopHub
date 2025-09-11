<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;

class ProductShow extends Component
{   
    public $product_id;
    protected $queryString = ['product_id'];

    public function render()
    {
        $product = Product::with(['categories', 'attributes.values', 'variations.values', 'relatedProducts'])
            ->findOrFail($this->product_id);

        return view('livewire.admin.product-show', [
            'product' => $product,
        ]);
    }
}
