<?php

namespace App\Filament\Resources\ArsipRapors\Pages;

use App\Filament\Resources\ArsipRapors\ArsipRaporResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArsipRapor extends EditRecord
{
    protected static string $resource = ArsipRaporResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
