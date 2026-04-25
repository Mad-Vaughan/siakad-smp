<?php

namespace App\Filament\Resources\Assesments\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StudentAssesmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentAssesments';

    protected static ?string $title = 'Nilai Siswa';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student.name')
                    ->label('Nama Siswa'),
                TextEntry::make('score')
                    ->label('Nilai'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('score')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->sortable(),
                TextInputColumn::make('score')
                    ->disabled(fn () => ! Auth::user()->can('Update:Assesment'))
                    ->placeholder('Masukkan Nilai')
                    ->rules(['required', 'numeric', 'min:0', 'max:100'])
                    ->label('Nilai')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->headerActions([
                Action::make('save')
                    ->label('Simpan Semua')
                    ->action(function () {
                        Notification::make()
                            ->title('Berhasil menyimpan nilai siswa.')
                            ->success()
                            ->send();
                    })
                    ->color('success'),
            ])
            ->paginated(false)
            ->toolbarActions([]);
    }
}
