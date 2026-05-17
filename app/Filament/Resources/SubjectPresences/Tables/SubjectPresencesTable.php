<?php

namespace App\Filament\Resources\SubjectPresences\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubjectPresencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(
                fn ($record): string => \App\Filament\Resources\SubjectPresences\SubjectPresenceResource::getUrl('edit', ['record' => $record])
            )
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('schedule.subject.name')
                    ->label('Mata Pelajaran')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                // 👇 DIBIKIN RAPI: Tahun Ajaran Sendiri 👇
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),

                // 👇 DIBIKIN RAPI: Semester Pake Warna 👇
                TextColumn::make('academicYear.semester')
                    ->label('Semester')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'ganjil' => 'info',
                        'genap' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->sortable(),
            ])
            ->filters([
                // 👇 FILTER BIAR BISA LIAT SEMESTER LALU 👇
                SelectFilter::make('academic_year_id')
                    ->label('Filter Tahun/Semester')
                    ->relationship('academicYear', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - ".ucfirst($record->semester))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('classroom_id')
                    ->label('Filter Kelas')
                    ->relationship('classroom', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
