<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Livewire\Component;
use Livewire\WithPagination;

class ShippingMethods extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $providerFilter = 'all';
    public $costFilter = 'all';
    public $suitabilityFilter = 'all';
    public $perPage = 15;
    public array $selectedIds = [];
    public bool $selectAll = false;
    public array $selectedCategoryIds = [];
    public string $bulkCategoryId = '';
    public $name = '';
    public $type = 'flat_rate';
    public $cost = 0;
    public $estimated_days = null;
    public $active = true;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:shipping_methods,name',
        'type' => 'required|in:flat_rate,free_shipping,local_pickup',
        'cost' => 'required|numeric|min:0',
        'estimated_days' => 'nullable|integer|min:0',
        'active' => 'boolean',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'providerFilter' => ['except' => 'all'],
        'costFilter' => ['except' => 'all'],
        'suitabilityFilter' => ['except' => 'all'],
        'perPage' => ['except' => 15],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingProviderFilter()
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingCostFilter()
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingSuitabilityFilter()
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->type = 'flat_rate';
        $this->cost = 0;
        $this->estimated_days = null;
        $this->active = true;
        $this->selectedCategoryIds = [];
        $this->editingId = null;
        $this->resetValidation();
    }

    public function save()
    {
        if ($this->editingId) {
            $this->rules['name'] = 'required|string|max:255|unique:shipping_methods,name,' . $this->editingId;
        }

        $this->validate();

        $defaultZoneId = ShippingZone::query()->value('id');
        if (! $defaultZoneId) {
            $defaultZoneId = ShippingZone::create(['name' => 'Default Zone'])->id;
        }

        $existing = $this->editingId ? ShippingMethod::find($this->editingId) : null;

        $method = ShippingMethod::updateOrCreate(
            ['id' => $this->editingId],
            [
                'shipping_zone_id' => $existing?->shipping_zone_id ?? $defaultZoneId,
                'name' => $this->name,
                'type' => $this->type,
                'cost' => $this->type === 'free_shipping' ? 0 : $this->cost,
                'estimated_days' => $this->estimated_days ?? 0,
                'active' => $this->active,
            ]
        );

        $method->categories()->sync(array_map('intval', $this->selectedCategoryIds));

        session()->flash('message', $this->editingId ? 'Shipping method updated successfully.' : 'Shipping method created successfully.');
        $this->resetForm();
    }

    public function edit($methodId)
    {
        $method = ShippingMethod::with('categories:id')->findOrFail($methodId);
        $this->editingId = $method->id;
        $this->name = $method->name;
        $this->type = $method->type ?? 'flat_rate';
        $this->cost = $method->cost;
        $this->estimated_days = $method->estimated_days;
        $this->active = $method->active;
        $this->selectedCategoryIds = $method->categories->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    public function toggleActive(int $methodId): void
    {
        $method = ShippingMethod::findOrFail($methodId);
        $method->update(['active' => ! $method->active]);
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedIds = $this->buildFilteredQuery()
                ->orderByDesc('active')
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();
        } else {
            $this->selectedIds = [];
        }
    }

    public function bulkActivate(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }
        ShippingMethod::whereIn('id', $this->selectedIds)->update(['active' => true]);
        session()->flash('message', count($this->selectedIds) . ' shipping method(s) activated.');
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function bulkDeactivate(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }
        ShippingMethod::whereIn('id', $this->selectedIds)->update(['active' => false]);
        session()->flash('message', count($this->selectedIds) . ' shipping method(s) deactivated.');
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }
        ShippingMethod::whereIn('id', $this->selectedIds)->delete();
        session()->flash('message', count($this->selectedIds) . ' shipping method(s) deleted.');
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function bulkTagCategory(): void
    {
        if (empty($this->selectedIds) || $this->bulkCategoryId === '') {
            return;
        }

        $categoryId = (int) $this->bulkCategoryId;
        $methods = ShippingMethod::whereIn('id', $this->selectedIds)->get();
        foreach ($methods as $method) {
            $method->categories()->syncWithoutDetaching([$categoryId]);
        }

        session()->flash('message', 'Tagged ' . count($this->selectedIds) . ' shipping method(s) to category.');
        $this->bulkCategoryId = '';
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function deactivateAllSendcloud(): void
    {
        $count = ShippingMethod::where('provider', 'sendcloud')->update(['active' => false]);
        session()->flash('message', "Deactivated {$count} Sendcloud shipping method(s).");
    }

    public function activateAllSendcloud(): void
    {
        $count = ShippingMethod::where('provider', 'sendcloud')->update(['active' => true]);
        session()->flash('message', "Activated {$count} Sendcloud shipping method(s).");
    }

    public function deleteShippingMethod($methodId)
    {
        ShippingMethod::findOrFail($methodId)->delete();
        session()->flash('message', 'Shipping method deleted successfully.');
        $this->resetForm();
    }

    protected function buildFilteredQuery()
    {
        return ShippingMethod::query()
            ->with('categories:id,name')
            ->withCount('orders')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('active', true))
            ->when($this->statusFilter === 'inactive', fn ($q) => $q->where('active', false))
            ->when($this->providerFilter === 'sendcloud', fn ($q) => $q->where('provider', 'sendcloud'))
            ->when($this->providerFilter === 'manual', fn ($q) => $q->where(function ($sub) {
                $sub->whereNull('provider')->orWhere('provider', '!=', 'sendcloud');
            }))
            ->when($this->costFilter === 'free', fn ($q) => $q->where('cost', '<=', 0))
            ->when($this->costFilter === 'paid', fn ($q) => $q->where('cost', '>', 0))
            ->when($this->suitabilityFilter === 'electronics', function ($q) {
                $q->where('cost', '>', 0)
                    ->whereRaw("LOWER(name) NOT LIKE '%letter%'")
                    ->whereRaw("LOWER(name) NOT LIKE '%unstamped%'")
                    ->whereRaw("LOWER(name) NOT LIKE '%return%'")
                    ->where(function ($sub) {
                        $sub->whereRaw("LOWER(name) LIKE '%home%'")
                            ->orWhereRaw("LOWER(name) LIKE '%express%'")
                            ->orWhereRaw("LOWER(name) LIKE '%colissimo%'")
                            ->orWhereRaw("LOWER(name) LIKE '%chronopost%'")
                            ->orWhereRaw("LOWER(name) LIKE '%dhl%'")
                            ->orWhereRaw("LOWER(name) LIKE '%ups%'")
                            ->orWhereRaw("LOWER(name) LIKE '%dpd%'")
                            ->orWhereRaw("LOWER(name) LIKE '%gls%'")
                            ->orWhereRaw("LOWER(name) LIKE '%0-5kg%'")
                            ->orWhereRaw("LOWER(name) LIKE '%1-2kg%'")
                            ->orWhereRaw("LOWER(name) LIKE '%2-5kg%'")
                            ->orWhereRaw("LOWER(name) LIKE '%0.5-1kg%'");
                    });
            })
            ->when($this->suitabilityFilter === 'home', function ($q) {
                $q->whereRaw("LOWER(name) LIKE '%home%'")
                    ->orWhereRaw("LOWER(name) LIKE '%domicile%'")
                    ->orWhereRaw("LOWER(name) LIKE '%signature%'");
            })
            ->when($this->suitabilityFilter === 'express', function ($q) {
                $q->whereRaw("LOWER(name) LIKE '%express%'")
                    ->orWhereRaw("LOWER(name) LIKE '%chrono%'")
                    ->orWhereRaw("LOWER(name) LIKE '%dhl%'")
                    ->orWhereRaw("LOWER(name) LIKE '%ups%'");
            })
            ->when($this->suitabilityFilter === 'pickup_point', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereRaw("LOWER(name) LIKE '%locker%'")
                        ->orWhereRaw("LOWER(name) LIKE '%relais%'")
                        ->orWhereRaw("LOWER(name) LIKE '%shop2shop%'")
                        ->orWhere('type', 'local_pickup');
                });
            });
    }

    public function render()
    {
        $query = $this->buildFilteredQuery();

        if ($this->costFilter === 'low_to_high') {
            $query->orderBy('cost', 'asc');
        } elseif ($this->costFilter === 'high_to_low') {
            $query->orderBy('cost', 'desc');
        } else {
            $query->orderByDesc('active')->orderBy('created_at', 'desc');
        }

        $shippingMethods = $query->paginate($this->perPage);

        $statsBase = ShippingMethod::query()->withCount('orders');
        $usedMethods = (clone $statsBase)->has('orders')->count();
        $inactiveMethods = (clone $statsBase)->where('active', false)->count();
        $mostUsedMethod = (clone $statsBase)->orderByDesc('orders_count')->first();

        $stats = [
            'total_methods' => (clone $statsBase)->count(),
            'active_methods' => (clone $statsBase)->where('active', true)->count(),
            'used_methods' => $usedMethods,
            'unused_methods' => (clone $statsBase)->doesntHave('orders')->count(),
            'inactive_methods' => $inactiveMethods,
            'total_orders_using_shipping' => \App\Models\Order::query()->whereNotNull('shipping_method_id')->count(),
            'most_used_method_name' => $mostUsedMethod?->name,
            'most_used_method_count' => $mostUsedMethod?->orders_count ?? 0,
        ];

        $allCategories = Category::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.shipping-methods', [
            'shippingMethods' => $shippingMethods,
            'stats' => $stats,
            'allCategories' => $allCategories,
        ]);
    }
}
