<?php

namespace App\Filament\Resources\Classrooms\RelationManagers;

use App\Filament\Resources\StudentClassrooms\StudentClassroomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentClassrooms';

    protected static ?string $relatedResource = StudentClassroomResource::class;

    protected static ?string $title = 'Siswa';

    protected static ?string $label = 'Siswa';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()->label('Tambah Siswa'),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
