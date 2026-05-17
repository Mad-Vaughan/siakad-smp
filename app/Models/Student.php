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
                $query->where('name', Roles::STUDENT->value);
            });
        });

        static::created(function ($student) {
            $student->assignRole(Roles::STUDENT->value);
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
        )->where('student_classrooms.is_active', true);
    }

    public function studentClassrooms()
    {
        return $this->hasMany(StudentClassroom::class, 'student_id');
    }
}
