<?php

namespace App\Filament\Resources\StudentClassrooms\Pages;

use App\Filament\Resources\StudentClassrooms\StudentClassroomResource;
use App\Models\StudentClassroom;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStudentClassroom extends EditRecord
{
    protected static string $resource = StudentClassroomResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->getRecord()?->student_id !== null) {
            $data['student_ids'] = [$this->getRecord()->student_id];
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $studentIds = array_filter($data['student_ids'] ?? []);
        $isActive = isset($data['is_active']) ? (bool) $data['is_active'] : $record->is_active;
        $classroomId = $data['classroom_id'] ?? $record->classroom_id;

        if (! empty($studentIds)) {
            $firstStudentId = array_shift($studentIds);

            $record->update([
                'student_id' => $firstStudentId,
                'classroom_id' => $classroomId,
                'is_active' => $isActive,
            ]);

            foreach ($studentIds as $studentId) {
                StudentClassroom::updateOrCreate([
                    'student_id' => $studentId,
                    'classroom_id' => $classroomId,
                ], [
                    'is_active' => $isActive,
                ]);
            }

            return $record->refresh();
        }

        unset($data['student_ids']);

        return parent::handleRecordUpdate($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    // 👇 JURUS TENDANGAN BALIK KE TABEL 👇
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
