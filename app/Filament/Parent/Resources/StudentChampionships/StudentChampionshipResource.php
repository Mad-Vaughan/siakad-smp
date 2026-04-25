<?php

namespace App\Filament\Parent\Resources\StudentChampionships;

use App\Enums\ChampionLevel;
use App\Enums\ChampionType;
use App\Filament\Parent\Resources\StudentChampionships\Pages\ListStudentChampionships;
use App\Models\Championship;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentChampionshipResource extends Resource
{
    protected static ?string $model = Championship::class;

    protected static bool $shouldSkipAuthorization = true;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Kejuaraan';

    protected static ?string $pluralModelLabel = 'Kejuaraan';

    protected static string|UnitEnum|null $navigationGroup = 'Informasi Akademik';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kejuaraan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->label('Tahun')
                    ->badge()
                    ->sortable(),
                TextColumn::make('level')
                    ->label('Tingkat')
                    ->badge()
                    ->icon(fn (?ChampionLevel $state) => $state?->getIcon())
                    ->color(fn (?ChampionLevel $state) => $state?->getColor())
                    ->formatStateUsing(fn (?ChampionLevel $state) => $state?->getLabel()),
                TextColumn::make('type')
                    ->label('Kategori')
                    ->badge()
                    ->icon(fn (?ChampionType $state) => $state?->getIcon())
                    ->color(fn (?ChampionType $state) => $state?->getColor())
                    ->formatStateUsing(fn (?ChampionType $state) => $state?->getLabel()),
                TextColumn::make('position')
                    ->label('Peringkat')
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('year', 'desc')
            ->paginated()
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

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
            'index' => ListStudentChampionships::route('/'),
        ];
    }
}
