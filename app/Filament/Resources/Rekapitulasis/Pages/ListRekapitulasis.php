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
            Action::make('cetak_rekap_kelas')
                ->label('Cetak Rekap Kelas')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->form([
                    Select::make('classroom_id')
                        ->label('Pilih Kelas')
                        ->options(Classroom::all()->pluck('name', 'id'))
                        ->required()
                        ->native(false), // Biar tampilannya cakep gak kaku
                    
                    Select::make('academic_year_id')
                        ->label('Tahun Ajaran')
                        ->options(function () {
                            // 👇 JURUS ANTI ERROR: Cek dulu datanya ada apa kaga 👇
                            // Kita ambil semua kolom tahun, kalau kolomnya bukan 'year', dia bakal cari 'tahun_ajaran'
                            return AcademicYear::all()->mapWithKeys(function ($item) {
                                $label = $item->year ?? $item->tahun_ajaran ?? $item->name ?? "ID: {$item->id}";
                                return [$item->id => (string) $label];
                            })->toArray();
                        })
                        ->default(function () {
                            // Ambil yang is_active-nya true (atau angka 1)
                            return AcademicYear::where('is_active', true)->first()?->id ?? 
                                   AcademicYear::latest()->first()?->id;
                        })
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    // Paksa redirect pake URL biar kaga nyangkut di Livewire
                    $url = route('cetak.rekap.final', [
                        'classroom' => $data['classroom_id'],
                        'year' => $data['academic_year_id']
                    ]);
                    
                    return redirect()->to($url);
                }),
        ];
    }
}