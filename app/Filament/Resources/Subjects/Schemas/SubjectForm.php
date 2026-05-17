<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Mata Pelajaran')
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Mata Pelajaran')
                            ->required()
                            ->placeholder('Masukkan nama mata pelajaran (cth: Matematika)')
                            ->maxLength(255),

                        Select::make('teacher_id')
                            ->label('Guru Pengampu')
                            ->relationship('teacher', 'name', fn ($query) => $query->role('teacher'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
            ]);
    }
}
