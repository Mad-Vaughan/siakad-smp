<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\AdminLogin;
use App\Filament\Widgets\AdminOverviewWidget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(AdminLogin::class)
            // 👇 JURUS SAT SET BIAR ADMIN BISA GANTI PASSWORD 👇
            ->profile()
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): string => Blade::render('<div style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: #6b7280;">Lupa password? <a href="https://wa.me/6281234567890?text=Halo%20Admin%20TU,%20saya%20lupa%20password%20akun%20SIAKAD%20saya." target="_blank" style="color: #2563EB; text-decoration: underline; font-weight: 600; transition: color 0.2s;">Hubungi TU di sini</a></div>') // masukin nomor tu
            )
            ->colors([
                'primary' => '#2563EB',
                // Lo juga bisa nambahin warna lain biar makin rapi
                'danger' => \Filament\Support\Colors\Color::Red,
                'gray' => \Filament\Support\Colors\Color::Slate,
                'info' => \Filament\Support\Colors\Color::Blue,
                'success' => \Filament\Support\Colors\Color::Emerald,
                'warning' => \Filament\Support\Colors\Color::Orange,
            ])
            // 👇 INI DIA JURUS SAKTI BIAR BAPAK GRUPNYA KAGA NYUNGSEP! 👇
            ->navigationGroups([
                'Manajemen Kelas & Mata Pelajaran',
                'Manajemen Penilaian',
                'Manajemen TU',
                'Manajemen Laporan',
                'Manajemen Pengguna',
                'Pelindung',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AdminOverviewWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
