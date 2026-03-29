<?php

use App\Models\AdminRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            DB::table('admin_roles')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => ucwords(str_replace('_', ' ', $slug)),
                    'permissions' => json_encode($permissions),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained('admin_roles')->nullOnDelete();
        });

        foreach (DB::table('admins')->select('id', 'role')->get() as $admin) {
            $roleId = DB::table('admin_roles')->where('slug', $admin->role)->value('id');

            if ($roleId) {
                DB::table('admins')->where('id', $admin->id)->update(['role_id' => $roleId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('admin_roles');
    }
};
