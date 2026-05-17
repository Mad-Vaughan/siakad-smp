<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Mata Pelajaran')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Mata Pelajaran')
                            ->weight('bold'),

                        TextEntry::make('teacher.name')
                            ->label('Guru Pengampu')
                            ->badge()
                            ->color('success'),

                        // 👇 BAGIAN DAFTAR KELAS UDAH GUE BUMI HANGUSKAN TOTAL! 👇
                    ]),
            ]);
    }
}
