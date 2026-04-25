<?php

namespace App\Filament\Resources\Presences\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PresenceForm
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
                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),
                    ]),
            ]);
    }
}
