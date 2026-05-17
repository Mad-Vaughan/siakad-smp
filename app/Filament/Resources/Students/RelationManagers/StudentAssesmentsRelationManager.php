<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Models\AcademicYear;
use App\Models\Subject;
use BackedEnum; // 👈 WAJIB IMPORT INI
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn; // 👈 WAJIB IMPORT INI
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentAssesmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentAssesments';

    protected static ?string $title = 'Riwayat Nilai & Tugas';

    protected static string|BackedEnum|null $icon = 'heroicon-o-academic-cap';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                // 👇 TAHUN AJARAN & SEMESTER MASUK SINI JON 👇
                TextColumn::make('assessment.academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),

                TextColumn::make('assessment.academicYear.semester')
                    ->label('Semester')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'ganjil' => 'info',
                        'genap' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('assessment.subject.name')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('assessment.type')
                    ->label('Jenis Penilaian')
                    ->badge()
                    ->sortable(),

                TextColumn::make('score')
                    ->label('Nilai Diperoleh')
                    ->weight('bold')
                    ->size('lg')
                    ->color(fn ($state) => $state >= 75 ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('note')
                    ->label('Catatan Guru')
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->filters([
                // 👇 FILTER MATA PELAJARAN 👇
                SelectFilter::make('subject_id')
                    ->label('Filter Mapel')
                    ->options(fn () => Subject::pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['value'])) {
                            $query->whereHas('assessment', fn ($q) => $q->where('subject_id', $data['value']));
                        }
                    }),

                // 👇 FILTER TAHUN AJARAN 👇
                SelectFilter::make('academic_year_id')
                    ->label('Filter Tahun/Semester')
                    ->options(fn () => AcademicYear::all()->mapWithKeys(fn ($ay) => [$ay->id => $ay->name.' ('.ucfirst($ay->semester).')'])->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['value'])) {
                            $query->whereHas('assessment', fn ($q) => $q->where('academic_year_id', $data['value']));
                        }
                    }),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
