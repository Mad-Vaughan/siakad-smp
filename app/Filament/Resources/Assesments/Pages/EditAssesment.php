<?php

namespace App\Filament\Resources\Assesments\Pages;

use App\Filament\Resources\Assesments\AssesmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAssesment extends EditRecord
{
    protected static string $resource = AssesmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
