<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // Ini buat nama di menu samping
    protected static ?string $navigationLabel = 'Beranda'; 
    
    // Ini buat judul gede di halamannya
    protected static ?string $title = 'Beranda Utama'; 
}