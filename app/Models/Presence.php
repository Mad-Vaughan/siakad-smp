<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'type',
        'classroom_id',
        'academic_year_id',
        'date',
    ];

    protected static function booted()
    {
        static::creating(function ($presence) {
            // MAGIC: Kalo absennya mapel, otomatis narik kelas & tahun ajaran dari jadwalnya
            if ($presence->type === 'mapel' && $presence->schedule_id) {
                $jadwal = Schedule::with('classroom.academicYear')->find($presence->schedule_id);
                if ($jadwal && $jadwal->classroom) {
                    $presence->classroom_id = $jadwal->classroom_id;
                    $presence->academic_year_id = $jadwal->classroom->academicYear->id;
                }
            }
            // Kalo absennya harian, narik tahun ajaran dari kelas yang dipilih
            elseif ($presence->type === 'harian' && $presence->classroom_id) {
                $kelas = Classroom::with('academicYear')->find($presence->classroom_id);
                if ($kelas && $kelas->academicYear) {
                    $presence->academic_year_id = $kelas->academicYear->id;
                }
            }
        });

        static::created(function ($presence) {
            $presence->studentPresences()->createMany(
                $presence->classroom->students->map(function ($student) {
                    return ['student_id' => $student->id];
                })->toArray()
            );
        });

        static::deleting(function ($presence) {
            $presence->studentPresences()->delete();
        });
    }

    // RELASI
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
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
