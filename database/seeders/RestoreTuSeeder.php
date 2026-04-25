<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Tu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RestoreTuSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure role exists
        Role::firstOrCreate(['name' => Roles::TU->value]);

        // Default TU account
        $email = 'tu@example.com';

        $tu = Tu::withoutGlobalScopes()->where('email', $email)->first();

        if (! $tu) {
            $tu = Tu::create([
                'name' => 'Tata Usaha',
                'email' => $email,
                'password' => Hash::make('password'),
            ]);
        } else {
            // ensure role assigned
            if (! $tu->hasRole(Roles::TU->value)) {
                $tu->assignRole(Roles::TU->value);
            }
        }

        $this->command?->info("TU account available: {$tu->email} (password: password)");
    }
}
