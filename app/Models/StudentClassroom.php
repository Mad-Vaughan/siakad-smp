<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassroom extends Model
{
    /** @use HasFactory<\Database\Factories\StudentClassroomFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'classroom_id',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($studentClassroom) {
            if ($studentClassroom->is_active) {
                StudentClassroom::where('student_id', $studentClassroom->student_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });

        static::updating(function ($studentClassroom) {
            if ($studentClassroom->is_active) {
                StudentClassroom::where('student_id', $studentClassroom->student_id)
                    ->where('is_active', true)
                    ->where('id', '!=', $studentClassroom->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
