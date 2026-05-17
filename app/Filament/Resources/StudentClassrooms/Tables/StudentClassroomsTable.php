<?php

namespace App\Filament\Resources\StudentClassrooms\Tables;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class StudentClassroomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // 👇 JURUS ANTI-MUMET: Misahin anak per Kelas (Ini yang lu butuhin) 👇
            ->defaultGroup('classroom.name')
            ->groups([
                Group::make('classroom.name')
                    ->label('Daftar Siswa Kelas')
                    ->collapsible(),
            ])
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                // 👇 INFO TAHUN AJARAN 👇
                TextColumn::make('classroom.academicYear.name')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        $tahun = $record->classroom->academicYear->name ?? '-';
                        $semester = ucfirst($record->classroom->academicYear->semester ?? '');

                        return "{$tahun} ({$semester})";
                    })
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('academic_year')
                    ->label('Filter Tahun Ajaran')
                    ->relationship('classroom.academicYear', 'name')
                    ->searchable()
                    ->preload(),
            ])
            // 👇 ACTION ASLI LU 100%, GUE KAGA SENTUH SAMA SEKALI 👇
            ->recordActions([
                Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->action(fn ($record) => redirect(StudentResource::getUrl('view', ['record' => $record->student_id]))),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
