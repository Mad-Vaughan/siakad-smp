<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends User
{
    use HasFactory;

    protected $table = 'users';

    // 👇 INI OBAT 404-NYA JON 👇
    // Biar Spatie nyatet role dan bacanya tetep sebagai "User"
    public function getMorphClass()
    {
        return User::class;
    }

    protected static function booted()
    {
        static::addGlobalScope('admin', function ($builder) {
            $builder->whereHas('roles', function ($query) {
                $query->where('name', Roles::ADMIN->value);
            });
        });

        static::creating(function ($admin) {
            if (empty($admin->password)) {
                $admin->password = 'password';
            }
        });

        static::created(function ($admin) {
            // 👇 Karena udah ada getMorphClass, kita bisa langsung sikat gini aja
            $admin->assignRole(Roles::ADMIN->value);
        });
    }
}
