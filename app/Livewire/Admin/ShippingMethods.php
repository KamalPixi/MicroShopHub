<?php

namespace App\Livewire\Admin;

use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Livewire\Component;
use Livewire\WithPagination;

class ShippingMethods extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
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

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->type = 'flat_rate';
        $this->cost = 0;
        $this->estimated_days = null;
        $this->active = true;
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

        ShippingMethod::updateOrCreate(
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

        session()->flash('message', $this->editingId ? 'Shipping method updated successfully.' : 'Shipping method created successfully.');
        $this->resetForm();
    }

    public function edit($methodId)
    {
        $method = ShippingMethod::findOrFail($methodId);
        $this->editingId = $method->id;
        $this->name = $method->name;
        $this->type = $method->type ?? 'flat_rate';
        $this->cost = $method->cost;
        $this->estimated_days = $method->estimated_days;
        $this->active = $method->active;
    }

    public function deleteShippingMethod($methodId)
    {
        ShippingMethod::findOrFail($methodId)->delete();
        session()->flash('message', 'Shipping method deleted successfully.');
        $this->resetForm();
    }

    public function render()
    {
        $shippingMethods = ShippingMethod::query()
            ->withCount('orders')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

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

        return view('livewire.admin.shipping-methods', [
            'shippingMethods' => $shippingMethods,
            'stats' => $stats,
        ]);
    }
}
