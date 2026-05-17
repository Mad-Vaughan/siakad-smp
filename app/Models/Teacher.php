<?php

namespace App\Models;

use App\Enums\Roles;
use BackedEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends User
{
    use HasFactory;

    protected $table = 'users';

    protected static function booted()
    {
        static::addGlobalScope('teacher', function ($builder) {
            $builder->whereHas('roles', function ($query) {
                // 👇 JURUS ANTI ERROR PHP 8.4: Ambil nilainya langsung dengan aman 👇
                $roleValue = Roles::TEACHER instanceof BackedEnum ? Roles::TEACHER->value : Roles::TEACHER;

                // Cari semua variasi nama role biar kaga ada guru yang "gaib"
                $query->whereIn('name', [$roleValue, 'teacher', 'guru', 'Teacher', 'Guru']);
            });
        });

        static::created(function ($teacher) {
            $teacher->assignRole(Roles::TEACHER->value);
        });
    }

    public function getMorphClass()
    {
        return User::class;
    }
}
