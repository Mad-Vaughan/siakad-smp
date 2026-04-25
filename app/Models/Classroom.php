<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    /** @use HasFactory<\Database\Factories\ClassroomFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'academic_year_id',
        'teacher_id',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studentClassrooms(): HasMany
    {
        return $this->hasMany(StudentClassroom::class, 'classroom_id');
    }

    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            StudentClassroom::class,
            'classroom_id',
            'id',
            'id',
            'student_id'
        );
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function assesments(): HasMany
    {
        return $this->hasMany(Assesment::class);
    }
}
