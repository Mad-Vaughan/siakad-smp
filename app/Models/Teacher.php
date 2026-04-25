<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends User
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory;

    protected $table = 'users';

    protected static function booted()
    {
        static::addGlobalScope('teacher', function ($builder) {
            $builder->whereHas('roles', function ($query) {
                $query->where('name', Roles::TEACHER);
            });
        });

        static::created(function ($teacher) {
            $teacher->assignRole(Roles::TEACHER);
        });
    }

    public function getMorphClass()
    {
        return User::class;
    }
}
