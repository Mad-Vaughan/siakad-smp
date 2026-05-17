<?php

namespace App\Filament\Resources\ArsipRapors\Tables;

use App\Models\AcademicYear;
use App\Models\StudentAssesment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArsipRaporsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['student', 'classroom.academicYear']))
            ->columns([
                TextColumn::make('classroom.academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),

                TextColumn::make('classroom.academicYear.semester')
                    ->label('Semester')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => strtolower($state) === 'ganjil' ? 'warning' : 'success'),

                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->searchable(),

                TextColumn::make('classroom.name')
                    ->label('Kelas Sejarah')
                    ->badge()
                    ->color('info')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('academic_year')
                    ->label('Filter Tahun Ajaran')
                    ->options(fn () => AcademicYear::all()->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['value'])) {
                            $query->whereHas('classroom', fn ($q) => $q->where('academic_year_id', $data['value']));
                        }
                    }),
            ])
            ->actions([
                // 👇 JURUS BYPASS FULL ALAMAT! 👇
                \Filament\Tables\Actions\Action::make('cetak_rapor')
                    ->label('Cetak Rapor')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function ($record) {

                        $student = $record->student;
                        $classroom = $record->classroom;

                        $assessments = StudentAssesment::where('student_id', $student->id)
                            ->whereHas('assessment', function ($q) use ($classroom) {
                                $q->where('classroom_id', $classroom->id);
                            })->get();

                        \Filament\Notifications\Notification::make()
                            ->title('Data Nilai Ditemukan!')
                            ->body('Si '.$student->name.' punya '.$assessments->count().' nilai di kelas ini.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }
}
