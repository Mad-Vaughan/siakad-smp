<?php

namespace App\Filament\Resources\Schedules;

use App\Filament\Concerns\NavigationGrouping\ClassAndSubjectManagement;
use App\Filament\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\Resources\Schedules\Pages\EditSchedule;
use App\Filament\Resources\Schedules\Pages\ListSchedules;
use App\Filament\Resources\Schedules\Schemas\ScheduleForm;
use App\Filament\Resources\Schedules\Tables\SchedulesTable;
use App\Models\Schedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
// 👇 INI WAJIB BIAR KAGA ERROR CLASS BUILDER NOT FOUND 👇
use Illuminate\Database\Eloquent\Builder;

class ScheduleResource extends Resource
{
    // 👇 SUNTIKAN JURUS DEWA 👇
    use ClassAndSubjectManagement;

    protected static ?string $model = Schedule::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Jadwal Pelajaran';

    protected static ?string $pluralModelLabel = 'Jadwal Pelajaran';

    protected static ?string $navigationLabel = 'Jadwal Pelajaran';

    // 👇 URUTAN KE 4 👇
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'id';

    // Access control delegated to Filament Shield

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Admins see everything
        if (auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin'])) {
            return $query->orderByDayAndTime();
        }

        // Restrict to schedules from active academic years
        $query->whereHas('classroom.academicYear', function (Builder $q) {
            $q->where('is_active', true);
        });

        // If teacher, further limit to schedules where teacher is wali kelas OR guru mapel
        if (auth()->check() && auth()->user()->hasRole('teacher')) {
            $query->where(function (Builder $q) {
                $q->whereHas('classroom', fn ($cq) => $cq->where('teacher_id', auth()->id()))
                    ->orWhereHas('subject', fn ($sq) => $sq->where('teacher_id', auth()->id()));
            });
        }

        return $query->orderByDayAndTime();
    }

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }
}
