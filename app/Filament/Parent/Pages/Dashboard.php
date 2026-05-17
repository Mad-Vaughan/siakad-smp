<?php

namespace App\Filament\Parent\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Beranda';

    protected static ?string $navigationLabel = 'Beranda';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    /**
     * Mengatur Judul Utama (Heading) Dashboard
     */
    public function getHeading(): string
    {
        // Mengambil nama user yang sedang login (Orang Tua/Wali)
        $nama = auth()->user()->name;

        return "Selamat Datang, Bapak/Ibu {$nama}!";
    }

    /**
     * Mengatur Sub-judul (Subheading) Dashboard
     */
    public function getSubheading(): ?string
    {
        return 'Melalui halaman ini, Anda dapat memantau kehadiran dan perkembangan nilai buah hati Anda secara real-time.';
    }

    public function getTitle(): string
    {
        return 'Beranda';
    }
}
