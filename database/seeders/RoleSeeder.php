<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Support\RolePermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        collect(Roles::cases())->each(function (Roles $role): void {
            Role::updateOrCreate([
                'name' => $role->value,
                'guard_name' => 'web',
            ]);
        });

        $allPermissions = Permission::all();
        $adminRole = Role::findByName(Roles::ADMIN->value);
        $adminRole->syncPermissions($allPermissions);

        $teacherPermissions = Permission::query()
            ->whereIn('name', RolePermissions::teacher())
            ->get();

        Role::findByName(Roles::TEACHER->value)->syncPermissions($teacherPermissions);
    }
}
