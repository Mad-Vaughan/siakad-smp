<?php

namespace App\Filament\Parent\Resources\StudentPresences;

use App\Enums\PresenceStatus;
use App\Filament\Parent\Resources\StudentPresences\Pages\ListStudentPresences;
use App\Models\AcademicYear;
use App\Models\StudentPresence;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentPresenceResource extends Resource
{
    protected static ?string $model = StudentPresence::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Kehadiran Siswa';

    protected static ?string $pluralModelLabel = 'Catatan Kehadiran Harian';

    protected static string|UnitEnum|null $navigationGroup = 'Informasi Akademik';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('presence.date')
                    ->label('Tanggal')
                    ->date('d F Y') // Biar format tanggalnya cakep
                    ->sortable(),

                TextColumn::make('presence.classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status Kehadiran')
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
                        $semester = ucfirst(strtolower($year->semester ?? 'belum diketahui'));
                        return "{$year->name} - {$semester}";
                    }),

                TextColumn::make('note')
                    ->label('Keterangan')
                    ->placeholder('Tidak ada catatan')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
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
            ])
            ->defaultSort('presence.date', 'desc')
            ->paginated()
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        // 👇 INI GEMBOKNYA JON! WAJIB TYPE HARIAN! 👇
        $query = parent::getEloquentQuery()
            ->with(['presence.classroom'])
            ->whereHas('presence', function (Builder $query) {
                $query->where('type', 'harian');
            });

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
