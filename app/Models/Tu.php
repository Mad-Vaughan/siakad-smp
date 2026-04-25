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
                $query->where('name', Roles::TU);
            });
        });

        static::created(function ($tu) {
            $tu->assignRole(Roles::TU);
        });
    }

    public function getMorphClass()
    {
        return User::class;
    }
}
