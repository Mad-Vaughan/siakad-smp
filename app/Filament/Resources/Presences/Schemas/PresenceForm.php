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
                Section::make('Buat Daftar Hadir Harian')
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema([
                        // 👇 Kunci mati jadi Harian 👇
                        Select::make('type')
                            ->label('Jenis Presensi')
                            ->options([
                                'harian' => 'Presensi Harian (Wali Kelas)',
                            ])
                            ->default('harian')
                            ->disabled() // Kunci biar kaga bisa diubah User
                            ->dehydrated() // Tetep dikirim ke database pas di-save
                            ->required(),

                        Select::make('classroom_id')
                            ->label('Kelas')
                            ->preload()
                            ->relationship('classroom', 'name', function ($query) {
                                $query->whereHas('academicYear', fn ($q) => $q->where('is_active', true));
                                if (auth()->user()->hasRole('teacher')) {
                                    $query->where('teacher_id', auth()->id());
                                }
                            })
                            ->searchable()
                            ->required(),

                        DatePicker::make('date')
                            ->label('Tanggal Pertemuan')
                            ->default(now())
                            ->required(),
                    ]),
            ]);
    }
}
