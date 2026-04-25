<?php

namespace App\Filament\Parent\Resources\StudentPresences\Pages;

use App\Filament\Parent\Resources\StudentPresences\StudentPresenceResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentPresences extends ListRecords
{
    protected static string $resource = StudentPresenceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
