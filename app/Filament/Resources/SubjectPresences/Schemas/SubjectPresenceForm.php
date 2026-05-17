<?php

namespace App\Filament\Resources\SubjectPresences\Schemas;

use App\Models\Schedule;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubjectPresenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Buat Daftar Hadir Mata Pelajaran')
                    ->columns(1)
                    ->schema([
                        Select::make('type')
                            ->label('Jenis Presensi')
                            ->options(['mapel' => 'Presensi Mata Pelajaran'])
                            ->default('mapel')
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),

                        Select::make('schedule_id')
                            ->label(fn () => auth()->user()->hasRole('admin') ? 'Pilih Jadwal (Mode Admin: Semua Mapel & Tahun)' : 'Jadwal Pelajaran (Sesuai Mapel Anda)')
                            ->options(function () {
                                $query = Schedule::with(['classroom', 'subject']);

                                if (! auth()->user()->hasRole('admin')) {
                                    $query->whereHas('classroom.academicYear', fn ($q) => $q->where('is_active', true));

                                    if (auth()->user()->hasRole('teacher')) {
                                        $query->whereHas('subject', fn ($q) => $q->where('teacher_id', auth()->id()));
                                    }
                                }

                                return $query->get()->mapWithKeys(function ($record) {
                                    $kelas = $record->classroom?->name ?? 'Tanpa Kelas';
                                    $mapel = $record->subject?->name ?? 'Tanpa Mapel';

                                    return [$record->id => "Kelas {$kelas} — {$mapel} ({$record->day}, {$record->start_time})"];
                                });
                            })
                            ->searchable()
                            ->preload()
                            // 👇 INI DIA OBATNYA JON! KITA JEBOL BATAS LIMITNYA JADI 1000! 👇
                            ->optionsLimit(1000)
                            ->required(),

                        DatePicker::make('date')
                            ->label('Tanggal Pertemuan')
                            ->default(now())
                            ->hint(function () {
                                $active = \App\Models\AcademicYear::where('is_active', true)->first();

                                return $active ? "Tahun Ajaran: {$active->name} ({$active->semester})" : null;
                            })
                            ->hintColor('primary')
                            ->required(),
                    ]),
            ]);
    }
}
