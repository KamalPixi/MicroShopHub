<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
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
        $admins = Admin::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.user-list', [
            'admins' => $admins,
        ]);
    }

    public function deleteAdmin($adminId)
    {
        Admin::findOrFail($adminId)->delete();
        session()->flash('message', 'Admin user deleted successfully.');
    }
}
