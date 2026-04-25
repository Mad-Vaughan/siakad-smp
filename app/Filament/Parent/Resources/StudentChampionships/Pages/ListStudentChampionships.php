<?php

namespace App\Filament\Parent\Resources\StudentChampionships\Pages;

use App\Filament\Parent\Resources\StudentChampionships\StudentChampionshipResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentChampionships extends ListRecords
{
    protected static string $resource = StudentChampionshipResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
