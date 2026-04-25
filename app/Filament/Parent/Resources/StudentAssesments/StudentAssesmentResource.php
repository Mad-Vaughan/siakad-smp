<?php

namespace App\Filament\Parent\Resources\StudentAssesments;

use App\Enums\AssesmentType;
use App\Filament\Parent\Resources\StudentAssesments\Pages\ListStudentAssesments;
use App\Models\StudentAssesment;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentAssesmentResource extends Resource
{
    protected static ?string $model = StudentAssesment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Penilaian';

    protected static ?string $pluralModelLabel = 'Penilaian';

    protected static string|UnitEnum|null $navigationGroup = 'Informasi Akademik';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('assessment.subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assessment.classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->sortable(),
                TextColumn::make('assessment.type')
                    ->label('Jenis Penilaian')
                    ->badge()
                    ->icon(fn (?AssesmentType $state) => $state?->getIcon())
                    ->color(fn (?AssesmentType $state) => $state?->getColor())
                    ->formatStateUsing(fn (?AssesmentType $state) => $state?->getLabel()),
                TextColumn::make('score')
                    ->label('Nilai')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->paginated()
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['assessment.subject', 'assessment.classroom']);

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
            'index' => ListStudentAssesments::route('/'),
        ];
    }
}
