<?php

namespace App\Enums\Filament;

enum NavigationGrouping: string
{
    case ClassAndSubjectManagement = 'Class & Subject Management';
    case UserManagement = 'User Management';
    case AssesmentAndChampionshipManagement = 'Assesment & Championship Management';
    case ReportManagement = 'Report Management';

    public function getLabel(): string
    {
        return match ($this) {
            self::ClassAndSubjectManagement => 'Manajemen Kelas & Mata Pelajaran',
            self::UserManagement => 'Manajemen Pengguna',
            self::AssesmentAndChampionshipManagement => 'Manajemen Penilaian',
            self::ReportManagement => 'Manajemen Laporan',
        };
    }
}
