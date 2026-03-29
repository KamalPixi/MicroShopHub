<?php

namespace App\Livewire\Admin;

use App\Models\AdminRole;
use Livewire\Component;
use Livewire\WithPagination;

class Roles extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public ?int $editingId = null;
    public string $name = '';
    public string $slug = '';
    public array $permissions = [];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash'],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ];
    }

    public function mount(): void
    {
        $this->permissions = config('admin_permissions.role_defaults.editor', []);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->permissions = config('admin_permissions.role_defaults.editor', []);
        $this->resetValidation();
    }

    public function save(): void
    {
        $rules = $this->rules();

        if ($this->editingId) {
            $rules['slug'] = ['required', 'string', 'max:255', 'alpha_dash', 'unique:admin_roles,slug,' . $this->editingId];
        } else {
            $rules['slug'] = ['required', 'string', 'max:255', 'alpha_dash', 'unique:admin_roles,slug'];
        }

        $this->validate($rules);

        AdminRole::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'slug' => $this->slug,
                'permissions' => array_values(array_intersect($this->permissions, $this->allPermissionKeys())),
            ]
        );

        session()->flash('message', $this->editingId ? 'Role updated successfully.' : 'Role created successfully.');
        $this->resetForm();
    }

    public function edit(int $roleId): void
    {
        $role = AdminRole::findOrFail($roleId);

        $this->editingId = $role->id;
        $this->name = $role->name;
        $this->slug = $role->slug;
        $this->permissions = $role->permissions ?? [];
    }

    public function delete(int $roleId): void
    {
        $role = AdminRole::findOrFail($roleId);

        if ($role->slug === 'super_admin') {
            session()->flash('failed', 'Super Admin role cannot be deleted.');
            return;
        }

        if ($role->admins()->exists()) {
            session()->flash('failed', 'This role is assigned to admins. Reassign them first.');
            return;
        }

        $role->delete();
        session()->flash('message', 'Role deleted successfully.');
        $this->resetForm();
    }

    public function render()
    {
        $roles = AdminRole::query()
            ->withCount('admins')
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%' . $this->search . '%')->orWhere('slug', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.admin.roles', [
            'roles' => $roles,
            'permissionGroups' => config('admin_permissions.groups', []),
        ]);
    }

    public function allPermissionKeys(): array
    {
        return collect(config('admin_permissions.groups', []))
            ->flatMap(fn ($group) => array_keys($group))
            ->values()
            ->all();
    }
}
