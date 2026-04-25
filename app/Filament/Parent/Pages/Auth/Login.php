<?php

namespace App\Filament\Parent\Pages\Auth;

use App\Models\Student; // 👈 JURUS PINDAH RUMAH: Nyari datanya di tabel Siswa, bukan tabel User/Admin!
use Carbon\Carbon;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nisn')
                    ->label('NISN')
                    ->numeric()
                    ->required()
                    ->autocomplete('username')
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
                TextInput::make('date_of_birth')
                    ->label('Tanggal Lahir (hhbbyyyy)')
                    ->required()
                    ->placeholder('Contoh: 01022010')
                    ->minLength(8)
                    ->maxLength(10)
                    ->extraInputAttributes([
                        'tabindex' => 2,
                        'inputmode' => 'numeric',
                    ]),
                $this->getRememberFormComponent(),
            ]);
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $data = $this->form->getState();

        $nisnInput = (string) ($data['nisn'] ?? '');
        $nisnVariants = array_unique([
            $nisnInput,
            ltrim($nisnInput, '0'),
            str_pad(ltrim($nisnInput, '0'), 10, '0', STR_PAD_LEFT),
        ]);

        $user = \App\Models\Student::query()
            ->whereIn('nisn', array_filter($nisnVariants))
            ->first();

        $isPasswordValid = false;

        if ($user) {
            $inputTgl = trim($data['date_of_birth'] ?? '');
            $formattedTgl = null;
            
            // Ubah ketikan ortu (01012010) jadi standar (2010-01-01)
            if (strlen($inputTgl) === 8 && is_numeric($inputTgl)) {
                $hari = substr($inputTgl, 0, 2);
                $bulan = substr($inputTgl, 2, 2);
                $tahun = substr($inputTgl, 4, 4);
                $formattedTgl = "$tahun-$bulan-$hari";
            } elseif (strlen($inputTgl) >= 8) {
                $parsed = $this->resolveBirthDate($inputTgl);
                if ($parsed) $formattedTgl = $parsed->toDateString();
            }

            // 👇 JURUS PENYAMAAN TANGGAL EXCEL VS STANDAR 👇
            $dbDateRaw = (string) $user->date_of_birth;
            
            try {
                // Kita paksa baca tanggal aneh dari Excel (1/1/2010) jadi standar (2010-01-01)
                $dbDateStandard = \Carbon\Carbon::parse($dbDateRaw)->format('Y-m-d');
            } catch (\Throwable $e) {
                $dbDateStandard = $dbDateRaw;
            }

            if ($formattedTgl) {
                // Cek Password Hash ATAU Tanggal Lahir yang udah disamain formatnya
                if (\Illuminate\Support\Facades\Hash::check($formattedTgl, $user->password) || 
                    \Illuminate\Support\Facades\Hash::check($dbDateRaw, $user->password) || 
                    $dbDateStandard === $formattedTgl) {
                    $isPasswordValid = true;
                }
            }
        }

        if (! $user || ! $isPasswordValid) {
            \Illuminate\Support\Facades\Log::warning('Parent login failed', [
                'nisn' => $nisnInput,
                'input_dob' => $data['date_of_birth'] ?? null,
            ]);
            $this->throwFailureValidationException();
        }

        if (method_exists($user, 'canAccessPanel') && ! $user->canAccessPanel(\Filament\Facades\Filament::getCurrentOrDefaultPanel())) {
            $this->throwFailureValidationException();
        }

        \Filament\Facades\Filament::auth()->login($user, $data['remember'] ?? false);
        session()->regenerate();

        return app(LoginResponse::class);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            $this->getAdminLoginAction(),
            $this->getBackToHomeAction(),
        ];
    }

    protected function getAdminLoginAction(): Action
    {
        return Action::make('adminLogin')
            ->label('Masuk sebagai Admin')
            ->url(Filament::getPanel('admin')?->getLoginUrl() ?? url('/admin/login'))
            ->color('gray')
            ->outlined();
    }

    protected function getBackToHomeAction(): Action
    {
        return Action::make('backToHome')
            ->label('Kembali ke Halaman Utama')
            ->url(url('/'))
            ->color('gray')
            ->outlined();
    }

    protected function resolveBirthDate(string $input): ?Carbon
    {
        $input = trim($input);
        if ($input === '') return null;

        $formats = ['dmY', 'dmy', 'Y-m-d', 'Ymd', 'd-m-Y', 'd/m/Y'];

        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $input);
                if ($dt !== false) return $dt;
            } catch (\Throwable $e) {
                // continue
            }
        }

        try {
            return Carbon::parse($input);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.nisn' => __('Kredensial yang diberikan tidak dapat ditemukan.'),
        ]);
    }
}