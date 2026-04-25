<?php

namespace App\Filament\Resources\StudentClassrooms\Pages;

use App\Filament\Resources\StudentClassrooms\StudentClassroomResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentClassroom extends ViewRecord
{
    protected static string $resource = StudentClassroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
