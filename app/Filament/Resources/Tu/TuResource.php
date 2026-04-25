<?php

namespace App\Filament\Resources\Tu;

use App\Filament\Concerns\NavigationGrouping\UserManagementGrouping;
use App\Filament\Resources\Tu\Pages\CreateTu;
use App\Filament\Resources\Tu\Pages\EditTu;
use App\Filament\Resources\Tu\Pages\ListTu;
use App\Filament\Resources\Tu\Pages\ViewTu;
use App\Filament\Resources\Tu\Schemas\TuForm;
use App\Filament\Resources\Tu\Schemas\TuInfolist;
use App\Filament\Resources\Tu\Tables\TuTable;
use App\Models\Tu;
use BackedEnum;
use UnitEnum;
use App\Enums\Filament\NavigationGrouping;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class TuResource extends Resource
{
    use UserManagementGrouping;

    protected static ?string $model = Tu::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::Briefcase;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Tata Usaha';

    // Ensure this resource is shown under the User Management group in navigation
    protected static UnitEnum|string|null $navigationGroup = NavigationGrouping::UserManagement;
    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return TuForm::configure($schema);
    }

    // Force registration in navigation regardless of Shield permission checks
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function infolist(Schema $schema): Schema
    {
        return TuInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TuTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTu::route('/'),
            'create' => CreateTu::route('/create'),
            'view' => ViewTu::route('/{record}'),
            'edit' => EditTu::route('/{record}/edit'),
        ];
    }
}
