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
                                $query->whereHas('academicYear', function ($q) {
                                    $q->where('is_active', true);
                                });
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
