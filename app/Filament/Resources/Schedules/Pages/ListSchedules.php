<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Models\AcademicYear;
use App\Models\Schedule;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getCopyScheduleAction(),
            CreateAction::make(),
        ];
    }

    private function getCopyScheduleAction(): Action
    {
        return Action::make('copy_schedule')
            ->label('Salin Jadwal')
            ->icon('heroicon-m-document-duplicate')
            ->form([
                Select::make('from_academic_year_id')
                    ->label('Pilih Tahun Ajaran Asal')
                    ->options(function () {
                        return AcademicYear::query()
                            ->get()
                            ->mapWithKeys(function ($year) {
                                $semester = ucfirst(strtolower($year->semester ?? 'Ganjil'));
                                return [$year->id => "{$year->name} - {$semester}"];
                            })
                            ->toArray();
                    })
                    ->required()
                    ->searchable(),
            ])
            ->action(function (array $data): void {
                $this->copySchedules($data['from_academic_year_id']);
            });
    }

    private function copySchedules(int $fromAcademicYearId): void
    {
        try {
            DB::transaction(function () use ($fromAcademicYearId) {
                // 1. Cari Tahun Ajaran yang AKTIF (Tahun Ajaran Tujuan)
                $activeAcademicYear = AcademicYear::where('is_active', true)->firstOrFail();

                if ($activeAcademicYear->id == $fromAcademicYearId) {
                    Notification::make()
                        ->danger()
                        ->title('Gagal Menyalin')
                        ->body('Tahun ajaran asal dan tujuan tidak boleh sama.')
                        ->send();
                    return;
                }

                // 2. Ambil semua jadwal dari Tahun Ajaran ASAL beserta data kelasnya
                $sourceSchedules = Schedule::whereHas('classroom', function ($query) use ($fromAcademicYearId) {
                    $query->where('academic_year_id', $fromAcademicYearId);
                })->with('classroom')->get();

                if ($sourceSchedules->isEmpty()) {
                    Notification::make()
                        ->warning()
                        ->title('Tidak Ada Jadwal')
                        ->body('Tahun Ajaran asal yang dipilih tidak memiliki jadwal untuk disalin.')
                        ->send();
                    return;
                }

                $copiedCount = 0;
                $missingClassrooms = [];

                foreach ($sourceSchedules as $schedule) {
                    // 💡 KUNCI JAWABANNYA DI SINI JON! 💡
                    // Cari kelas padanannya yang namanya SAMA tapi terdaftar di Tahun Ajaran yang BARU AKTIF
                    $targetClassroom = \App\Models\Classroom::where('name', $schedule->classroom->name)
                        ->where('academic_year_id', $activeAcademicYear->id)
                        ->first();

                    // Jika kelas tersebut belum dibuat di Tahun Ajaran Baru, kita skip biar kaga crash
                    if (! $targetClassroom) {
                        $missingClassrooms[$schedule->classroom->name] = true;
                        continue;
                    }

                    // Cek apakah jadwal serupa sudah pernah disalin (biar gak duplikat double kalau diklik 2x)
                    $existingSchedule = Schedule::where('classroom_id', $targetClassroom->id)
                        ->where('subject_id', $schedule->subject_id)
                        ->where('day', $schedule->day)
                        ->where('start_time', $schedule->start_time)
                        ->exists();

                    // Jika belum ada, kloning dan ganti ID kelasnya ke kelas tahun ajaran baru!
                    if (! $existingSchedule) {
                        $newSchedule = $schedule->replicate();
                        $newSchedule->classroom_id = $targetClassroom->id; // Pindahkan ke kamar baru Jon!
                        $newSchedule->save();
                        $copiedCount++;
                    }
                }

                // Susun pesan laporan biar TU tau kelas mana yang belum dibikin
                $bodyMessage = "Total jadwal yang berhasil disalin dan aktif: {$copiedCount} jadwal.";
                if (!empty($missingClassrooms)) {
                    $listKelas = implode(', ', array_keys($missingClassrooms));
                    $bodyMessage .= " (Catatan: Jadwal untuk kelas [{$listKelas}] dilewati karena kelasnya belum lu bikin di Tahun Ajaran Baru).";
                }

                if ($copiedCount > 0) {
                    Notification::make()
                        ->success()
                        ->title('Jadwal Berhasil Disalin!')
                        ->body($bodyMessage)
                        ->send();
                } else {
                    Notification::make()
                        ->warning()
                        ->title('Tidak Ada Jadwal Baru')
                        ->body('Semua jadwal sudah terisi atau kelas padanan belum dibuat di Tahun Ajaran Baru.')
                        ->send();
                }

                // Refresh halaman agar langsung muncul data barunya
                $this->redirect(request()->header('Referer'));
            });
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title('Gagal Menyalin Jadwal')
                ->body('Terjadi kesalahan: '.$e->getMessage())
                ->send();
        }
    }
}