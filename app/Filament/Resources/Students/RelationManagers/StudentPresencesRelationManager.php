<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Enums\PresenceStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                TextColumn::make('presence.classroom.name')
                    ->label('Kelas'),
                TextColumn::make('presence.date')
                    ->label('Tanggal')
                    ->date('l, j F Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('note')
                    ->label('Catatan')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PresenceStatus::class),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
