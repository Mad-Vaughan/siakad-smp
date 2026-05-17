<?php

namespace App\Filament\Parent\Pages; // 👈 Alamatnya udah bener di panel Parent

use App\Models\Classroom;
use App\Models\Schedule;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class StudentSchedule extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    // 👇 Alamat tampilannya juga udah gue pindahin ke folder parent 👇
    protected string $view = 'filament.parent.pages.student-schedule';

    protected static ?string $navigationLabel = 'Jadwal Pelajaran';

    protected static ?string $title = 'Jadwal Pelajaran Saya';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('student');
    }

    protected function getViewData(): array
    {
        $user = auth()->user();

        // LOGIC DEWA: Otomatis nyari kelas khusus siswa yang lagi login!
        $kelasIdSiswa = DB::table('student_classrooms')
            ->where('student_id', $user->id)
            ->pluck('classroom_id')
            ->toArray();

        $activeClassroom = Classroom::whereIn('id', $kelasIdSiswa)
            ->whereHas('academicYear', function ($q) {
                $q->where('is_active', true);
            })->first();

        $schedules = collect();
        if ($activeClassroom) {
            // CUMA nampilin jadwal yang nyangkut sama kelasnya si siswa!
            $schedules = Schedule::where('classroom_id', $activeClassroom->id)
                ->with(['subject.teacher'])
                ->orderByRaw("CASE day 
                    WHEN 'Senin' THEN 1 
                    WHEN 'Selasa' THEN 2 
                    WHEN 'Rabu' THEN 3 
                    WHEN 'Kamis' THEN 4 
                    WHEN 'Jumat' THEN 5 
                    WHEN 'Sabtu' THEN 6 
                    ELSE 7 END")
                ->orderBy('start_time')
                ->get()
                ->groupBy('day');
        }

        return [
            'activeClassroom' => $activeClassroom,
            'schedules' => $schedules,
        ];
    }
}
