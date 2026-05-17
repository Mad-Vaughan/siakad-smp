<?php

namespace App\Filament\Resources\StudentClassrooms\Pages;

use App\Filament\Resources\StudentClassrooms\StudentClassroomResource;
use App\Models\StudentClassroom;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentClassroom extends CreateRecord
{
    protected static string $resource = StudentClassroomResource::class;

    protected function handleRecordCreation(array $data): StudentClassroom
    {
        $classroomId = $data['classroom_id'] ?? null;
        $isActive = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        $studentIds = array_filter($data['student_ids'] ?? []);

        if (! empty($studentIds)) {
            $last = null;

            foreach ($studentIds as $studentId) {
                $last = StudentClassroom::updateOrCreate([
                    'student_id' => $studentId,
                    'classroom_id' => $classroomId,
                ], [
                    'is_active' => $isActive,
                ]);
            }

            return $last;
        }

        unset($data['student_ids']);

        return parent::handleRecordCreation($data);
    }

    // 👇 JURUS TENDANGAN BALIK KE TABEL 👇
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
