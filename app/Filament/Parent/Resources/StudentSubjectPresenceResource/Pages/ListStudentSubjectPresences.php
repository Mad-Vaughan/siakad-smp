<?php

namespace App\Filament\Parent\Resources\StudentSubjectPresenceResource\Pages;

use App\Filament\Parent\Resources\StudentSubjectPresenceResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentSubjectPresences extends ListRecords
{
    protected static string $resource = StudentSubjectPresenceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
