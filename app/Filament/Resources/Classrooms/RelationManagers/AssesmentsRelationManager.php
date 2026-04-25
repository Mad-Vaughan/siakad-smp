<?php

namespace App\Filament\Resources\Classrooms\RelationManagers;

use App\Enums\AssesmentType;
use App\Filament\Resources\Assesments\AssesmentResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AssesmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assesments';

    protected static ?string $title = 'Penilaian';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->preload()
                    ->required(),
                Select::make('type')
                    ->label('Tipe')
                    ->options(AssesmentType::class)
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('subject.name')
                    ->label('Mata Pelajaran'),
                TextEntry::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->numeric(),
                TextEntry::make('type')
                    ->label('Tipe')
                    ->badge(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Buat Penilaian')->visible(Auth::user()->can('Create:Assesment')),
            ])
            ->recordActions([
                ViewAction::make()->url(function (AssesmentResource $resource, $record) {
                    return $resource->getUrl('view', ['record' => $record]);
                }),
                EditAction::make()->visible(Auth::user()->can('Update:Assesment')),
            ])
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
