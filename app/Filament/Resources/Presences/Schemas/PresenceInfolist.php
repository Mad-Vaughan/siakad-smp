<?php

namespace App\Filament\Resources\Presences\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PresenceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('classroom.name')
                            ->label('Kelas')
                            ->placeholder('-'),

                        // Use camelCase for the relationship path
                        TextEntry::make('academicYear.name')
                            ->label('Tahun Ajaran')
                            ->placeholder('-'),

                        TextEntry::make('date')
                            ->label('Tanggal')
                            ->date()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
