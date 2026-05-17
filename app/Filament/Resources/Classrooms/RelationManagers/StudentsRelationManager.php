<?php

namespace App\Filament\Resources\Classrooms\RelationManagers;

use App\Filament\Resources\StudentClassrooms\StudentClassroomResource;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\StudentClassroom;
use Filament\Actions\BulkAction;
// 👇 KEMBALI KE JALAN YANG LURUS (TANPA KATA TABLES!) 👇
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentClassrooms';

    protected static ?string $relatedResource = StudentClassroomResource::class;

    protected static ?string $title = 'Siswa';

    protected static ?string $label = 'Siswa';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Siswa')
                    // 👇 JURUS YANG BENER: mutateFormDataUsing 👇
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['is_active'] = true;

                        return $data;
                    })
                    ->after(function (Model $record) {
                        // Cabut status aktif kelas si anak di tempat lama
                        \App\Models\StudentClassroom::where('student_id', $record->student_id)
                            ->where('id', '!=', $record->id)
                            ->update(['is_active' => false]);

                        // Kalo dia sempet jadi alumni, statusnya dibalikin jadi aktif
                        \App\Models\Student::where('id', $record->student_id)
                            ->update(['active_status' => 'aktif']);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('aktifkan_massal')
                        ->label('Salin & Aktifkan ke Semester Ini')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $activeAcademicYear = AcademicYear::where('is_active', true)->first();

                            if (! $activeAcademicYear) {
                                Notification::make()
                                    ->danger()
                                    ->title('Tahun Ajaran Aktif Tidak Ditemukan')
                                    ->body('Tidak ada tahun ajaran aktif saat ini.')
                                    ->send();

                                return;
                            }

                            $processedCount = 0;

                            foreach ($records as $record) {
                                $record->loadMissing('classroom.academicYear');

                                $currentAcademicYear = $record->classroom?->academicYear;

                                if ($currentAcademicYear && $currentAcademicYear->is_active) {
                                    $record->is_active = true;
                                    $record->save();

                                    $processedCount++;

                                    continue;
                                }

                                $targetClassroom = Classroom::where('name', $record->classroom->name)
                                    ->where('academic_year_id', $activeAcademicYear->id)
                                    ->first();

                                if (! $targetClassroom) {
                                    continue;
                                }

                                $existingTargetRecord = StudentClassroom::where('student_id', $record->student_id)
                                    ->where('classroom_id', $targetClassroom->id)
                                    ->first();

                                if ($existingTargetRecord) {
                                    $existingTargetRecord->is_active = true;
                                    $existingTargetRecord->save();
                                } else {
                                    $newRecord = $record->replicate();
                                    $newRecord->classroom_id = $targetClassroom->id;
                                    $newRecord->is_active = true;
                                    $newRecord->save();
                                }

                                $processedCount++;
                            }

                            Notification::make()
                                ->success()
                                ->title('Siswa Berhasil Diaktifkan')
                                ->body("{$processedCount} siswa telah diproses untuk aktif di tahun ajaran saat ini.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
