<?php

namespace App\Filament\Resources\Classrooms\RelationManagers;

use App\Filament\Resources\Presences\PresenceResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PresencesRelationManager extends RelationManager
{
    protected static string $relationship = 'presences';

    protected static ?string $title = 'Presensi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('classroom.name')
                            ->label('Kelas'),
                        TextEntry::make('date')
                            ->label('Tanggal')
                            ->date(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('l, j F Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Buat Presensi')->visible(Auth::user()->can('Create:Presence')),
            ])
            ->recordActions([
                ViewAction::make()->url(function (PresenceResource $resource, $record) {
                    return $resource->getUrl('view', ['record' => $record]);
                }),
                EditAction::make()->visible(Auth::user()->can('Update:Presence')),
                DeleteAction::make()->visible(Auth::user()->can('Delete:Presence')),
            ])
            ->defaultSort('date', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
