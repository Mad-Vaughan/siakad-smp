<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 👇 BAGIAN 1: IDENTITAS UTAMA 👇
                Section::make('Identitas Utama')
                    ->description('Data diri pokok siswa sesuai Dapodik.')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap Siswa')
                            ->placeholder('Masukkan nama lengkap...')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->required(),

                        TextInput::make('nisn')
                            ->label('NISN')
                            ->placeholder('Contoh: 0012345678')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->live(onBlur: true) // 👈 JURUS DETEKSI KETIKAN
                            ->afterStateUpdated(function ($get, $set, ?string $state) {
                                // 🧙‍♂️ MAGIC AUTOFILL: Bikin email otomatis dari NISN
                                if (! $get('email') && filled($state)) {
                                    $set('email', $state.'@siswa.siakad.com');
                                }
                            }),

                        TextInput::make('nipd')
                            ->label('NIPD (Nomor Induk Peserta Didik)')
                            ->placeholder('Masukkan NIPD...')
                            ->maxLength(20)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($get, $set, ?string $state) {
                                // 🧙‍♂️ MAGIC AUTOFILL: Kalo NISN kaga ada, pake NIPD buat email
                                if (! $get('email') && ! $get('nisn') && filled($state)) {
                                    $set('email', $state.'@siswa.siakad.com');
                                }
                            }),

                        TextInput::make('nik')
                            ->label('NIK (Nomor Induk Kependudukan)')
                            ->placeholder('16 Digit NIK...')
                            ->maxLength(16)
                            ->numeric(),

                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                            ])
                            ->required(),

                        Select::make('religion')
                            ->label('Agama')
                            ->options([
                                'Islam' => 'Islam',
                                'Kristen' => 'Kristen',
                                'Katolik' => 'Katolik',
                                'Hindu' => 'Hindu',
                                'Buddha' => 'Buddha',
                                'Konghucu' => 'Konghucu',
                            ])
                            ->searchable(),
                    ]),

                // 👇 BAGIAN 2: DATA KELAHIRAN & KONTAK 👇
                Section::make('Kelahiran & Kontak')
                    ->description('Tempat/tanggal lahir dan informasi kontak yang bisa dihubungi.')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(2)
                    ->schema([
                        TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->placeholder('Contoh: Jakarta')
                            ->maxLength(100),

                        DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->maxDate(now())
                            ->required()
                            ->live(onBlur: true) // 👈 JURUS BUAT BIKIN PASSWORD
                            ->afterStateUpdated(function ($set, ?string $state, $context) {
                                // 🧙‍♂️ MAGIC AUTOFILL: Password otomatis pake tanggal lahir saat BIKIN BARU
                                if ($context === 'create' && filled($state)) {
                                    $set('password', Hash::make($state));
                                }
                            }),

                        TextInput::make('phone')
                            ->label('Nomor Telepon/WA')
                            ->placeholder('Contoh: 08123456789')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('email')
                            ->label('Email / Username')
                            ->email()
                            ->placeholder('otomatis@siswa.siakad.com')
                            ->maxLength(255)
                            ->required(), // 👈 Dijadiin required karena butuh buat login

                        // 👇 HIDDEN FIELD BUAT NYIMPEN PASSWORD PAS BIKIN BARU 👇
                        TextInput::make('password')
                            ->hidden() // Di umpetin biar TU kaga usah ngetik manual
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state)),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Masukkan alamat lengkap (Jalan, RT/RW, Desa, Kecamatan)...')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }
}
