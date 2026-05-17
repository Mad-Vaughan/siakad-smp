<?php

namespace App\Filament\Pages;

use App\Models\Schedule;
use BackedEnum;
use Filament\Pages\Page;

class TeacherSchedule extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected string $view = 'filament.pages.teacher-schedule';

    protected static ?string $navigationLabel = 'Jadwal Mengajar';

    protected static ?string $title = 'Jadwal Mengajar Saya';

    // 👇 SATPAM KHUSUS GURU 👇
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('teacher');
    }

    protected function getViewData(): array
    {
        $user = auth()->id();

        // Cari semua jadwal yang Gurunya adalah User yang lagi login
        // Dan cuma di Tahun Ajaran yang lagi aktif
        $schedules = Schedule::query()
            ->whereHas('subject', fn ($q) => $q->where('teacher_id', $user))
            ->whereHas('classroom.academicYear', fn ($q) => $q->where('is_active', true))
            ->with(['classroom', 'subject'])
            ->get()
            ->sortBy(function ($schedule) {
                $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];

                return $days[$schedule->day] ?? 7;
            })
            ->groupBy('day');

        return [
            'schedules' => $schedules,
        ];
    }
}
