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
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        if (is_null($this->getRawOriginal('permissions'))) {
            return in_array($permission, $this->defaultPermissions(), true);
        }

        return in_array($permission, $this->permissions ?? [], true);
    }

    public function effectivePermissions(): array
    {
        if (is_null($this->getRawOriginal('permissions'))) {
            return $this->defaultPermissions();
        }

        return $this->permissions ?? [];
    }

    public function defaultPermissions(): array
    {
        return config('admin_permissions.role_defaults.' . $this->role, []);
    }

}
