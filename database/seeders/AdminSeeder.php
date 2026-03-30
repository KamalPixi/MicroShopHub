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
        $installerSettings = session('installer.settings', []);
        $name = trim((string) ($installerSettings['admin_name'] ?? 'Admin')) ?: 'Admin';
        $email = trim((string) ($installerSettings['admin_email'] ?? 'admin@e.com')) ?: 'admin@e.com';
        $password = (string) ($installerSettings['admin_password'] ?? 'password');

        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role_id' => $roleId,
                'password' => Hash::make($password),
            ]
        );

        if ($email !== 'admin@e.com') {
            Admin::where('email', 'admin@e.com')->where('email', '!=', $email)->delete();
        }
    }
}
