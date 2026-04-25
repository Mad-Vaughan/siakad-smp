<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Resources\Championships\ChampionshipResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ChampionshipsRelationManager extends RelationManager
{
    protected static string $relationship = 'championships';

    protected static ?string $relatedResource = ChampionshipResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
