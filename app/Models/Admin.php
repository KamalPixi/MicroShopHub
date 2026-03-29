<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements CanResetPassword
{
    use Notifiable, CanResetPasswordTrait;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role_id' => 'integer',
            'permissions' => 'array',
        ];
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->roleSlug() === 'super_admin') {
            return true;
        }

        if ($this->adminRole) {
            return $this->adminRole->hasPermission($permission);
        }

        return in_array($permission, $this->defaultPermissions(), true);
    }

    public function effectivePermissions(): array
    {
        if ($this->adminRole) {
            return $this->adminRole->permissions ?? [];
        }

        return $this->defaultPermissions();
    }

    public function defaultPermissions(): array
    {
        return config('admin_permissions.role_defaults.' . $this->roleSlug(), []);
    }

    public function roleSlug(): string
    {
        return $this->adminRole?->slug ?: ($this->role ?: 'editor');
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->adminRole?->name ?: ucfirst(str_replace('_', ' ', $this->roleSlug()));
    }

    public function adminRole()
    {
        return $this->belongsTo(AdminRole::class, 'role_id');
    }

    public function rolePermissions(): array
    {
        if ($this->adminRole) {
            return $this->adminRole->permissions ?? [];
        }

        return $this->defaultPermissions();
    }

}
