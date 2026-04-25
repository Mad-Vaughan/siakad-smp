<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends User
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $table = 'users';

    public function getMorphClass()
    {
        return User::class;
    }

    protected static function booted()
    {
        static::addGlobalScope('student', function ($builder) {
            $builder->whereHas('roles', function ($query) {
                $query->where('name', Roles::STUDENT);
            });
        });

        static::created(function ($student) {
            $student->assignRole(Roles::STUDENT);
        });
    }

    public function classroom()
    {
        return $this->hasOneThrough(
            Classroom::class,
            StudentClassroom::class,
            'student_id',
            'id',
            'id',
            'classroom_id'
        )->where('is_active', true);
    }
}
