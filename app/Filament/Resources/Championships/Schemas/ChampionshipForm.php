<?php

namespace App\Filament\Resources\Championships\Schemas;

use App\Enums\ChampionLevel;
use App\Enums\ChampionType;
use App\Enums\Roles;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChampionshipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('student_id')
                            ->relationship('student', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', Roles::STUDENT)))
                            ->searchable()
                            ->label('Pilih Siswa')
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Kejuaraan')
                            ->placeholder('contoh: Olimpiade Sains Nasional, Lomba Cerdas Cermat, Kompetisi Robotik')
                            ->required(),
                        DatePicker::make('year')
                            ->label('Tahun')
                            ->format('Y')
                            ->displayFormat('Y')
                            ->native(false)
                            ->required(),
                        Select::make('level')
                            ->options(ChampionLevel::class)
                            ->required(),
                        TextInput::make('position')
                            ->label('Posisi')
                            ->placeholder('contoh: Juara 1, Juara 2, Juara 3, Peserta')
                            ->required(),
                        Select::make('type')
                            ->label('Tipe')
                            ->options(ChampionType::class)
                            ->required(),
                    ]),
            ]);
    }
}
