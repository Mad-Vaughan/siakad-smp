<?php

namespace App\Filament\Resources\Assesments;

use App\Filament\Concerns\NavigationGrouping\AssesmentAndChampionshipGrouping;
use App\Filament\Resources\Assesments\Pages\CreateAssesment;
use App\Filament\Resources\Assesments\Pages\EditAssesment;
use App\Filament\Resources\Assesments\Pages\ListAssesments;
use App\Filament\Resources\Assesments\Pages\ViewAssesment;
use App\Filament\Resources\Assesments\RelationManagers\StudentAssesmentsRelationManager;
use App\Filament\Resources\Assesments\Schemas\AssesmentForm;
use App\Filament\Resources\Assesments\Schemas\AssesmentInfolist;
use App\Filament\Resources\Assesments\Tables\AssesmentsTable;
use App\Models\Assesment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class AssesmentResource extends Resource
{
    use AssesmentAndChampionshipGrouping;

    protected static ?string $model = Assesment::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::ClipboardText;

    protected static ?string $label = 'Penilaian';

    public static function form(Schema $schema): Schema
    {
        return AssesmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssesmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssesmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudentAssesmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssesments::route('/'),
            'create' => CreateAssesment::route('/create'),
            'view' => ViewAssesment::route('/{record}'),
            'edit' => EditAssesment::route('/{record}/edit'),
        ];
    }
}
