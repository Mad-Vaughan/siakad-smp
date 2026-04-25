<?php

namespace App\Filament\Resources\Tu\Pages;

use App\Filament\Resources\Tu\TuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTu extends ListRecords
{
    protected static string $resource = TuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Tata Usaha';
    }
}
