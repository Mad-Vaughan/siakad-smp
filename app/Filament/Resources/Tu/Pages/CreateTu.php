<?php

namespace App\Filament\Resources\Tu\Pages;

use App\Filament\Resources\Tu\TuResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTu extends CreateRecord
{
    protected static string $resource = TuResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
