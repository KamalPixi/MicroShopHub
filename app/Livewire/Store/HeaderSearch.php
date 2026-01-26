<?php

namespace App\Livewire\Store;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class HeaderSearch extends Component
{
    public $query = '';
    public $category = '';


    public function performSearch()
    {
        // Redirect to the full search page with params
        return redirect()->route('store.search', [
            'query' => $this->query,
            'category' => $this->category
        ]);
    }

    public function render()
    {
        $results = [];

        // Only search if query has at least 2 characters
        if (strlen($this->query) >= 2) {
            $results = Product::where('status', 1) // Only active products
                ->where('name', 'like', '%' . $this->query . '%')
                ->when($this->category, function ($q) {
                    // Filter by category if selected
                    return $q->whereHas('categories', function ($catQuery) {
                        $catQuery->where('categories.id', $this->category);
                    });
                })
                ->take(7) // Limit results for the dropdown
                ->get();
        }

        // Fetch root categories for the dropdown
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('livewire.store.header-search', [
            'results' => $results,
            'categories' => $categories
        ]);
    }
}
