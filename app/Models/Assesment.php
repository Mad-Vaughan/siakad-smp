<?php

namespace App\Models;

use App\Enums\AssesmentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assesment extends Model
{
    /** @use HasFactory<\Database\Factories\AssessmentFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => AssesmentType::class,
    ];

    protected static function booted()
    {
        static::creating(function ($assesment) {
            $academicYearId = Classroom::find($assesment->classroom_id)->academic_year_id;
            $assesment->academic_year_id = $academicYearId;
        });

        static::created(function ($assesment) {
            $assesment->studentAssesments()->createMany(
                $assesment->classroom->students->map(function ($student) {
                    return ['student_id' => $student->id];
                })->toArray()
            );
        });

    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function studentAssesments()
    {
        return $this->hasMany(StudentAssesment::class);
    }
}
