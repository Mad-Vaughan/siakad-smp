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
                Section::make('')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Masukkan nama lengkap...')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->placeholder('Masukkan email...')
                            ->maxLength(255)
                            ->required(),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(Gender::class),
                        TextInput::make('password')
                            ->password()
                            ->label('Kata Sandi')
                            ->placeholder('Masukkan kata sandi...')
                            ->maxLength(255)
                            ->required(),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->placeholder('Masukkan alamat...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
