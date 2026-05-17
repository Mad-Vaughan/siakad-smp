<?php

namespace App\Filament\Resources\Teachers\Tables;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Guru')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(function ($state) {
                        $val = strtolower($state instanceof BackedEnum ? $state->value : $state);

                        return match ($val) {
                            'male', 'l' => 'Laki-laki',
                            'female', 'p' => 'Perempuan',
                            default => '-',
                        };
                    })
                    ->searchable(),

                // 👇 INFO GURU NGAJAR MAPEL APA AJA 👇
                TextColumn::make('mata_pelajaran')
                    ->label('Mengajar Mapel')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $subjects = \App\Models\Subject::where('teacher_id', $record->id)
                            ->pluck('name')
                            ->toArray();

                        // 👇 JURUS SAPU BERSIH DUPLIKAT MAPEL 👇
                        $subjects = array_values(array_unique($subjects));

                        return count($subjects) > 0 ? $subjects : ['Belum Ada Mapel'];
                    })
                    ->color(fn (string $state): string => $state === 'Belum Ada Mapel' ? 'danger' : 'success')
                    ->wrap(),

                // 👇 INFO WALI KELAS UDAH GUE BUMI HANGUSKAN DARI SINI JON! 👇
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
