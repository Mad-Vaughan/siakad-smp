<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    /** @use HasFactory<\Database\Factories\PresenceFactory> */
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'academic_year_id',
        'date',
    ];

    protected static function booted()
    {
        static::creating(function ($presence) {
            $academicYear = Classroom::find($presence->classroom_id)->academicYear;
            $presence->academic_year_id = $academicYear->id;
        });

        static::created(function ($presence) {
            $presence->studentPresences()->createMany(
                $presence->classroom->students->map(function ($student) {
                    return ['student_id' => $student->id];
                })->toArray()
            );
        });
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function studentPresences()
    {
        return $this->hasMany(StudentPresence::class);
    }
}
