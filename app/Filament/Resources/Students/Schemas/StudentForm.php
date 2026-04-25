<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
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
                            ->label('Nama Siswa')
                            ->placeholder('Masukkan nama lengkap...')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('nisn')
                            ->label('NISN')
                            ->placeholder('Masukkan NISN...')
                            ->maxLength(20)
                            ->required(),
                        DatePicker::make('date_of_birth')
                            ->maxDate(now()->subYears(5))
                            ->label('Tanggal Lahir'),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(Gender::class),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Masukkan alamat...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
