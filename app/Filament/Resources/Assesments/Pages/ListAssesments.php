<?php

namespace App\Filament\Resources\Assesments\Pages;

use App\Filament\Resources\Assesments\AssesmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssesments extends ListRecords
{
    protected static string $resource = AssesmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
