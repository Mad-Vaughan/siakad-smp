<?php

use App\Livewire\Pages\About;
use App\Livewire\Pages\Achievements;
use App\Livewire\Pages\Activities;
use App\Livewire\Pages\Admission;
use App\Livewire\Pages\Contact;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\News;
use App\Livewire\Pages\Programs;
use App\Livewire\Pages\TuRegister;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CetakController; // 👇 INI WAJIB ADA JON 👇

Route::get('/', Home::class)->name('home');
Route::get('/profil-sekolah', About::class)->name('about');
Route::get('/program', Programs::class)->name('programs');
Route::get('/prestasi', Achievements::class)->name('achievements');
Route::get('/kegiatan', Activities::class)->name('activities');
Route::get('/berita', News::class)->name('news');
Route::get('/hubungi-kami', Contact::class)->name('contact');
Route::get('/ppdb', Admission::class)->name('admission');
Route::get('/tu-register', [TuRegister::class, '__invoke'])->name('tu-register');

// 👇 INI ROUTE CETAK YANG BENER SESUAI CONTROLLER LO 👇
Route::get('/cetak-rekap-final/{classroom}/{year}', [CetakController::class, 'rekapFinal'])->name('cetak.rekap.final');