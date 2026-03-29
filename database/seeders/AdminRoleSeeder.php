<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = config('admin_permissions.role_defaults', []);

        foreach ($defaults as $slug => $permissions) {
            AdminRole::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => ucwords(str_replace('_', ' ', $slug)),
                    'permissions' => $permissions,
                ]
            );
        }
    }
}
