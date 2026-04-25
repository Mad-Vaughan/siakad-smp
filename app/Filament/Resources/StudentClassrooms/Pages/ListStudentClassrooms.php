<?php

namespace App\Filament\Resources\StudentClassrooms\Pages;

use App\Filament\Resources\StudentClassrooms\StudentClassroomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentClassrooms extends ListRecords
{
    protected static string $resource = StudentClassroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
