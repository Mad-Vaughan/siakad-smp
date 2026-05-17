<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tu extends User
{
    /** @use HasFactory<\Database\Factories\TuFactory> */
    use HasFactory;

    protected $table = 'users';

    protected static function booted()
    {
        static::addGlobalScope('tu', function ($builder) {
            $builder->whereHas('roles', function ($query) {
                // 👇 UDAH DITAMBAHIN ->value 👇
                $query->where('name', Roles::TU->value);
            });
        });

        static::created(function ($tu) {
            // 👇 UDAH DITAMBAHIN ->value 👇
            $tu->assignRole(Roles::TU->value);
        });
    }

    public function getMorphClass()
    {
        return User::class;
    }
}
