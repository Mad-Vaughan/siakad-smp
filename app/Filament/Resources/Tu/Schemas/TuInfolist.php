<?php

namespace App\Filament\Resources\Tu\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TuInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Profil Tata Usaha')
                    ->description('Detail data pribadi petugas administrasi sekolah.')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),

                        TextEntry::make('email')
                            ->label('Alamat Email')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),

                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->badge()
                            ->formatStateUsing(fn ($state) => match (strtolower($state->value ?? $state ?? '')) {
                                'l', 'male', 'laki-laki' => 'Laki-laki',
                                'p', 'female', 'perempuan' => 'Perempuan',
                                default => $state ?? '-',
                            })
                            ->color(fn ($state) => match (strtolower($state->value ?? $state ?? '')) {
                                'l', 'male', 'laki-laki' => 'info',
                                'p', 'female', 'perempuan' => 'warning',
                                default => 'gray',
                            }),

                        TextEntry::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull()
                            ->placeholder('Alamat belum diisi')
                            ->icon('heroicon-m-map-pin'),
                    ]),
            ]);
    }
}
