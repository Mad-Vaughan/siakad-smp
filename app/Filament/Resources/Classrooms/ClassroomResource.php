<?php

namespace App\Filament\Resources\Classrooms;

use App\Filament\Concerns\NavigationGrouping\ClassAndSubjectManagement;
use App\Filament\Resources\Classrooms\Pages\CreateClassroom;
use App\Filament\Resources\Classrooms\Pages\EditClassroom;
use App\Filament\Resources\Classrooms\Pages\ListClassrooms;
use App\Filament\Resources\Classrooms\Pages\ViewClassroom;
use App\Filament\Resources\Classrooms\RelationManagers\AssesmentsRelationManager;
use App\Filament\Resources\Classrooms\RelationManagers\PresencesRelationManager;
use App\Filament\Resources\Classrooms\RelationManagers\StudentsRelationManager;
use App\Filament\Resources\Classrooms\Schemas\ClassroomForm;
use App\Filament\Resources\Classrooms\Schemas\ClassroomInfolist;
use App\Filament\Resources\Classrooms\Tables\ClassroomsTable;
use App\Models\Classroom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class ClassroomResource extends Resource
{
    use ClassAndSubjectManagement;

    protected static ?string $model = Classroom::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::BookBookmark;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Kelas';

    public static function form(Schema $schema): Schema
    {
        return ClassroomForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClassroomInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassroomsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudentsRelationManager::class,
            PresencesRelationManager::class,
            AssesmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassrooms::route('/'),
            'create' => CreateClassroom::route('/create'),
            'view' => ViewClassroom::route('/{record}'),
            'edit' => EditClassroom::route('/{record}/edit'),
        ];
    }
}
