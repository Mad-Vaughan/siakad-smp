<?php

namespace App\Filament\Resources\Rekapitulasis;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentClassroom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RekapitulasiResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $modelLabel = 'Rekap Kehadiran Dan Nilai';

    protected static ?string $pluralModelLabel = 'Daftar Rekap Kehadiran Dan Rata Rata Nilai';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen TU';

    protected static ?string $navigationLabel = 'Rekap Kehadiran Dan Nilai';

    protected static ?string $slug = 'rekap-kehadiran';

    public static function getPermissionPrefix(): string
    {
        return 'rekapitulasi';
    }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'tu']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'tu']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classroom_name')
                    ->label('Kelas')
                    ->getStateUsing(function ($record, $livewire) {
                        $yearId = self::getAcademicYearFilterState($livewire);

                        if ($yearId) {
                            $classroomAssignment = StudentClassroom::where('student_id', $record->id)
                                ->whereHas('classroom', fn ($query) => $query->where('academic_year_id', $yearId))
                                ->with('classroom')
                                ->first();

                            if ($classroomAssignment?->classroom?->name) {
                                return $classroomAssignment->classroom->name;
                            }
                        }

                        $activeAssignment = StudentClassroom::where('student_id', $record->id)
                            ->where('is_active', true)
                            ->with('classroom')
                            ->first();

                        return $activeAssignment?->classroom?->name ?? '-';
                    }),

                TextColumn::make('h')
                    ->label('H')
                    ->alignCenter()
                    ->getStateUsing(function ($record, $livewire) {
                        $yearId = self::getAcademicYearFilterState($livewire);

                        return self::getAttendanceData($record, $yearId)['hadir'];
                    }),

                TextColumn::make('s')
                    ->label('S')
                    ->alignCenter()
                    ->getStateUsing(function ($record, $livewire) {
                        $yearId = self::getAcademicYearFilterState($livewire);

                        return self::getAttendanceData($record, $yearId)['sakit'];
                    }),

                TextColumn::make('i')
                    ->label('I')
                    ->alignCenter()
                    ->getStateUsing(function ($record, $livewire) {
                        $yearId = self::getAcademicYearFilterState($livewire);

                        return self::getAttendanceData($record, $yearId)['izin'];
                    }),

                TextColumn::make('a')
                    ->label('A')
                    ->alignCenter()
                    ->getStateUsing(function ($record, $livewire) {
                        $yearId = self::getAcademicYearFilterState($livewire);

                        return self::getAttendanceData($record, $yearId)['alpa'];
                    }),

                TextColumn::make('persentase')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('Kehadiran (%)')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (float) $state >= 90 => 'success',
                        (float) $state >= 75 => 'warning',
                        default => 'danger',
                    })
                    ->getStateUsing(function ($record, $livewire) {
                        $yearId = self::getAcademicYearFilterState($livewire);
                        $data = self::getAttendanceData($record, $yearId);

                        $totalHadir = $data['hadir'];
                        $totalPertemuan = $data['total_pertemuan'];

                        if ($totalPertemuan === 0) {
                            return '0%';
                        }

                        $persen = ($totalHadir / $totalPertemuan) * 100;

                        return number_format($persen, 1).'%';
                    }),

                TextColumn::make('rata_rata_nilai')
                    ->label('Rata-rata Nilai')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record, $livewire) {
                        $yearId = self::getAcademicYearFilterState($livewire);

                        return self::getAverageScore($record, $yearId);
                    }),
            ])
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran & Semester')
                    ->options(fn () => AcademicYear::all()->mapWithKeys(fn ($item) => [$item->id => "{$item->name} - ".ucfirst($item->semester)])->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            return $query->whereHas('studentClassrooms', function (Builder $q) use ($data) {
                                $q->whereHas('classroom', fn ($cq) => $cq->where('academic_year_id', $data['value']));
                            });
                        }

                        return $query;
                    })
                    ->native(false),

                SelectFilter::make('classroom_name')
                    ->label('Filter Kelas')
                    ->options(fn () => Classroom::select('name')->distinct()->pluck('name', 'name')->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            return $query->whereHas('studentClassrooms', function (Builder $q) use ($data) {
                                $q->whereHas('classroom', fn ($cq) => $cq->where('name', $data['value']));
                            });
                        }

                        return $query;
                    })
                    ->searchable()
                    ->native(false),
            ]);
    }

    protected static function getAcademicYearFilterState($livewire): ?int
    {
        $filterState = $livewire->getTableFilterState('academic_year_id');

        if (is_array($filterState)) {
            return $filterState['value'] ?? null;
        }

        if (is_object($filterState) && property_exists($filterState, 'value')) {
            return $filterState->value;
        }

        return $filterState;
    }

    protected static function getAttendanceData($student, $academicYearId = null)
    {
        // 1. Kita bikin Base Query langsung tembus ke Database (Bypass PHP Enum)
        $baseQuery = \App\Models\StudentPresence::where('student_id', $student->id)
            ->whereHas('presence', function ($q) use ($academicYearId) {

                // 👇 INI OBAT ANTI INFLASINYA JON! Cuma ngitung Harian 👇
                $q->where('type', 'harian');

                if ($academicYearId) {
                    $q->where('academic_year_id', $academicYearId);
                } else {
                    $q->whereHas('academicYear', fn ($ay) => $ay->where('is_active', true));
                }
            });

        // 2. Hitung langsung di database pake whereIn (10.000% Aman dari 0 0 0)
        return [
            'total_pertemuan' => (clone $baseQuery)->count(),
            'hadir' => (clone $baseQuery)->whereIn('status', ['present', 'hadir', 'Hadir'])->count(),
            'sakit' => (clone $baseQuery)->whereIn('status', ['sick', 'sakit', 'Sakit'])->count(),
            'izin' => (clone $baseQuery)->whereIn('status', ['permission', 'izin', 'Izin'])->count(),
            'alpa' => (clone $baseQuery)->whereIn('status', ['absent', 'late', 'alpa', 'terlambat'])->count(),
        ];
    }

    protected static function getAverageScore($student, $academicYearId = null)
    {
        $assesmentsQuery = \App\Models\StudentAssesment::where('student_id', $student->id)
            ->whereHas('assessment', function ($q) use ($academicYearId) {
                if ($academicYearId) {
                    $q->where('academic_year_id', $academicYearId);
                } else {
                    $q->whereHas('academicYear', fn ($ay) => $ay->where('is_active', true));
                }
            });

        $avg = $assesmentsQuery->avg('score') ?? 0;

        return number_format($avg, 2);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekapitulasis::route('/'),
        ];
    }
}
