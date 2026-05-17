<?php

namespace App\Filament\Resources\Presences\RelationManagers;

use App\Enums\PresenceStatus;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class StudentPresencesRelationManager extends RelationManager
{
    protected static string $relationship = 'studentPresences';

    protected static ?string $title = 'Presensi Siswa';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('status'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                ViewColumn::make('status')
                    ->label('Status Kehadiran')
                    ->view('tables.columns.presence-status-checkboxes')
                    ->state(fn ($record) => $record->status instanceof PresenceStatus ? $record->status->value : $record->status)
                    ->viewData([
                        'canUpdatePresence' => true,
                        'statuses' => [
                            PresenceStatus::PRESENT->value => 'Hadir',
                            PresenceStatus::SICK->value => 'Sakit',
                            PresenceStatus::PERMISSION->value => 'Izin',
                            PresenceStatus::LATE->value => 'Terlambat',
                            PresenceStatus::ABSENT->value => 'Alpa',
                        ],
                    ])
                    ->disabledClick(),
                TextInputColumn::make('note')
                    ->placeholder('Masukkan Catatan (Opsional)')
                    ->label('Catatan')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('markAllPresent')
                    ->label('Hadirkan Semua')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function () {
                        $this->getRelationship()->update(['status' => PresenceStatus::PRESENT->value]);

                        Notification::make()
                            ->title('Semua siswa ditandai hadir.')
                            ->success()
                            ->send();
                    }),

                Action::make('save')
                    ->label('Simpan Semua')
                    ->action(function () {
                        Notification::make()
                            ->title('Berhasil menyimpan presensi siswa.')
                            ->success()
                            ->send();

                        // 👇 INI DIA GPS PINTARNYA JON! 👇
                        $presenceType = $this->getOwnerRecord()->type;

                        if ($presenceType === 'mapel') {
                            return redirect(\App\Filament\Resources\SubjectPresences\SubjectPresenceResource::getUrl('index'));
                        } else {
                            return redirect(\App\Filament\Resources\Presences\PresenceResource::getUrl('index'));
                        }
                    })
                    ->color('success'),
            ]);
    }

    public function updatePresenceStatus(int $recordId, string $statusValue): void
    {
        $status = PresenceStatus::tryFrom($statusValue);

        if (! $status) {
            return;
        }

        $record = $this->getRelationship()->find($recordId);

        $currentStatus = $record->status instanceof PresenceStatus ? $record->status->value : $record->status;

        if (! $record || $currentStatus === $statusValue) {
            return;
        }

        $record->update(['status' => $status]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
