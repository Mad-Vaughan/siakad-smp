<?php

namespace App\Filament\Resources\Championships;

use App\Filament\Concerns\NavigationGrouping\AssesmentAndChampionshipGrouping;
use App\Filament\Resources\Championships\Pages\CreateChampionship;
use App\Filament\Resources\Championships\Pages\EditChampionship;
use App\Filament\Resources\Championships\Pages\ListChampionships;
use App\Filament\Resources\Championships\Pages\ViewChampionship;
use App\Filament\Resources\Championships\Schemas\ChampionshipForm;
use App\Filament\Resources\Championships\Schemas\ChampionshipInfolist;
use App\Filament\Resources\Championships\Tables\ChampionshipsTable;
use App\Models\Championship;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class ChampionshipResource extends Resource
{
    use AssesmentAndChampionshipGrouping;

    protected static ?string $model = Championship::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::Trophy;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Kejuaraan';

    public static function form(Schema $schema): Schema
    {
        return ChampionshipForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChampionshipInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChampionshipsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChampionships::route('/'),
            'create' => CreateChampionship::route('/create'),
            'view' => ViewChampionship::route('/{record}'),
            'edit' => EditChampionship::route('/{record}/edit'),
        ];
    }
}
