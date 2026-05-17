<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    // Biar semua kolom bisa diisi
    protected $guarded = [];

    // 👇 INI DIA YANG DITANGISI SAMA ERROR LU JON! 👇
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // 👇 INI JUGA WAJIB ADA BIAR KAGA ERROR MAPEL 👇
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // 👇 SCOPE UNTUK SORTING JADWAL BERDASARKAN KELAS, HARI & JAM 👇
    public function scopeOrderByDayAndTime($query)
    {
        return $query->orderBy('classroom_id')
            ->orderByRaw("
                CASE 
                    WHEN day = 'Senin' THEN 1
                    WHEN day = 'Selasa' THEN 2
                    WHEN day = 'Rabu' THEN 3
                    WHEN day = 'Kamis' THEN 4
                    WHEN day = 'Jumat' THEN 5
                    WHEN day = 'Sabtu' THEN 6
                    ELSE 7
                END
            ")
            ->orderBy('start_time');
    }
}
