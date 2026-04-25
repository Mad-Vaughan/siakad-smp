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
        // Create or update multiple student-classroom records from the form data
        $classroomId = $data['classroom_id'] ?? null;
        $isActive = isset($data['is_active']) ? (bool) $data['is_active'] : false;

        $last = null;

        for ($i = 1; $i <= 10; $i++) {
            $key = 'student_id_' . $i;

            if (empty($data[$key])) {
                continue;
            }

            $studentId = $data[$key];

            $last = StudentClassroom::updateOrCreate([
                'student_id' => $studentId,
                'classroom_id' => $classroomId,
            ], [
                'is_active' => $isActive,
            ]);
        }

        // If none were provided, fallback to parent behavior creating a single record
        if (! $last) {
            return parent::handleRecordCreation($data);
        }

        return $last;
    }
}
