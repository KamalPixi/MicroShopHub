<?php

namespace App\Livewire\Admin;

use App\Models\Discount;
use Livewire\Component;
use Livewire\WithPagination;

class Discounts extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $code = '';
    public $type = 'percentage';
    public $value = null;
    public $min_order_amount = 0;
    public $usage_limit = null;
    public $per_user_limit = null;
    public $starts_at = null;
    public $expires_at = null;
    public $active = true;
    public $editingId = null;

    protected $rules = [
        'code' => 'required|string|max:255|unique:discounts,code',
        'type' => 'required|in:percentage,fixed,free_shipping',
        'value' => 'nullable|numeric|min:0',
        'min_order_amount' => 'required|numeric|min:0',
        'usage_limit' => 'nullable|integer|min:1',
        'per_user_limit' => 'nullable|integer|min:1',
        'starts_at' => 'nullable|date|before:expires_at',
        'expires_at' => 'nullable|date|after:starts_at',
        'active' => 'boolean',
    ];

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedType($value)
    {
        if ($value === 'free_shipping') {
            $this->value = null;
        }
    }

    public function resetForm()
    {
        $this->code = '';
        $this->type = 'percentage';
        $this->value = null;
        $this->min_order_amount = 0;
        $this->usage_limit = null;
        $this->per_user_limit = null;
        $this->starts_at = null;
        $this->expires_at = null;
        $this->active = true;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function save()
    {
        if ($this->editingId) {
            $this->rules['code'] = 'required|string|max:255|unique:discounts,code,' . $this->editingId;
        }

        if ($this->type !== 'free_shipping') {
            $this->rules['value'] = 'required|numeric|min:0';
        }

        $this->validate();

        Discount::updateOrCreate(
            ['id' => $this->editingId],
            [
                'code' => $this->code,
                'type' => $this->type,
                'value' => $this->type === 'free_shipping' ? null : $this->value,
                'min_order_amount' => $this->min_order_amount,
                'usage_limit' => $this->usage_limit,
                'per_user_limit' => $this->per_user_limit,
                'starts_at' => $this->starts_at,
                'expires_at' => $this->expires_at,
                'active' => $this->active,
            ]
        );

        session()->flash('message', $this->editingId ? 'Coupon updated successfully.' : 'Coupon created successfully.');
        $this->resetForm();
    }

    public function edit($discountId)
    {
        $discount = Discount::findOrFail($discountId);
        $this->editingId = $discount->id;
        $this->code = $discount->code;
        $this->type = $discount->type;
        $this->value = $discount->value;
        $this->min_order_amount = $discount->min_order_amount;
        $this->usage_limit = $discount->usage_limit;
        $this->per_user_limit = $discount->per_user_limit;
        $this->starts_at = $discount->starts_at ? $discount->starts_at->format('Y-m-d\TH:i') : null;
        $this->expires_at = $discount->expires_at ? $discount->expires_at->format('Y-m-d\TH:i') : null;
        $this->active = $discount->active;
    }

    public function deleteDiscount($discountId)
    {
        Discount::findOrFail($discountId)->delete();
        session()->flash('message', 'Coupon deleted successfully.');
        $this->resetForm();
    }

    public function render()
    {
        $discounts = Discount::query()
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.discounts', [
            'discounts' => $discounts,
        ]);
    }
}
