<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassroomInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('academicYear.name')
                            ->label('Tahun Ajaran')
                            ->numeric(),
                        TextEntry::make('teacher.name')
                            ->label('Wali Kelas')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label('Nama Kelas'),
                    ]),
            ]);
    }
}
