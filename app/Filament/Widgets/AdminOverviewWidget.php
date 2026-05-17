<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Presence;
use App\Models\Student; // 👈 TAMBAHAN
use App\Models\StudentClassroom; // 👈 TAMBAHAN
use App\Models\Subject;
use App\Models\Teacher;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AdminOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = Filament::auth()->user();
        $activeYear = AcademicYear::where('is_active', true)->first(); // 👈 KUNCI UTAMA

        // 👇 JALUR KHUSUS GURU 👇
        if ($user && $user->hasRole(['guru', 'teacher'])) {
            $totalMapelGuru = Subject::where('teacher_id', $user->id)->count();

            // Cuma narik kelas binaan di tahun ajaran aktif!
            $kelasBina = Classroom::where('teacher_id', $user->id)
                ->when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))
                ->pluck('name')->join(', ');

            $isWali = ! empty($kelasBina);
            $judulTugas = $isWali ? 'Wali Kelas' : 'Guru Mapel';

            $deskripsiKelas = $isWali ? 'Kelas: '.Str::limit($kelasBina, 18) : 'Tidak ada kelas binaan';

            $warnaWali = $isWali ? 'warning' : 'gray';
            $iconWali = $isWali ? 'heroicon-o-star' : 'heroicon-o-minus-circle';

            return [
                Stat::make('Selamat Datang!', $user->name)
                    ->description($activeYear ? 'T.A: '.$activeYear->name.' ('.ucfirst($activeYear->semester).')' : 'Sistem Nilai Dan Absensi')
                    ->descriptionIcon('heroicon-o-hand-raised')
                    ->color('success'),

                Stat::make('Mata Pelajaran', $totalMapelGuru)
                    ->description('Mapel yang Anda ampu')
                    ->descriptionIcon('heroicon-o-book-open')
                    ->color('info'),

                Stat::make('Tugas Tambahan', $judulTugas)
                    ->description($deskripsiKelas)
                    ->descriptionIcon($iconWali)
                    ->color($warnaWali),

                Stat::make('Status Akun', 'Aktif')
                    ->description('Akses Guru terverifikasi')
                    ->descriptionIcon('heroicon-o-check-badge')
                    ->color('primary'),
            ];
        }

        // 👇 JALUR ADMIN / TU (Difilter berdasar Tahun Aktif) 👇
        $totalStudents = Student::query()->count(); // Kalo mau siswa aktif aja, ganti ini. Tapi biasanya TU mau liat semua
        $activeStudents = $activeYear ? StudentClassroom::where('is_active', true)->whereHas('classroom', fn ($q) => $q->where('academic_year_id', $activeYear->id))->count() : 0;
        $totalTeachers = Teacher::query()->count();
        $totalClassrooms = $activeYear ? Classroom::where('academic_year_id', $activeYear->id)->count() : 0;
        $todayAttendances = Presence::query()->whereDate('date', Carbon::today())->count();

        $maleTeachers = Teacher::query()->whereIn('gender', ['L', 'l', 'male', 'Male', 'Laki-laki', 'laki-laki'])->count();
        $femaleTeachers = Teacher::query()->whereIn('gender', ['P', 'p', 'female', 'Female', 'Perempuan', 'perempuan'])->count();

        return [
            Stat::make('Siswa Aktif', $activeStudents)
                ->description('Dari Total '.$totalStudents.' Siswa')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary'),
            Stat::make('Total Guru', $totalTeachers)
                ->description('Pengajar aktif secara keseluruhan')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success'),
            Stat::make('Guru Laki-laki', $maleTeachers)
                ->description('Total pengajar pria')
                ->descriptionIcon('heroicon-o-user')
                ->color('info'),
            Stat::make('Guru Perempuan', $femaleTeachers)
                ->description('Total pengajar wanita')
                ->descriptionIcon('heroicon-o-user')
                ->color('warning'),
            Stat::make('Kelas Aktif', $totalClassrooms)
                ->description('Kelas berjalan semester ini')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('warning'),
            Stat::make('Absensi Hari Ini', $todayAttendances)
                ->description('Pertemuan yang tercatat')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('info'),
        ];
    }
}
