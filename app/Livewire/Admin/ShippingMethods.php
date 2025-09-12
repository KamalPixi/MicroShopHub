<?php

namespace App\Livewire\Admin;

use App\Models\ShippingMethod;
use Livewire\Component;
use Livewire\WithPagination;

class ShippingMethods extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $name = '';
    public $cost = 0;
    public $estimated_days = null;
    public $active = true;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:shipping_methods,name',
        'cost' => 'required|numeric|min:0',
        'estimated_days' => 'nullable|integer|min:1',
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

        ShippingMethod::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'cost' => $this->cost,
                'estimated_days' => $this->estimated_days,
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
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.shipping-methods', [
            'shippingMethods' => $shippingMethods,
        ]);
    }
}
