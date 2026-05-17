<?php

namespace App\Filament\Resources\Classrooms\RelationManagers;

use App\Enums\AssesmentType;
use App\Filament\Resources\Assesments\AssesmentResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AssesmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assesments';

    protected static ?string $title = 'Penilaian';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 👇 GUE TAMBAHIN NAMA SAMA TANGGAL BIAR KAGA ERROR PAS DISAVE 👇
                TextInput::make('name')
                    ->label('Nama Penilaian/Tugas')
                    ->placeholder('Contoh: Tugas Harian 1')
                    ->required(),

                DatePicker::make('date')
                    ->label('Tanggal Penilaian')
                    ->default(now())
                    ->required(),

                // 👇 SMART FILTER MAPEL (KACAMATA KUDA FIX!) 👇
                Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->options(function (RelationManager $livewire) {
                        $classroomId = $livewire->getOwnerRecord()->id;

                        $query = \App\Models\Subject::query()
                            ->whereHas('classrooms', function (Builder $query) use ($classroomId) {
                                $query->where('classrooms.id', $classroomId);
                            });

                        $user = Auth::user();
                        if ($user && $user->hasRole(['guru', 'teacher']) && ! $user->hasRole(['admin', 'tu'])) {
                            $query->where('teacher_id', $user->id);
                        }

                        return $query->pluck('name', 'id');
                    })
                    ->preload()
                    ->searchable()
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
                TextEntry::make('name')->label('Nama Tugas'),
                TextEntry::make('subject.name')->label('Mata Pelajaran'),
                TextEntry::make('date')->label('Tanggal')->date('d M Y'),
                TextEntry::make('academic_year_id')->label('Tahun Ajaran')->numeric(),
                TextEntry::make('type')->label('Tipe')->badge(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nama Penilaian')->sortable()->searchable(),
                TextColumn::make('date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('subject.name')->label('Mata Pelajaran')->sortable(),
                TextColumn::make('type')->label('Tipe')->badge()->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Guru tetep dapet tombol Create buat ngasih nilai
                CreateAction::make()->label('Buat Penilaian'),
            ])
            ->recordActions([
                ViewAction::make()->url(function (AssesmentResource $resource, $record) {
                    return $resource->getUrl('view', ['record' => $record]);
                }),

                // 👇 GEMBOK FIX BIAR GURU KAGA BISA NGEDIT/NGAPUS (CUMA ADMIN/TU) 👇
                EditAction::make()
                    ->visible(fn () => ! Auth::user()->hasRole(['guru', 'teacher'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // 👇 GEMBOK TONG SAMPAH 👇
                    DeleteBulkAction::make()
                        ->visible(fn () => ! Auth::user()->hasRole(['guru', 'teacher'])),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
