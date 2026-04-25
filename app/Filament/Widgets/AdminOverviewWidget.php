<?php

namespace App\Filament\Widgets;

use App\Models\Classroom;
use App\Models\Presence;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AdminOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalStudents = Student::query()->count();
        $totalTeachers = Teacher::query()->count();
        $totalClassrooms = Classroom::query()->count();
        $todayAttendances = Presence::query()
            ->whereDate('date', Carbon::today())
            ->count();

        return [
            Stat::make('Total Siswa', $totalStudents)
                ->description('Siswa terdaftar')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary'),
            Stat::make('Total Guru', $totalTeachers)
                ->description('Pengajar aktif')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success'),
            Stat::make('Total Kelas', $totalClassrooms)
                ->description('Kelas yang berjalan')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('warning'),
            Stat::make('Absensi Hari Ini', $todayAttendances)
                ->description('Pertemuan yang tercatat')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('info'),
        ];
    }
}
