<?php

namespace App\Filament\Resources\Championships\Pages;

use App\Filament\Resources\Championships\ChampionshipResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChampionships extends ListRecords
{
    protected static string $resource = ChampionshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
