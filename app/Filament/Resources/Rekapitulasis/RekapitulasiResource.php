<?php

namespace App\Filament\Resources\Rekapitulasis;

use App\Filament\Resources\Rekapitulasis\Pages;
use App\Models\Student;
use App\Models\Presence;
use App\Models\Classroom;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class RekapitulasiResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $modelLabel = 'Rekap Kehadiran';
    
    protected static ?string $pluralModelLabel = 'Daftar Rekap Kehadiran';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-presentation-chart-bar';
    
    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen TU';
    
    protected static ?string $navigationLabel = 'Rekap Kehadiran';
    
    protected static ?string $slug = 'rekap-kehadiran';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->sortable(),
                
                TextColumn::make('h')
                    ->label('H')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => self::getAttendanceCount($record, 'hadir')),
                
                TextColumn::make('s')
                    ->label('S')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => self::getAttendanceCount($record, 'sakit')),

                TextColumn::make('i')
                    ->label('I')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => self::getAttendanceCount($record, 'izin')),

                TextColumn::make('a')
                    ->label('A')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => self::getAttendanceCount($record, 'alpa')),

                TextColumn::make('persentase')
                    ->label('Rata-rata %')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (float)$state >= 90 => 'success',
                        (float)$state >= 75 => 'warning',
                        default => 'danger',
                    })
                    ->getStateUsing(function ($record) {
                        $filters = request()->query('tableFilters');
                        $month = $filters['month']['value'] ?? date('m');
                        $year = $filters['year']['value'] ?? date('Y');
                        
                        $totalHadir = self::getAttendanceCount($record, 'hadir');
                        $totalHari = self::countWorkDays($month, $year);

                        if ($totalHari == 0) return "0%";
                        $persen = ($totalHadir / $totalHari) * 100;
                        return number_format($persen, 1) . '%';
                    }),
            ])
            ->filters([
                SelectFilter::make('classroom_id')
                    ->label('Filter Kelas')
                    ->relationship('classroom', 'name'),
                
                SelectFilter::make('month')
                    ->label('Pilih Bulan')
                    ->options([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                    ])
                    ->default(date('m'))
                    ->query(fn (Builder $query) => $query), 

                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(array_combine(range(2024, 2030), range(2024, 2030)))
                    ->default(date('Y'))
                    ->query(fn (Builder $query) => $query),
            ]);
    }

    protected static function getAttendanceCount($student, $status)
    {
        $filters = request()->query('tableFilters');
        // Pastikan ambil value-nya dengan bener, kalo kaga ada pake default bulan ini
        $month = isset($filters['month']['value']) ? $filters['month']['value'] : date('m');
        $year = isset($filters['year']['value']) ? $filters['year']['value'] : date('Y');

        // Tarik data dari StudentPresence (Anak), cek tanggalnya di Presence (Induk)
        $presences = \App\Models\StudentPresence::where('student_id', $student->id)
            ->whereHas('presence', function ($q) use ($month, $year) {
                $q->whereMonth('date', $month)
                  ->whereYear('date', $year);
            })
            ->get();

        // Pake accessor (hadir, sakit, izin, alpa) yang udah lo bikin di model StudentPresence
        return $presences->filter(function($p) use ($status) {
            if ($status === 'hadir') return $p->hadir;
            if ($status === 'sakit') return $p->sakit;
            if ($status === 'izin') return $p->izin;
            if ($status === 'alpa') return $p->alpa || $p->terlambat; // Terlambat diitung alpa/sesuaikan
            return false;
        })->count();
    }

    protected static function countWorkDays($month, $year)
    {
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $workDays = 0;

        while ($start <= $end) {
            if (!$start->isWeekend()) {
                $workDays++;
            }
            $start->addDay();
        }
        return $workDays;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekapitulasis::route('/'),
        ];
    }
}