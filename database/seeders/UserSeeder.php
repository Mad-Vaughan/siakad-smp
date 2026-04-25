<?php

namespace Database\Seeders;

use App\Enums\Roles;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::factory()->isAdmin()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole(Roles::ADMIN);

        $teachers = \App\Models\User::factory(10)->isTeacher()->create();
    }
}
