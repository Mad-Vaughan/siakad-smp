<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Closure;
// 👈 use Filament\Forms\Get; (UDAH GUE HAPUS BIAR KAGA BENTROK)
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jadwal Pelajaran')
                    ->columns(2)
                    ->schema([
                        Select::make('classroom_id')
                            ->label('Kelas & Tahun Ajaran')
                            ->relationship(
                                'classroom',
                                'name',
                                fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
                            )
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $tahunAjaran = $record->academicYear?->name ?? 'N/A';
                                $semester = ucfirst($record->academicYear?->semester ?? '');

                                return "{$record->name} — {$tahunAjaran} ({$semester})";
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('day')
                            ->label('Hari')
                            ->options([
                                'Senin' => 'Senin',
                                'Selasa' => 'Selasa',
                                'Rabu' => 'Rabu',
                                'Kamis' => 'Kamis',
                                'Jumat' => 'Jumat',
                                'Sabtu' => 'Sabtu',
                            ])
                            ->required()
                            ->native(false),

                        Grid::make(2)
                            ->schema([
                                TimePicker::make('start_time')
                                    ->label('Jam Mulai')
                                    ->seconds(false)
                                    ->required(),

                                TimePicker::make('end_time')
                                    ->label('Jam Selesai')
                                    ->seconds(false)
                                    ->required()
                                    // 👇 LABEL 'Get' UDAH GUE COPOT DI SINI JON! 👇
                                    ->rule(function ($get, ?Model $record) {
                                        return function (string $attribute, $value, Closure $fail) use ($get, $record) {
                                            $classroomId = $get('classroom_id');
                                            $subjectId = $get('subject_id');
                                            $day = $get('day');
                                            $startTime = $get('start_time');
                                            $endTime = $value;

                                            // Kalau datanya belum lengkap diisi, lewatin dulu
                                            if (! $classroomId || ! $subjectId || ! $day || ! $startTime || ! $endTime) {
                                                return;
                                            }

                                            // 1. CEK BENTROK KELAS
                                            $bentrokKelas = \App\Models\Schedule::where('classroom_id', $classroomId)
                                                ->where('day', $day)
                                                ->where(function ($query) use ($startTime, $endTime) {
                                                    $query->where('start_time', '<', $endTime)
                                                        ->where('end_time', '>', $startTime);
                                                });

                                            if ($record) {
                                                $bentrokKelas->where('id', '!=', $record->id);
                                            }

                                            if ($bentrokKelas->exists()) {
                                                $fail('Gagal! Kelas ini sudah memiliki jadwal pelajaran lain di rentang jam tersebut.');
                                            }

                                            // 2. CEK BENTROK GURU
                                            $subject = \App\Models\Subject::find($subjectId);
                                            if ($subject && $subject->teacher_id) {
                                                $bentrokGuru = \App\Models\Schedule::where('day', $day)
                                                    ->whereHas('subject', function ($query) use ($subject) {
                                                        $query->where('teacher_id', $subject->teacher_id);
                                                    })
                                                    ->where(function ($query) use ($startTime, $endTime) {
                                                        $query->where('start_time', '<', $endTime)
                                                            ->where('end_time', '>', $startTime);
                                                    });

                                                if ($record) {
                                                    $bentrokGuru->where('id', '!=', $record->id);
                                                }

                                                if ($bentrokGuru->exists()) {
                                                    $fail('Gagal! Guru pengampu mata pelajaran ini sudah memiliki jadwal mengajar di kelas lain pada jam tersebut.');
                                                }
                                            }
                                        };
                                    }),
                            ]),
                    ]),
            ]);
    }
}
