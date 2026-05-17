<?php

namespace App\Filament\Resources\StudentHistories\Pages;

use App\Filament\Resources\StudentHistories\StudentHistoryResource;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentHistory extends ViewRecord
{
    protected static string $resource = StudentHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
