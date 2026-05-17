<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassroom extends Model
{
    use HasFactory;

    // 👇 INI BIANG KEROKNYA! Harus didaftarin biar Seeder bisa ngisi status aktifnya 👇
    protected $fillable = [
        'student_id',
        'classroom_id',
        'is_active',
    ];

    // 👇 OBAT SILANG MERAH: Taruhnya di sini Jon, bukan di model Kelas 👇
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function student()
    {
        // Sesuaikan sama relasi lu, pake User::class atau Student::class
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
