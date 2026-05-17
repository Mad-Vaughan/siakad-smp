<?php

namespace App\Filament\Resources\ArsipRapors;

use App\Models\AcademicYear;
use App\Models\StudentClassroom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
// 👈 Pake ini Jon
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ArsipRaporResource extends Resource
{
    protected static ?string $model = StudentClassroom::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Arsip & Cetak Rapor';

    protected static ?string $pluralLabel = 'Arsip Rapor Siswa';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen TU';

    protected static ?int $navigationSort = 3;

    public static function getPermissionPrefix(): string
    {
        return 'arsip-rapor';
    }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'tu']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'tu']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['student', 'classroom.academicYear']))
            ->columns([
                TextColumn::make('classroom.academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('classroom.academicYear.semester')
                    ->label('Semester')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => strtolower($state) === 'ganjil' ? 'warning' : 'success'),

                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('academic_year')
                    ->label('Filter Tahun Ajaran')
                    ->options(fn () => AcademicYear::all()->pluck('name', 'id')),
            ])
            ->actions([

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArsipRapors::route('/'),
        ];
    }
}
