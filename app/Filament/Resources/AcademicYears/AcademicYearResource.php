<?php

namespace App\Filament\Resources\AcademicYears;

use App\Filament\Concerns\NavigationGrouping\ClassAndSubjectManagement;
use App\Filament\Resources\AcademicYears\Pages\CreateAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\EditAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\ListAcademicYears;
use App\Filament\Resources\AcademicYears\Pages\ViewAcademicYear;
use App\Filament\Resources\AcademicYears\Schemas\AcademicYearForm;
use App\Filament\Resources\AcademicYears\Schemas\AcademicYearInfolist;
use App\Filament\Resources\AcademicYears\Tables\AcademicYearsTable;
use App\Models\AcademicYear;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class AcademicYearResource extends Resource
{
    use ClassAndSubjectManagement;

    protected static ?string $model = AcademicYear::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::CalendarCheck;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Tahun Ajaran';

    protected static ?string $navigationLabel = 'Tahun Ajaran';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AcademicYearForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AcademicYearInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicYearsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAcademicYears::route('/'),
            'create' => CreateAcademicYear::route('/create'),
            'view' => ViewAcademicYear::route('/{record}'),
            'edit' => EditAcademicYear::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Admins and TU can see all years
        if (auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'tu'])) {
            return $query;
        }

        // Other users: only active academic year(s)
        $query->where('is_active', true);

        return $query;
    }

    // CATATAN JON:
    // Fungsi canCreate, canEdit, dan canDelete SENGAJA DIHAPUS
    // biar Filament Shield (menu centang-centang) yang ambil alih 100%!
}
