<?php

namespace App\Filament\Resources\SubjectPresences\Pages;

use App\Filament\Resources\SubjectPresences\SubjectPresenceResource;
use App\Models\Schedule;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListSubjectPresences extends ListRecords
{
    protected static string $resource = SubjectPresenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 👇 INI TOMBOL AJAIB BUAT GURU NYETAK REKAP MAPEL 👇
            Action::make('cetak_rekap_mapel')
                ->label('Cetak Rekap Mapel')
                ->icon('heroicon-o-printer')
                ->color('success') // Warna ijo biar menarik
                ->modalHeading('Cetak Rekapitulasi Mata Pelajaran')
                ->modalWidth('md')
                ->form([
                    Select::make('schedule_id')
                        ->label('Pilih Jadwal Pelajaran')
                        ->options(function () {
                            $query = Schedule::with(['classroom', 'subject']);

                            // Filter pake Jurus Dewa kemaren
                            if (! auth()->user()->hasRole('admin')) {
                                $query->whereHas('classroom.academicYear', fn ($q) => $q->where('is_active', true));
                                if (auth()->user()->hasRole('teacher')) {
                                    $query->whereHas('subject', fn ($q) => $q->where('teacher_id', auth()->id()));
                                }
                            }

                            return $query->get()->mapWithKeys(function ($record) {
                                $kelas = $record->classroom?->name ?? 'Tanpa Kelas';
                                $mapel = $record->subject?->name ?? 'Tanpa Mapel';

                                return [$record->id => "Kelas {$kelas} — {$mapel}"];
                            });
                        })
                        ->searchable()
                        ->optionsLimit(1000)
                        ->required(),
                ])
                // 👇 INI OBAT BYPASS LIVEWIRE-NYA JON! 👇
                ->action(function (array $data, \Livewire\Component $livewire) {
                    // 1. Kita bikin URL arah tujuan cetaknya
                    $url = route('cetak.rekap.mapel', ['schedule' => $data['schedule_id']]);

                    // 2. Kita suruh browser buka tab baru pake Javascript paksa!
                    $livewire->js("window.open('{$url}', '_blank');");
                }),

            CreateAction::make(),
        ];
    }
}
