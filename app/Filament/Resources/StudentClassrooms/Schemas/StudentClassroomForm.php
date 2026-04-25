<?php

namespace App\Filament\Resources\StudentClassrooms\Schemas;

use App\Enums\Roles;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('classroom_id')
                            ->label('Kelas')
                            ->relationship('classroom', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),

                        // Multiple student slots to add several students quickly
                        Select::make('student_id_1')
                            ->label('Siswa 1')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),
                        Select::make('student_id_2')
                            ->label('Siswa 2')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),
                        Select::make('student_id_3')
                            ->label('Siswa 3')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),
                        Select::make('student_id_4')
                            ->label('Siswa 4')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),
                        Select::make('student_id_5')
                            ->label('Siswa 5')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),
                        Select::make('student_id_6')
                            ->label('Siswa 6')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),
                        Select::make('student_id_7')
                            ->label('Siswa 7')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),
                        Select::make('student_id_8')
                            ->label('Siswa 8')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),
                        Select::make('student_id_9')
                            ->label('Siswa 9')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),
                        Select::make('student_id_10')
                            ->label('Siswa 10')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required(),
                    ]),
            ]);
    }
}
