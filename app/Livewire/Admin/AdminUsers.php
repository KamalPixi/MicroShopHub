<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use App\Models\AdminRole;
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
    public $role_id = null;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:admins,email',
        'password' => 'required|string|min:8',
        'role_id' => 'required|exists:admin_roles,id',
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
        $this->role_id = $this->defaultRoleId('editor');
        $this->editingId = null;
        $this->resetValidation();
    }

    public function mount(): void
    {
        $this->role_id = $this->defaultRoleId('editor');
    }

    public function save()
    {
        if ($this->editingId) {
            $this->rules['email'] = 'required|email|max:255|unique:admins,email,' . $this->editingId;
            $this->rules['password'] = 'nullable|string|min:8';
        }

        $this->validate();

        $role = AdminRole::findOrFail($this->role_id);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $role->id,
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
        $this->role_id = $admin->role_id ?: $this->defaultRoleId($admin->roleSlug());
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
            ->with('adminRole')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.admin-users', [
            'admins' => $admins,
            'roles' => AdminRole::query()->orderBy('name')->get(),
        ]);
    }

    public function defaultRoleId(string $slug): ?int
    {
        return AdminRole::where('slug', $slug)->value('id')
            ?: AdminRole::query()->orderBy('id')->value('id');
    }
}
