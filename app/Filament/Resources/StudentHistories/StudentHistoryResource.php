<?php

namespace App\Filament\Resources\StudentHistories;

use App\Filament\Resources\StudentHistories\Schemas\StudentHistoryInfolist;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class StudentHistoryResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $modelLabel = 'Siswa';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Histori Belajar';

    protected static ?string $pluralLabel = 'Buku Induk Histori Siswa';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen TU';

    protected static ?int $navigationSort = 4;

    // 🔒 GEMBOK: Murni cuma buat ngintip
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return StudentHistoryInfolist::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentHistoryInfolist::configure($schema);
    }

    // 👇 INI DIA KEPALA FUNGSI YANG ILANG JON! 👇
    public static function table(Table $table): Table
    {
        return $table
            ->query(Student::query())
            ->recordUrl(fn (Model $record): string => static::getUrl('view', ['record' => $record->id]))
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->copyable(),

                // 👇 INI YANG BIKIN TU SENENG JON! Munculin Kelas Terakhir 👇
                TextColumn::make('kelas_terakhir')
                    ->label('Kelas Saat Ini')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        // Cari kelas siswa yang statusnya lagi is_active
                        $activeClass = \App\Models\StudentClassroom::where('student_id', $record->id)
                            ->where('is_active', true)
                            ->with('classroom')
                            ->first();

                        return $activeClass ? $activeClass->classroom->name : 'Lulus / Tidak Ada Kelas';
                    }),

                TextColumn::make('active_status')
                    ->label('Status')
                    ->badge()
                    // 👇 WARNANYA UDAH GUE BENERIN SESUAI SEEDER KITA 👇
                    ->color(fn (?string $state): string => match (strtolower($state ?? '')) {
                    'aktif' => 'success',
                    'alumni' => 'gray',
                    default => 'warning',
                }),
            ])
            // 👇 TAMBAHAN FILTER BIAR GAMPANG NYARI 👇
            ->filters([
                SelectFilter::make('active_status')
                    ->label('Saring Status')
                    ->options([
                        'Aktif' => 'Siswa Aktif Saja',
                        'Alumni' => 'Hanya Alumni',
                    ]),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPermissionPrefix(): string
    {
        return 'student-history';
    }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'tu']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'tu']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentHistories::route('/'),
            'view' => Pages\ViewStudentHistory::route('/{record}'),
        ];
    }
}
