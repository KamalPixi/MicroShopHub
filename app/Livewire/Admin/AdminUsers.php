<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUsers extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'editor';
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:admins,email',
        'password' => 'required|string|min:8',
        'role' => 'required|in:super_admin,editor,viewer',
    ];

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'editor';
        $this->editingId = null;
        $this->resetValidation();
    }

    public function save()
    {
        if ($this->editingId) {
            $this->rules['email'] = 'required|email|max:255|unique:admins,email,' . $this->editingId;
            $this->rules['password'] = 'nullable|string|min:8';
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password && !$this->editingId) {
            $data['password'] = Hash::make($this->password);
        } elseif ($this->password && $this->editingId) {
            $data['password'] = Hash::make($this->password);
        }

        Admin::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        session()->flash('message', $this->editingId ? 'Admin updated successfully.' : 'Admin created successfully.');
        $this->resetForm();
    }

    public function edit($adminId)
    {
        $admin = Admin::findOrFail($adminId);
        $this->editingId = $admin->id;
        $this->name = $admin->name;
        $this->email = $admin->email;
        $this->role = $admin->role;
        $this->password = '';
    }

    public function deleteAdmin($adminId)
    {
        Admin::findOrFail($adminId)->delete();
        session()->flash('message', 'Admin deleted successfully.');
        $this->resetForm();
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

        return view('livewire.admin.admin-users', [
            'admins' => $admins,
        ]);
    }
}
