<?php

namespace App\Filament\Resources\Students;

use App\Filament\Concerns\NavigationGrouping\UserManagementGrouping;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\RelationManagers\StudentAssesmentsRelationManager;
use App\Filament\Resources\Students\RelationManagers\StudentPresencesRelationManager;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use ToneGabes\Filament\Icons\Enums\Phosphor;

class StudentResource extends Resource
{
    use UserManagementGrouping;

    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::UserCircle;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Siswa';

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudentPresencesRelationManager::class,
            StudentAssesmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && auth()->user()->hasRole('student')) {
            $query->where('id', auth()->id());
        }

        if (auth()->check() && auth()->user()->hasRole(['guru', 'teacher'])) {
            $query->whereHas('studentClassrooms.classroom', function ($cq) {
                $cq->where('teacher_id', auth()->id());
            });
        }

        // 👇 GEMBOK UDAH DIHANCURIN! ADMIN & TU BEBAS LIAT SEMUA SISWA! 👇
        return $query;
    }

    // Access and navigation handled by Filament Shield
}
