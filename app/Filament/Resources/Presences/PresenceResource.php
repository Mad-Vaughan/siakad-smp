<?php

namespace App\Filament\Resources\Presences;

use App\Filament\Concerns\NavigationGrouping\ClassAndSubjectManagement;
use App\Filament\Resources\Presences\Pages\CreatePresence;
use App\Filament\Resources\Presences\Pages\EditPresence;
use App\Filament\Resources\Presences\Pages\ListPresences;
use App\Filament\Resources\Presences\Pages\ViewPresence;
use App\Filament\Resources\Presences\RelationManagers\StudentPresencesRelationManager;
use App\Filament\Resources\Presences\Schemas\PresenceForm;
use App\Filament\Resources\Presences\Schemas\PresenceInfolist;
use App\Filament\Resources\Presences\Tables\PresencesTable;
use App\Models\Presence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class PresenceResource extends Resource
{
    use ClassAndSubjectManagement;

    protected static ?string $model = Presence::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::ListChecks;

    protected static ?string $recordTitleAttribute = 'date';

    protected static ?string $label = 'Presensi';

    public static function form(Schema $schema): Schema
    {
        return PresenceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PresenceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PresencesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudentPresencesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPresences::route('/'),
            'create' => CreatePresence::route('/create'),
            'view' => ViewPresence::route('/{record}'),
            'edit' => EditPresence::route('/{record}/edit'),
        ];
    }
}
