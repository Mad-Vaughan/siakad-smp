<?php

namespace App\Filament\Resources\Presences;

use App\Filament\Concerns\NavigationGrouping\ClassAndSubjectManagement;
use App\Filament\Resources\Presences\Pages\CreatePresence;
use App\Filament\Resources\Presences\Pages\EditPresence;
use App\Filament\Resources\Presences\Pages\ListPresences;
use App\Filament\Resources\Presences\Pages\ViewPresence;
use App\Filament\Resources\Presences\RelationManagers\StudentPresencesRelationManager;
use App\Filament\Resources\Presences\Schemas\PresenceForm;
use App\Filament\Resources\Presences\Tables\PresencesTable;
use App\Models\Presence;
// 👈 PENGAMAN PHP 8.4
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PresenceResource extends Resource
{
    use ClassAndSubjectManagement;

    protected static ?string $model = Presence::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $recordTitleAttribute = 'date';

    protected static ?string $label = 'Presensi Harian';

    protected static ?string $pluralLabel = 'Presensi Harian';

    protected static ?string $navigationLabel = 'Presensi Harian';

    // 👇 URUTAN 5 👇
    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('type', 'harian');

        // Admins see all harian presences
        if (auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin'])) {
            return $query;
        }

        // Teachers: only presences for classes they are wali of
        if (auth()->check() && auth()->user()->hasRole('teacher')) {
            $query->whereHas('classroom', function (Builder $cq) {
                $cq->where('teacher_id', auth()->id());
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
        return PresenceForm::configure($schema);
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
