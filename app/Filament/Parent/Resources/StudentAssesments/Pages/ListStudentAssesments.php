<?php

namespace App\Filament\Parent\Resources\StudentAssesments\Pages;

use App\Filament\Parent\Resources\StudentAssesments\StudentAssesmentResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentAssesments extends ListRecords
{
    protected static string $resource = StudentAssesmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
