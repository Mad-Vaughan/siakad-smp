<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Enums\PresenceStatus;
use App\Models\AcademicYear;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// 👈 Buat manggil data tahun ajaran di filter

class StudentPresencesRelationManager extends RelationManager
{
    protected static string $relationship = 'studentPresences';

    protected static ?string $title = 'Riwayat Presensi';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('presence.type')
                            ->label('Tipe Presensi')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                            ->color(fn (string $state): string => match ($state) {
                                'harian' => 'success',
                                'mapel' => 'info',
                                default => 'gray',
                            }),
                        TextEntry::make('presence.schedule.subject.name')
                            ->label('Mata Pelajaran')
                            ->placeholder('Harian (Tanpa Mapel)'),
                        TextEntry::make('presence.date')
                            ->label('Tanggal'),
                        TextEntry::make('status')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('note')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('presence.type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'harian' => 'success',
                        'mapel' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('presence.schedule.subject.name')
                    ->label('Mata Pelajaran')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('presence.classroom.name')
                    ->label('Kelas'),

                // 👇 INI DIA JON TAMBAHAN TAHUN AJARAN & SEMESTERNYA 👇
                TextColumn::make('presence.classroom.academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),

                TextColumn::make('presence.classroom.academicYear.semester')
                    ->label('Semester')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'ganjil' => 'info',
                        'genap' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('presence.date')
                    ->label('Tanggal')
                    ->date('l, j F Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->searchable(),

                TextColumn::make('note')
                    ->label('Catatan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Tipe')
                    ->options([
                        'harian' => 'Harian',
                        'mapel' => 'Mata Pelajaran',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['value'])) {
                            $query->whereHas('presence', fn ($q) => $q->where('type', $data['value']));
                        }
                    }),

                // 👇 FILTER TAHUN AJARAN BIAR KAGA CAMPUR ADUK 👇
                SelectFilter::make('academic_year')
                    ->label('Filter Tahun Ajaran')
                    ->options(fn () => AcademicYear::all()->mapWithKeys(fn ($ay) => [$ay->id => $ay->name.' ('.ucfirst($ay->semester).')'])->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['value'])) {
                            $query->whereHas('presence.classroom', fn ($q) => $q->where('academic_year_id', $data['value']));
                        }
                    }),

                SelectFilter::make('status')
                    ->options(PresenceStatus::class),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
