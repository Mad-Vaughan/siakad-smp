<?php

namespace App\Filament\Resources\StudentClassrooms;

use App\Filament\Resources\StudentClassrooms\Pages\CreateStudentClassroom;
use App\Filament\Resources\StudentClassrooms\Pages\EditStudentClassroom;
use App\Filament\Resources\StudentClassrooms\Pages\ListStudentClassrooms;
use App\Filament\Resources\StudentClassrooms\Pages\ViewStudentClassroom;
use App\Filament\Resources\StudentClassrooms\Schemas\StudentClassroomForm;
use App\Filament\Resources\StudentClassrooms\Schemas\StudentClassroomInfolist;
use App\Filament\Resources\StudentClassrooms\Tables\StudentClassroomsTable;
use App\Models\StudentClassroom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudentClassroomResource extends Resource
{
    protected static ?string $model = StudentClassroom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $label = 'Siswa di Kelas';

    public static function form(Schema $schema): Schema
    {
        return StudentClassroomForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentClassroomInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentClassroomsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentClassrooms::route('/'),
            'create' => CreateStudentClassroom::route('/create'),
            'view' => ViewStudentClassroom::route('/{record}'),
            'edit' => EditStudentClassroom::route('/{record}/edit'),
        ];
    }
}
