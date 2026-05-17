<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // Navigation label displayed in the sidebar menu
    protected static ?string $navigationLabel = 'Beranda';

    // Page title displayed as the main heading
    protected static ?string $title = 'Beranda Utama';
}
