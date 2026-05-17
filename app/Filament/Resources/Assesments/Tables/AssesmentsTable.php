<?php

namespace App\Filament\Resources\Assesments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssesmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 👇 NAMA TUGAS (Paling Penting Biar Kaga Bingung) 👇
                TextColumn::make('name')
                    ->label('Nama Penilaian')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // 👇 TANGGAL (Biar Jelas Kapan Diambil) 👇
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                // 👇 NAMA GURU (Biar Admin Tau Siapa Yang Ngasih Nilai) 👇
                TextColumn::make('subject.teacher.name')
                    ->label('Guru Pengampu')
                    ->searchable()
                    ->sortable()
                    ->toggleable(), // Bisa disembunyiin/ditampilin sama user

                // 👇 TAHUN AJARAN / SEMESTER 👇
                TextColumn::make('academicYear.name')
                    ->label('Semester')
                    ->badge()
                    ->color(fn ($record) => match (strtolower($record->academicYear?->semester ?? '')) {
                        'ganjil' => 'warning',
                        'genap' => 'success',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state, $record): string => $state.' - '.ucfirst($record->academicYear?->semester ?? ''))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sengaja diumpetin dari awal biar tabel kaga kepenuhan, tapi bisa dimunculin

                TextColumn::make('type')
                    ->badge()
                    ->label('Tipe')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
