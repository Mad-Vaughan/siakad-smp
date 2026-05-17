<?php

namespace App\Filament\Resources\Classrooms\Tables;

// 👇 KITA BALIKIN KE IMPORT V4 YANG BENER BIAR KAGA ERROR 👇
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassroomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),
                TextColumn::make('academicYear.semester')
                    ->label('Semester')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'ganjil' => 'warning',
                        'genap' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('teacher.name')
                    ->label('Wali Kelas')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable(),
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
