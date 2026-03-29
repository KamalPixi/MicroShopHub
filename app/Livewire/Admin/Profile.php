<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $admin = Auth::guard('admin')->user();

        $this->name = $admin->name;
        $this->email = $admin->email;
    }

    public function save(): void
    {
        $admin = Auth::guard('admin')->user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin->id)],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password:admin'],
            'new_password' => ['nullable', 'min:8', 'confirmed'],
        ]);

        $admin->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ])->save();

        if (! empty($this->new_password)) {
            $admin->forceFill([
                'password' => $this->new_password,
                'remember_token' => str()->random(60),
            ])->save();
        }

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('message', 'Profile updated successfully.');
    }

    public function render()
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        $permissions = $admin->effectivePermissions();
        $permissionGroups = config('admin_permissions.groups', []);

        return view('livewire.admin.profile', [
            'admin' => $admin,
            'permissions' => $permissions,
            'permissionGroups' => $permissionGroups,
        ]);
    }
}
