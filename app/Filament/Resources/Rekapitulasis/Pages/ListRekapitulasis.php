<?php

namespace App\Filament\Resources\Rekapitulasis\Pages;

use App\Filament\Resources\Rekapitulasis\RekapitulasiResource;
use App\Models\AcademicYear;
use App\Models\Classroom;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListRekapitulasis extends ListRecords
{
    protected static string $resource = RekapitulasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_rekap')
                ->label('Cetak Rekap Kelas')
                ->icon('heroicon-o-printer')
                ->modalWidth('md')
                ->form([
                    Select::make('academic_year_id')
                        ->label('Tahun Ajaran & Semester')
                        ->options(fn () => AcademicYear::all()->mapWithKeys(fn ($item) => [$item->id => "{$item->name} - ".ucfirst($item->semester)]))
                        ->required()
                        ->native(false)
                        ->live() // 👈 Bikin reaktif biar ngasih tau dropdown di bawahnya
                        ->afterStateUpdated(fn ($set) => $set('classroom_id', null)), // Reset kelas kalo tahun ajaran diganti

                    Select::make('classroom_id')
                        ->label('Pilih Kelas')
                        ->options(function ($get) {
                            // 👈 Ambil ID tahun ajaran yang dipilih di atas
                            $academicYearId = $get('academic_year_id');

                            // Kalau belum milih tahun ajaran, kosongin kelasnya
                            if (! $academicYearId) {
                                return [];
                            }

                            // 👈 Filter kelas cuma yang sesuai sama tahun ajaran yang dipilih
                            return Classroom::where('academic_year_id', $academicYearId)->pluck('name', 'id');
                        })
                        ->required()
                        ->native(false),
                ])
                ->action(fn (array $data) => redirect()->route('cetak.rekap.final', ['classroom' => $data['classroom_id'], 'year' => $data['academic_year_id']])),
        ];
    }
}
