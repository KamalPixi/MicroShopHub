<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->with('defaultAddress')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.customer-list', [
            'customers' => $customers,
        ]);
    }

    public function deleteCustomer($customerId)
    {
        User::findOrFail($customerId)->delete();
        session()->flash('message', 'Customer deleted successfully.');
    }
}
