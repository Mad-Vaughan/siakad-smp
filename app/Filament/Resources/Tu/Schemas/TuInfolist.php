<?php

namespace App\Filament\Resources\Tu\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TuInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama Lengkap'),

                TextEntry::make('email')
                    ->label('Email'),

                TextEntry::make('gender')
                    ->label('Jenis Kelamin')
                    ->badge(),

                TextEntry::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),
            ]);
    }
}