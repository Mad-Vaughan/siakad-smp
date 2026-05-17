<?php

namespace App\Filament\Resources\StudentHistories\Pages;

use App\Filament\Resources\StudentHistories\StudentHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentHistory extends EditRecord
{
    protected static string $resource = StudentHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
