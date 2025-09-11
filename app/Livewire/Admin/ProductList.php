<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';

    public function deleteProduct($productId) {
        $product = Product::findOrFail($productId);
        $product->delete();
    }

    public function render()
    {
        $products = Product::with(['categories', 'attributes.values', 'variations.values', 'relatedProducts'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%')
                      ->orWhereHas('categories', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.product-list', [
            'products' => $products,
        ]);
    }
}
