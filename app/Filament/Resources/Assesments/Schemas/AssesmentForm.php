<?php

namespace App\Filament\Resources\Assesments\Schemas;

use App\Enums\AssesmentType;
use App\Models\Schedule;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

// 👈 PENTING! Import Schedule

class AssesmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penilaian')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        // 1. Nama Tugas
                        TextInput::make('name')
                            ->label('Nama Penilaian/Tugas')
                            ->placeholder('Contoh: Tugas Harian 1 / UTS')
                            ->required()
                            ->columnSpanFull(),

                        // 2. Tanggal
                        DatePicker::make('date')
                            ->label('Tanggal Penilaian')
                            ->default(now())
                            ->required(),

                        // 3. Tipe
                        Select::make('type')
                            ->label('Tipe Penilaian')
                            ->options(AssesmentType::class)
                            ->required(),

                        // 4. Pilih Kelas (LOGIC BARU: BACA DARI JADWAL)
                        Select::make('classroom_id')
                            ->label('Kelas')
                            ->relationship('classroom', 'name', function (Builder $query) {
                                // Filter 1: Kelas di semester aktif
                                $query->whereHas('academicYear', fn ($q) => $q->where('is_active', true));

                                $user = auth()->user();
                                // Filter 2: Kalau Guru, cuma munculin kelas yang ADA JADWAL NGAJARNYA
                                if ($user && $user->hasRole('teacher') && ! $user->hasRole('admin')) {
                                    $jadwalKelasIds = Schedule::whereHas('subject', function ($q) use ($user) {
                                        $q->where('teacher_id', $user->id);
                                    })->pluck('classroom_id')->unique()->toArray();

                                    $query->whereIn('id', $jadwalKelasIds);
                                }
                            })
                            ->preload()
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $set('subject_id', null);

                                if ($state) {
                                    $user = auth()->user();

                                    // Cari mapel apa aja yang diajar di kelas ini (lewat jadwal)
                                    $query = \App\Models\Subject::whereHas('schedules', function ($q) use ($state) {
                                        $q->where('classroom_id', $state);
                                    });

                                    // Kalau guru, filter khusus mapel dia doang
                                    if ($user && $user->hasRole('teacher') && ! $user->hasRole('admin')) {
                                        $query->where('teacher_id', $user->id);
                                    }

                                    $subjects = $query->get();

                                    // AUTO-FILL kalau cuma 1 mapel (Biasanya Guru mapel ya ngajar 1 mapel aja per kelas)
                                    if ($subjects->count() === 1) {
                                        $set('subject_id', $subjects->first()->id);
                                    }
                                }
                            }),

                        // 5. Pilih Mata Pelajaran (LOGIC BARU: BACA DARI JADWAL)
                        Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->options(function ($get) {
                                $classroomId = $get('classroom_id');
                                $user = auth()->user();

                                if (! $classroomId) {
                                    return [];
                                }

                                // Cari mapel yang ada jadwalnya di kelas yang dipilih
                                $query = \App\Models\Subject::whereHas('schedules', function ($q) use ($classroomId) {
                                    $q->where('classroom_id', $classroomId);
                                });

                                // Kalau guru, filter mapel dia doang
                                if ($user && $user->hasRole('teacher') && ! $user->hasRole('admin')) {
                                    $query->where('teacher_id', $user->id);
                                }

                                return $query->pluck('name', 'id');
                            })
                            ->placeholder(fn ($get) => $get('classroom_id') ? 'Pilih Mata Pelajaran' : 'Pilih Kelas Terlebih Dahulu')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}
