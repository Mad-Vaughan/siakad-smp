<?php

namespace App\Filament\Parent\Resources\StudentPresences;

use App\Enums\PresenceStatus;
use App\Filament\Parent\Resources\StudentPresences\Pages\ListStudentPresences;
use App\Models\StudentPresence;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentPresenceResource extends Resource
{
    protected static ?string $model = StudentPresence::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Kehadiran';

    protected static ?string $pluralModelLabel = 'Kehadiran';

    protected static string|UnitEnum|null $navigationGroup = 'Informasi Akademik';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('presence.date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('presence.classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (?PresenceStatus $state) => $state?->getIcon())
                    ->color(fn (?PresenceStatus $state) => $state?->getColor())
                    ->formatStateUsing(fn (?PresenceStatus $state) => $state?->getLabel()),
            ])
            ->defaultSort('presence.date', 'desc')
            ->paginated()
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['presence.classroom']);

        $userId = Filament::auth()?->id();

        return $query->when(
            $userId,
            fn (Builder $builder) => $builder->where('student_id', $userId),
            fn (Builder $builder) => $builder->whereRaw('1 = 0'),
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentPresences::route('/'),
        ];
    }
}
