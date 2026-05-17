<?php

namespace App\Filament\Resources\Subjects;

use App\Filament\Concerns\NavigationGrouping\ClassAndSubjectManagement;
use App\Filament\Resources\Subjects\Pages\CreateSubject;
use App\Filament\Resources\Subjects\Pages\EditSubject;
use App\Filament\Resources\Subjects\Pages\ListSubjects;
use App\Filament\Resources\Subjects\Pages\ViewSubject;
use App\Filament\Resources\Subjects\Schemas\SubjectForm;
use App\Filament\Resources\Subjects\Schemas\SubjectInfolist;
use App\Filament\Resources\Subjects\Tables\SubjectsTable;
use App\Models\Subject;
use BackedEnum;
// 👈 PENGAMAN PHP 8.4
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class SubjectResource extends Resource
{
    use ClassAndSubjectManagement;

    protected static ?string $model = Subject::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::BookOpen;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Mata Pelajaran';

    protected static ?string $navigationLabel = 'Mata Pelajaran';

    // 👇 URUTAN 2 👇
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SubjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubjectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubjects::route('/'),
            'create' => CreateSubject::route('/create'),
            'view' => ViewSubject::route('/{record}'),
            'edit' => EditSubject::route('/{record}/edit'),
        ];
    }

    // 👇 JURUS MASTER DATA ABADI 👇
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Kalo yang login Admin atau TU, bebasin liat semua Mapel (Namanya juga Master Data Katalog)
        if (auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'tu'])) {
            return $query;
        }

        // Kalo yang login Guru: cuma liat mapel yang dia pegang
        if (auth()->check() && auth()->user()->hasRole('teacher')) {
            $query->where('teacher_id', auth()->id());
        }

        return $query;
    }

    // Access and navigation handled by Filament Shield
}
