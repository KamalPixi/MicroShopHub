<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\AdminRole;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleId = AdminRole::where('slug', 'super_admin')->value('id');

        Admin::updateOrCreate(
            ['email' => 'admin@e.com'],
            [
                'name' => 'Admin',
                'role_id' => $roleId,
                'password' => Hash::make('password'),
            ]
        );
    }
}
