<?php

namespace App\Filament\Parent\Widgets;

use App\Models\StudentClassroom;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

// 👈 Panggil model kelas

class StudentOverviewWidget extends StatsOverviewWidget
{
    // Biar kotaknya muat banyak ke samping
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = Filament::auth()?->user();

        if (! $user) {
            return [
                Stat::make('Total Kehadiran', 0),
                Stat::make('Rata-rata Nilai', '0,00'),
                Stat::make('Jumlah Kejuaraan', 0),
            ];
        }

        // Cari kelas aktifnya
        $activeClass = StudentClassroom::where('student_id', $user->id)
            ->where('is_active', true)
            ->first();
        $className = $activeClass?->classroom?->name ?? 'Belum Ada Kelas';

        $totalPresence = $user->studentPresences()->count();
        $averageScore = (float) $user->studentAssesments()->avg('score');
        $totalChampionship = $user->championships()->count();

        return [
            // 👇 INI JURUS AKAL-AKALANNYA: PROFIL JADI KOTAK WIDGET! 👇
            Stat::make('PROFIL SISWA', $user->name)
                ->description("NISN: {$user->nisn} | Kelas: {$className}")
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('info'),

            // 👇 INI KOTAK BAWAAN LU 👇
            Stat::make('TOTAL KEHADIRAN', $totalPresence)
                ->description('Rekaman absensi siswa')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('RATA-RATA NILAI', number_format($averageScore, 2, ',', '.'))
                ->description('Semua penilaian aktif')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('success'),
        ];
    }
}
