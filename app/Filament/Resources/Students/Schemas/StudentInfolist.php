<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Siswa'),
                        TextEntry::make('nisn')
                            ->label('NISN')
                            ->placeholder('-'),
                        TextEntry::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->columnSpanFull()
                            ->label('Alamat')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
