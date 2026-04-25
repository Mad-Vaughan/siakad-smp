<?php

namespace App\Filament\Resources\Admins\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
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
                            ->placeholder('Masukkan email...')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->required(),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(Gender::class),
                        TextInput::make('password')
                            ->password()
                            ->label('Kata Sandi')
                            ->placeholder('Masukkan kata sandi...')
                            ->required(),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Masukkan alamat...')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }
}
