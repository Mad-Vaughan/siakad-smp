<?php

namespace App\Filament\Resources\Presences\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter; // 👇 Wajib dipanggil buat bikin filter dropdown
use Filament\Tables\Table; // 👇 Wajib dipanggil buat query relasi
use Illuminate\Database\Eloquent\Builder;

class PresencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                // 👇 DIBIKIN KEMBAR IDENTIK SAMA MAPEL 👇
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),

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
                // 👇 INI DIA JAWABAN BUAT REVISI DOSPEM LO 👇
                SelectFilter::make('semester')
                    ->label('Filter Semester')
                    ->options([
                        'ganjil' => 'Semester Ganjil',
                        'genap' => 'Semester Genap',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            // Karena Presensi nyambung ke Kelas, dan Kelas nyambung ke Tahun Ajaran
                            // Kita tembusin query-nya langsung ke akar (academicYear)
                            return $query->whereHas('classroom.academicYear', function (Builder $q) use ($data) {
                                $q->where('semester', $data['value']);
                            });
                        }

                        return $query;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('date', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
