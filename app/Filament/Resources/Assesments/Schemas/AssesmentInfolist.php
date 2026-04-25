<?php

namespace App\Filament\Resources\Assesments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssesmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('classroom.name')
                            ->label('Kelas'),
                        TextEntry::make('subject.name')
                            ->label('Mata Pelajaran'),
                        TextEntry::make('type')
                            ->label('Tipe Penilaian')
                            ->badge(),
                    ]),
            ]);
    }
}
