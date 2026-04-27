<?php

namespace App\Filament\Resources\Assesments\Schemas;

use App\Enums\AssesmentType;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssesmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('classroom_id')
                            ->label('Kelas')
                            ->preload()
                            ->relationship('classroom', 'name', function ($query) {
                                // 👇 Filter 1: Cuma ambil kelas dari Tahun Ajaran yang Aktif
                                $query->whereHas('academicYear', function ($q) {
                                    $q->where('is_active', true);
                                });

                                // 👇 Filter 2: JURUS ANTI NGINTIP KELAS ORANG 👇
                                /** @var \App\Models\User $user */
                                $user = auth()->user();
                                
                                if ($user && $user->hasRole('teacher')) {
                                    $query->where('teacher_id', $user->id);
                                }
                            })
                            ->searchable()
                            ->required(),
                        Select::make('subject_id')
                            ->preload()
                            ->label('Mata Pelajaran')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('type')
                            ->label('Tipe Penilaian')
                            ->options(AssesmentType::class)
                            ->required(),
                    ]),
            ]);
    }
}