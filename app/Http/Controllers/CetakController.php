<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Presence;
use App\Models\Schedule; 
use App\Models\Student; 
use App\Models\StudentClassroom;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Http\Request;

class CetakController extends Controller
{
    // ==============================================================
    // 1. REKAP HARIAN (JURUS SINKRONISASI TOTAL JON!)
    // ==============================================================
    public function rekapFinal(Request $request, $classroom_id, $year_id = null)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        $academicYear = $classroom->academicYear;
        $semester = $academicYear->semester;

        // 👇 PENYAKIT 1 FIX: Buang where('is_active', true) biar histori siswa masa lalu tetep ketarik!
        $studentIds = StudentClassroom::query()
            ->where('classroom_id', $classroom->id)
            ->pluck('student_id')
            ->toArray();

        $students = Student::query()
            ->whereIn('id', $studentIds)
            ->orderBy('name')
            ->get();

        foreach ($students as $student) {
            // 👇 PENYAKIT 2 FIX: Kita hitung langsung di Database! Persis kaya kodingan RekapitulasiResource lu!
            $baseQuery = \App\Models\StudentPresence::where('student_id', $student->id)
                ->whereHas('presence', function ($q) use ($classroom) {
                    $q->where('classroom_id', $classroom->id)
                      ->where('type', 'harian'); 
                });

            // Hitung pake whereIn biar aman dari miss-translate bahasa Inggris/Indonesia
            $student->total_h = (clone $baseQuery)->whereIn('status', ['present', 'hadir', 'Hadir'])->count();
            $student->total_s = (clone $baseQuery)->whereIn('status', ['sick', 'sakit', 'Sakit'])->count();
            $student->total_i = (clone $baseQuery)->whereIn('status', ['permission', 'izin', 'Izin'])->count();
            $student->total_a = (clone $baseQuery)->whereIn('status', ['absent', 'late', 'alpa', 'terlambat'])->count();

            // Rata-rata nilai
            $student->rata_nilai = round(\App\Models\StudentAssesment::where('student_id', $student->id)
                ->whereHas('assessment', function ($q) use ($classroom) {
                    $q->where('classroom_id', $classroom->id);
                })->avg('score') ?? 0, 2);
        }

        return view('print.rekap-tu', compact('students', 'classroom', 'academicYear', 'semester'));
    }

    // ==============================================================
    // 2. REKAP MAPEL (LANDSCAPE ANTI GEPENG!)
    // ==============================================================
    public function cetakRekapMapel(Request $request)
    {
        $scheduleId = $request->query('schedule');

        $schedule = Schedule::with(['classroom.students', 'subject'])->findOrFail($scheduleId);
        $students = $schedule->classroom->students()->orderBy('name', 'asc')->get();
        
        $pertemuan = Presence::where('schedule_id', $scheduleId)
            ->where('type', 'mapel')
            ->orderBy('date', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.rekap_mapel', compact('schedule', 'students', 'pertemuan'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Rekap_Mapel_'.$schedule->subject->name.'.pdf');
    }
}