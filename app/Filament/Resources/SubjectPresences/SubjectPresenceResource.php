<?php

namespace App\Filament\Resources\SubjectPresences;

use App\Filament\Concerns\NavigationGrouping\ClassAndSubjectManagement;
use App\Filament\Resources\Presences\RelationManagers\StudentPresencesRelationManager;
use App\Filament\Resources\SubjectPresences\Schemas\SubjectPresenceForm;
use App\Filament\Resources\SubjectPresences\Tables\SubjectPresencesTable;
use App\Models\Presence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubjectPresenceResource extends Resource
{
    use ClassAndSubjectManagement;

    protected static ?string $model = Presence::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $label = 'Presensi Mata Pelajaran';

    protected static ?string $pluralLabel = 'Presensi Mata Pelajaran';

    protected static ?string $navigationLabel = 'Presensi Mata Pelajaran';

    // 👇 URUTAN KE 6 👇
    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('type', 'mapel');

        // Admins see all mapel presences
        if (auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin'])) {
            return $query;
        }

        // Teachers: only presences for subjects they teach
        if (auth()->check() && auth()->user()->hasRole('teacher')) {
            $query->whereHas('schedule.subject', function (Builder $sq) {
                $sq->where('teacher_id', auth()->id());
            });
        }

        // Non-admins: only presences in active academic years
        $query->whereHas('classroom.academicYear', function (Builder $q) {
            $q->where('is_active', true);
        });

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return SubjectPresenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubjectPresencesTable::configure($table);
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
            'index' => Pages\ListSubjectPresences::route('/'),
            'create' => Pages\CreateSubjectPresence::route('/create'),
            'edit' => Pages\EditSubjectPresence::route('/{record}/edit'),
        ];
    }
}
