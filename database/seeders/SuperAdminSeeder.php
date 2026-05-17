<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => Roles::ADMIN->value,
            'guard_name' => 'web',
        ]);

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sekolah.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'gender' => 'male',
                'address' => 'Sekolah SMP',
            ]
        );

        $superAdmin->assignRole(Roles::ADMIN->value);
    }
}
