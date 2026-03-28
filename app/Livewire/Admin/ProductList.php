<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\OrderItem;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';

    public function deleteProduct($productId) {
        $product = Product::findOrFail($productId);
        $product->delete();
        session()->flash('message', 'Product deleted successfully!');
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

        $soldAmounts = collect();
        $productIds = $products->getCollection()->pluck('id')->all();
        if (! empty($productIds)) {
            $soldAmounts = OrderItem::query()
                ->selectRaw('product_id, COALESCE(SUM(quantity * price), 0) as sold_amount')
                ->whereIn('product_id', $productIds)
                ->groupBy('product_id')
                ->pluck('sold_amount', 'product_id');
        }

        $products->getCollection()->transform(function ($product) use ($soldAmounts) {
            $product->sold_amount = (float) ($soldAmounts[$product->id] ?? 0);
            return $product;
        });

        return view('livewire.admin.product-list', [
            'products' => $products,
        ]);
    }
}
