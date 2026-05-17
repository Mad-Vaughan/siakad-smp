<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use App\Enums\Roles;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

// 👈 PENGAMAN BUAT GEMBOKNYA

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

                        // 👇 INI DIA GEMBOKNYA! 👇
                        Select::make('academic_year_id')
                            ->label('Tahun Ajaran & Semester')
                            // Tambahin filter ->where('is_active', true) biar yang lalu kaga nongol!
                            ->relationship('academicYear', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} (Semester ".ucfirst($record->semester).')')
                            ->preload()
                            ->searchable()
                            ->required(),
                        // 👆 BATAS GEMBOK 👆

                        Select::make('teacher_id')
                            ->label('Wali Kelas')
                            ->relationship('teacher', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::TEACHER->value)))
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->label('Nama Kelas')
                            ->columnSpanFull()
                            ->placeholder('Masukkan nama kelas')
                            ->maxLength(255)
                            ->required(),
                    ]),
            ]);
    }
}
