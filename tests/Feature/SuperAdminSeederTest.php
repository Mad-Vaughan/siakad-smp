<?php

use App\Models\User;
use Database\Seeders\SuperAdminSeeder;

it('creates the super admin user with admin role', function () {
    $this->seed(SuperAdminSeeder::class);

    $superAdmin = User::where('email', 'superadmin@sekolah.com')->first();

    expect($superAdmin)->not->toBeNull();
    expect($superAdmin->name)->toBe('Super Admin');
    expect($superAdmin->hasRole('admin'))->toBeTrue();
});
