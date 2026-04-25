<?php

namespace App\Filament\Resources\Tu\Pages;

use App\Filament\Resources\Tu\TuResource;
use Filament\Resources\Pages\EditRecord;

class EditTu extends EditRecord
{
    protected static string $resource = TuResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
