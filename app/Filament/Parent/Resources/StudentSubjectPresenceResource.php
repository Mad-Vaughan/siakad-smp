<?php

namespace App\Filament\Parent\Resources;

use App\Enums\PresenceStatus;
use App\Filament\Parent\Resources\StudentSubjectPresenceResource\Pages\ListStudentSubjectPresences;
use App\Models\AcademicYear;
use App\Models\StudentPresence;
use App\Models\Subject; // 👈 Wajib dipanggil buat narik data mapel
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentSubjectPresenceResource extends Resource
{
    protected static ?string $model = StudentPresence::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Kehadiran Mapel';

    protected static ?string $pluralModelLabel = 'Catatan Kehadiran Mata Pelajaran';

    protected static string|UnitEnum|null $navigationGroup = 'Informasi Akademik';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('presence.date')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->sortable(),

                TextColumn::make('presence.schedule.subject.name')
                    ->label('Mata Pelajaran')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('presence.classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (?PresenceStatus $state) => $state?->getIcon())
                    ->color(fn (?PresenceStatus $state) => $state?->getColor()),
                TextColumn::make('presence.classroom.academicYear.name')
                    ->label('T.A & Semester')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $year = $record->presence?->classroom?->academicYear;
                        if (! $year) return '-';
                        $semester = ucfirst(strtolower($year->semester ?? 'Belum Diketahui'));
                        return "{$year->name} - {$semester}";
                }),

                TextColumn::make('note')
                    ->label('Catatan Guru')
                    ->placeholder('-')
                    ->wrap(),
            ])
            ->filters([
                // 👇 FILTER 1: TAHUN AJARAN 👇
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(fn () => AcademicYear::all()->mapWithKeys(fn ($item) => [$item->id => "{$item->name} - ".ucfirst($item->semester)])->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            return $query->whereHas('presence', fn ($q) => $q->where('academic_year_id', $data['value']));
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
                            // Tembus 2 layer: student_presence -> presence -> schedule -> subject_id
                            return $query->whereHas('presence.schedule', fn ($q) => $q->where('subject_id', $data['value']));
                        }

                        return $query;
                    })
                    ->searchable()
                    ->native(false),

                // 👇 FILTER 3: STATUS KEHADIRAN 👇
                SelectFilter::make('status')
                    ->label('Status Kehadiran')
                    ->options(PresenceStatus::class)
                    ->native(false),
            ])
            ->defaultSort('presence.date', 'desc')
            ->paginated()
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = Filament::auth()?->id();

        return parent::getEloquentQuery()
            ->with(['presence.classroom', 'presence.schedule.subject'])
            ->where('student_id', $userId)
            ->whereHas('presence', function (Builder $query) {
                $query->where('type', 'mapel');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentSubjectPresences::route('/'),
        ];
    }
}
