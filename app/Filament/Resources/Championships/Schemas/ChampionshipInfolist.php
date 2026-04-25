<?php

namespace App\Filament\Resources\Championships\Schemas;

use App\Filament\Resources\Students\StudentResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChampionshipInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('student.name')
                            ->url(fn ($record) => StudentResource::getUrl('view', ['record' => $record->student_id]))
                            ->tooltip('Lihat detail siswa')
                            ->color('primary')
                            ->label('Siswa'),
                        TextEntry::make('name')
                            ->label('Nama Kejuaraan'),
                        TextEntry::make('year')
                            ->label('Tahun'),
                        TextEntry::make('level')
                            ->badge()
                            ->label('Tingkat'),
                        TextEntry::make('position')
                            ->label('Posisi'),
                        TextEntry::make('type')
                            ->badge()
                            ->label('Tipe')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
