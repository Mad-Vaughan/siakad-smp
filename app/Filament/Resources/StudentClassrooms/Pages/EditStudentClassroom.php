<?php

namespace App\Filament\Resources\StudentClassrooms\Pages;

use App\Filament\Resources\StudentClassrooms\StudentClassroomResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentClassroom extends EditRecord
{
    protected static string $resource = StudentClassroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
