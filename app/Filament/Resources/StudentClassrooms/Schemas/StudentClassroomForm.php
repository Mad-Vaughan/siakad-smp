<?php

namespace App\Filament\Resources\StudentClassrooms\Schemas;

use App\Enums\Roles;
use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

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

                        // 👇 INI DIA GEMBOKNYA JON! YANG LAIN KAGA GUE SENTUH 👇
                        Select::make('classroom_id')
                            ->label('Kelas')
                            ->relationship('classroom', 'name', fn (Builder $query) => $query->whereHas('academicYear', fn ($q) => $q->where('is_active', true)))
                            ->preload()
                            ->searchable()
                            ->required(),
                        // 👆 BATAS GEMBOK 👆

                        Select::make('student_ids')
                            ->label('Siswa')
                            ->multiple()
                            ->options(fn () => Student::query()
                                ->whereHas('roles', fn (Builder $query) => $query->where('name', Roles::STUDENT->value))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->rules(['required', 'array', 'distinct'])
                            ->helperText('Pilih satu atau lebih siswa untuk dimasukkan ke kelas. Duplikat akan dicegah otomatis.'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required(),
                    ]),
            ]);
    }
}
