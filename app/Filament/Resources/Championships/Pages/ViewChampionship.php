<?php

namespace App\Filament\Resources\Championships\Pages;

use App\Filament\Resources\Championships\ChampionshipResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChampionship extends ViewRecord
{
    protected static string $resource = ChampionshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
