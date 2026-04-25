<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends User
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory;

    protected $table = 'users';

    protected static function booted()
    {
        static::addGlobalScope('admin', function ($builder) {
            $builder->whereHas('roles', function ($query) {
                $query->where('name', Roles::ADMIN);
            });
        });

        static::created(function ($admin) {
            $admin->assignRole(Roles::ADMIN);
        });
    }
}
