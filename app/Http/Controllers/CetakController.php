<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\StudentClassroom;
use App\Models\StudentPresence;
use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function rekapFinal($classroom_id, $year_id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        $academicYear = AcademicYear::findOrFail($year_id);
        
        // 👇 JURUS PAMUNGKAS: Cari ID Siswa lewat tabel perantara (StudentClassroom) 👇
        $studentIds = StudentClassroom::where('classroom_id', $classroom_id)
            ->where('is_active', true) // Ambil yang kelasnya lagi aktif aja
            ->pluck('student_id');

        // Ambil data siswanya berdasarkan ID yang ketemu
        $students = Student::whereIn('id', $studentIds)->get();

        if ($students->isEmpty()) {
            return "Waduh Jon, di tabel 'student_classrooms' kaga ada siswa yang aktif di kelas " . $classroom->name . ".";
        }

        foreach ($students as $student) {
            // 👇 HITUNG ABSENSI PAKE LOGIC RELASI LO 👇
            // Kita ambil dari StudentPresence, tapi di-filter berdasarkan tanggal di tabel induknya (Presence)
            $presences = StudentPresence::where('student_id', $student->id)
                ->whereHas('presence', function ($q) use ($academicYear) {
                    $q->whereBetween('date', [$academicYear->start_date, $academicYear->end_date]);
                })->get();

            // Pake Accessor (hadir, sakit, dll) yang udah lo buat cantik-cantik di model StudentPresence
            $student->total_h = $presences->filter(fn($p) => $p->hadir)->count();
            $student->total_s = $presences->filter(fn($p) => $p->sakit)->count();
            $student->total_i = $presences->filter(fn($p) => $p->izin)->count();
            $student->total_a = $presences->filter(fn($p) => $p->alpa || $p->terlambat)->count(); // Telat gue gabung alpa ya, atau sesuaikan aja

            // Rata-rata nilai (Set 0 dulu sampe lo fix bikin sistem Penilaian)
            // Hitung Rata-rata nilai berdasarkan Tahun Ajaran yang dicetak
            // Kita numpang query ke relasi 'assessment' (Tabel Induk Penilaian)
            $student->rata_nilai = \App\Models\StudentAssesment::where('student_id', $student->id)
                ->whereHas('assessment', function($q) use ($academicYear) {
                    $q->where('academic_year_id', $academicYear->id);
                })
                ->avg('score') ?? 0; // KALO KOLOM NILAI LO BUKAN 'score', ganti jadi 'nilai' atau 'point' ya Jon!
        }

        return view('print.rekap-tu', compact('students', 'classroom', 'academicYear'));
    }
}