<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use App\Enums\Roles;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->relationship('academicYear', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('teacher_id')
                            ->label('Wali Kelas')
                            ->relationship('teacher', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::TEACHER)))
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Kelas')
                            ->columnSpanFull()
                            ->placeholder('Masuukkan nama kelas')
                            ->maxLength(255)
                            ->required(),
                    ]),
            ]);
    }
}
