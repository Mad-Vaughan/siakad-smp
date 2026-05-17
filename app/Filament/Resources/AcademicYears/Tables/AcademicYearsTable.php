<?php

namespace App\Filament\Resources\AcademicYears\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AcademicYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tahun Ajaran')
                    ->searchable(),
                TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ganjil' => 'warning',
                        'genap' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),

                // 👇 GEMBOK FIX: Cuma ngeblokir Guru, TU & Admin aman! 👇
                EditAction::make()
                    ->visible(fn () => ! auth()->user()->hasRole(['guru', 'teacher'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([

                    // 👇 GEMBOK FIX: Cuma ngeblokir Guru, TU & Admin aman! 👇
                    DeleteBulkAction::make()
                        ->visible(fn () => ! auth()->user()->hasRole(['guru', 'teacher'])),
                ]),
            ]);
    }
}
