<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicYearFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'seq',
    ];

    protected static function booted()
    {
        static::creating(function ($academicYear) {
            $academicYear->seq = AcademicYear::max('seq') + 1;
            if ($academicYear->is_active) {
                AcademicYear::where('is_active', true)->update(['is_active' => false]);
            }
        });

        static::updating(function ($academicYear) {
            if ($academicYear->is_active) {
                AcademicYear::where('is_active', true)->where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            }
        });
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
