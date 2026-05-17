<?php

namespace App\Filament\Parent\Pages\Auth;

use App\Models\User;
use Carbon\Carbon;
// use Filament\Forms\Components\Component; 👈 Ini gue buang biar kaga bikin PHP pusing
use Exception;
use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNisnFormComponent(),
                $this->getDobFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    // 👇 INI YANG DIBENERIN (Return type diganti langsung ke TextInput) 👇
    protected function getNisnFormComponent(): TextInput
    {
        return TextInput::make('nisn')
            ->label('NISN')
            ->required()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    // 👇 INI JUGA DIBENERIN 👇
    protected function getDobFormComponent(): TextInput
    {
        return TextInput::make('date_of_birth')
            ->label('Tanggal Lahir (Contoh: 16122010)')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    public function authenticate(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (\Filament\Http\Exceptions\TooManyRequestsException $exception) {
            $this->addError('nisn', __('filament-panels::pages/auth/login.messages.throttled', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => ceil($exception->secondsUntilAvailable / 60),
            ]));

            return null;
        }

        $data = $this->form->getState();
        $nisn = $data['nisn'];
        $dobInput = $data['date_of_birth'];

        // JURUS KONVERSI TANGGAL (Dari 01112013 menjadi 2013-11-01)
        $formattedDob = $dobInput;
        $cleanDob = preg_replace('/[^0-9]/', '', $dobInput);

        if (strlen($cleanDob) === 8) {
            try {
                $formattedDob = Carbon::createFromFormat('dmY', $cleanDob)->format('Y-m-d');
            } catch (Exception $e) {
                // Abaikan jika gagal parse, gunakan input asli
            }
        }

        // Cari user berdasarkan NISN & Tanggal Lahir murni
        $user = User::where('nisn', $nisn)
            ->where('date_of_birth', $formattedDob)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'data.nisn' => 'NISN atau Tanggal Lahir yang Anda masukkan salah. Silakan periksa kembali.',
            ]);
        }

        // Login bypass tanpa ngecek password Hash
        Filament::auth()->login($user, $data['remember'] ?? false);

        session()->regenerate();

        // 👇 INI JURUS CUCI OTAKNYA JON! MAKSA SETIR KE /parent 👇
        session()->forget('url.intended'); // 1. Hapus ingatan masa lalu
        session()->put('url.intended', url('/parent')); // 2. Tanam ingatan baru WAJIB ke /parent!
        // 👆 BATAS JURUS SAKTI 👆

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }

    // Ubah Action Button di bawah Form Login
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction()->label('Masuk'),
            Action::make('adminLogin')
                ->label('Masuk sebagai Admin / Guru')
                ->url(url('/admin/login'))
                ->color('gray')
                ->outlined(),
            Action::make('backToHome')
                ->label('Kembali ke Halaman Utama')
                ->url(url('/'))
                ->color('gray')
                ->outlined(),
        ];
    }
}
