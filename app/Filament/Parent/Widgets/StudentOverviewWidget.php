<?php

namespace App\Filament\Parent\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentOverviewWidget extends StatsOverviewWidget
{
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

        $totalPresence = $user->studentPresences()->count();
        $averageScore = (float) $user->studentAssesments()->avg('score');
        $totalChampionship = $user->championships()->count();

        return [
            Stat::make('Total Kehadiran', $totalPresence)
                ->description('Rekaman absensi siswa')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),
            Stat::make('Rata-rata Nilai', number_format($averageScore, 2, ',', '.'))
                ->description('Semua penilaian aktif')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('success'),
            Stat::make('Jumlah Kejuaraan', $totalChampionship)
                ->description('Prestasi yang tercatat')
                ->descriptionIcon('heroicon-o-trophy')
                ->color('warning'),
        ];
    }
}
