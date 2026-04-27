<?php

namespace App\Filament\Resources\Rekapitulasis\Pages;

use App\Filament\Resources\Rekapitulasis\RekapitulasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRekapitulasi extends EditRecord
{
    protected static string $resource = RekapitulasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
