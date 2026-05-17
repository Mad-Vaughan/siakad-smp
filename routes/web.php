<?php

use App\Http\Controllers\CetakController;
use App\Livewire\Pages\Home;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

if (app()->isLocal()) {
    Route::get('/bikin-admin', function () {
        try {
            User::updateOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('password123'),
                ]
            );

            return response('Akun admin lokal berhasil dibuat. Hapus rute ini sebelum produksi.', 201);
        } catch (\Throwable $e) {
            return response('Gagal membuat akun admin: '.$e->getMessage(), 500);
        }
    });
}

// Cetak rekap final untuk kelas dan tahun yang dipilih
Route::get('/cetak-rekap-final/{classroom}/{year}', [CetakController::class, 'rekapFinal'])->name('cetak.rekap.final');

// Jalur buat nyetak rekap absen 1 mata pelajaran
Route::get('/cetak/rekap-mapel', [CetakController::class, 'cetakRekapMapel'])->name('cetak.rekap.mapel');
