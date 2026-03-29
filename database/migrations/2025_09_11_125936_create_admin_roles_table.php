<?php

use App\Models\AdminRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

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

    public function down(): void
    {
        Schema::dropIfExists('admin_roles');
    }
};
