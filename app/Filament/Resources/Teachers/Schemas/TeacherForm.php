<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun & Data Diri Guru')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Masukkan nama lengkap...')
                            ->maxLength(255)
                            ->required(),

                        // 👇 INI BARU JON 👇
                        TextInput::make('nip')
                            ->label('NIP / NUPTK')
                            ->placeholder('Masukkan NIP atau NUPTK...')
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->placeholder('Masukkan email...')
                            ->maxLength(255)
                            ->required(),

                        // 👇 PASSWORD DI-UPGRADE BIAR AMAN PAS EDIT 👇
                        TextInput::make('password')
                            ->password()
                            ->label('Kata Sandi')
                            ->placeholder('Masukkan kata sandi...')
                            ->maxLength(255)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),

                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(Gender::class)
                            ->required(),

                        // 👇 INI BARU JON 👇
                        Select::make('employment_status')
                            ->label('Status Kepegawaian')
                            ->options([
                                'PNS' => 'PNS',
                                'PPPK' => 'PPPK',
                                'Honorer' => 'Honorer',
                                'GTY' => 'Guru Tetap Yayasan',
                            ])
                            ->default('Honorer'),

                        // 👇 INI BARU JON 👇
                        Select::make('active_status')
                            ->label('Status Keaktifan')
                            ->options([
                                'Aktif' => 'Aktif',
                                'Cuti' => 'Cuti',
                                'Pensiun' => 'Pensiun',
                                'Meninggal' => 'Meninggal Dunia',
                            ])
                            ->default('Aktif')
                            ->required(),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->placeholder('Masukkan alamat...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
