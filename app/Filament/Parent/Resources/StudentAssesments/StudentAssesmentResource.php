<?php

namespace App\Filament\Parent\Resources\StudentAssesments;

use App\Enums\AssesmentType;
use App\Filament\Parent\Resources\StudentAssesments\Pages\ListStudentAssesments;
use App\Models\AcademicYear;
use App\Models\StudentAssesment;
use App\Models\Subject; // 👈 Wajib dipanggil juga buat filter mapel
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                TextColumn::make('assessment.classroom.academicYear.name')
                    ->label('T.A & Semester')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $year = $record->assessment?->classroom?->academicYear;
                        if (! $year) return '-';
                        $semester = ucfirst(strtolower($year->semester ?? 'belum diketahui'));
                        return "{$year->name} - {$semester}";
                    }),
                TextColumn::make('score')
                    ->label('Nilai')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Catatan')
                    ->placeholder('-')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                // 👇 FILTER 1: TAHUN AJARAN 👇
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(fn () => AcademicYear::all()->mapWithKeys(fn ($item) => [$item->id => "{$item->name} - ".ucfirst($item->semester)])->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            return $query->whereHas('assessment', fn ($q) => $q->where('academic_year_id', $data['value']));
                        }

                        return $query;
                    })
                    ->native(false),

                // 👇 FILTER 2: MATA PELAJARAN 👇
                SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->options(fn () => Subject::pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            return $query->whereHas('assessment', fn ($q) => $q->where('subject_id', $data['value']));
                        }

                        return $query;
                    })
                    ->searchable()
                    ->native(false),

                // 👇 FILTER 3: JENIS PENILAIAN (UTS/UAS/dll) 👇
                SelectFilter::make('type')
                    ->label('Jenis Penilaian')
                    ->options(AssesmentType::class)
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            return $query->whereHas('assessment', fn ($q) => $q->where('type', $data['value']));
                        }

                        return $query;
                    })
                    ->native(false),
            ])
            ->defaultSort('updated_at', 'desc')
            ->paginated()
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['assessment.subject', 'assessment.classroom']);

        $user = Filament::auth()?->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['admin', 'super_admin', 'tu'])) {
            return $query;
        }

        if ($user->hasRole('teacher')) {
            return $query->whereHas('assessment', function (Builder $assessmentQuery) use ($user) {
                $assessmentQuery->where('teacher_id', $user->id)
                    ->orWhereHas('classroom', fn (Builder $classroomQuery) => $classroomQuery->where('teacher_id', $user->id));
            });
        }

        return $query->where('student_id', $user->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentAssesments::route('/'),
        ];
    }
}
