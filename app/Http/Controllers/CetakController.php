<?php

namespace App\Http\Controllers;

use App\Enums\PresenceStatus;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Presence;
use App\Models\Schedule; // 👈 Wajib import buat Mapel
use App\Models\Student; // 👈 Wajib import buat Mapel
use App\Models\StudentClassroom;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

// 👈 Mesin PDF-nya

class CetakController extends Controller
{
    // ==============================================================
    // 1. REKAP HARIAN (FUNGSI BAWAAN LU YANG UDAH DIPERBAIKI)
    // ==============================================================
    public function rekapFinal(Request $request, $classroom_id, $year_id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        $academicYear = AcademicYear::findOrFail($year_id);
        $semester = $academicYear->semester;

        // Cari ID siswa yang aktif di kelas & tahun ajaran tersebut
        $studentIds = StudentClassroom::query()
            ->where('classroom_id', $classroom->id)
            ->where('is_active', true)
            ->pluck('student_id')
            ->toArray();

        // Tarik data siswa
        $students = Student::query()
            ->whereIn('id', $studentIds)
            ->orderBy('name')
            ->get();

        foreach ($students as $student) {
            // 1. Tarik Data Presensi pakai Query biar Aman
            $attendances = \App\Models\StudentPresence::where('student_id', $student->id)
                ->whereHas('presence', function ($q) use ($classroom, $academicYear) {
                    $q->where('classroom_id', $classroom->id)
                        ->where('academic_year_id', $academicYear->id)
                        ->where('type', 'harian'); // 👈 INI OBAT ANTI INFLASINYA JON!
                })->get();

            $student->total_h = $attendances->filter(fn ($p) => $p->status === PresenceStatus::PRESENT)->count();
            $student->total_s = $attendances->filter(fn ($p) => $p->status === PresenceStatus::SICK)->count();
            $student->total_i = $attendances->filter(fn ($p) => $p->status === PresenceStatus::PERMISSION)->count();
            $student->total_a = $attendances->filter(fn ($p) => in_array($p->status, [PresenceStatus::ABSENT, PresenceStatus::LATE], true))->count();

            // 2. Tarik Rata-Rata Nilai pakai Query
            $student->rata_nilai = round(\App\Models\StudentAssesment::where('student_id', $student->id)
                ->whereHas('assessment', function ($q) use ($classroom, $academicYear) {
                    $q->where('classroom_id', $classroom->id)
                        ->where('academic_year_id', $academicYear->id);
                })->avg('score') ?? 0, 2);
        }

        return view('print.rekap-tu', compact('students', 'classroom', 'academicYear', 'semester'));
    }

    // ==============================================================
    // 2. REKAP MAPEL (FUNGSI BARU BUAT PDF)
    // ==============================================================
    public function cetakRekapMapel(Request $request)
    {
        $scheduleId = $request->query('schedule');

        $schedule = Schedule::with(['classroom.students', 'subject'])->findOrFail($scheduleId);
        $students = $schedule->classroom->students()->orderBy('name', 'asc')->get();
        $pertemuan = Presence::where('schedule_id', $scheduleId)->where('type', 'mapel')->orderBy('date', 'asc')->get();

        $pdf = Pdf::loadView('pdf.rekap_mapel', compact('schedule', 'students', 'pertemuan'))
            ->setPaper('a4', 'landscape'); // Kertas A4 Tidur

        return $pdf->stream('Rekap_Mapel_'.$schedule->subject->name.'.pdf');
    }
}
