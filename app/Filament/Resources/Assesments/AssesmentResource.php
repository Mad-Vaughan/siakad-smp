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
use App\Models\Teacher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class AssesmentResource extends Resource
{
    use AssesmentAndChampionshipGrouping;

    protected static ?string $model = Assesment::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::ClipboardText;

    protected static ?string $label = 'Penilaian';

    // Navigation registration delegated to Filament Shield

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'tu', 'teacher']);
    }

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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['admin', 'super_admin', 'tu'])) {
            return $query;
        }

        // Always restrict to active academic year for non-admin users
        $query->whereHas('academicYear', function (Builder $q) {
            $q->where('is_active', true);
        });

        if ($user->hasAnyRole(['guru', 'teacher'])) {
            // Resolve teacher id: prefer a teacher relation if present, otherwise fall back to user id
            $teacherId = $user->teacher?->id ?? $user->id;

            if ($teacherId) {
                // ONLY show assessments where the teacher is the subject's teacher
                $query->whereHas('subject', fn (Builder $sq) => $sq->where('teacher_id', $teacherId));
            } else {
                // If we can't determine teacher id, deny access
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }
}
