<?php

namespace App\Filament\Resources\StudentHistories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

// 👇 INI SURAT IZIN SAKTINYA DARI LARAVEL

class StudentHistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 📋 BAGIAN 1: IDENTITAS SISWA - FULL WIDTH VERTICAL
                Section::make('Identitas Siswa')
                    ->icon('heroicon-m-identification')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->weight('bold'),

                        TextEntry::make('nisn')
                            ->label('NISN')
                            ->placeholder('-'),

                        TextEntry::make('birth_place')
                            ->label('Tempat Lahir')
                            ->placeholder('-'),

                        TextEntry::make('active_status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (?string $state): string => match (strtolower($state ?? '')) {
                                'aktif' => 'success',
                                'alumni' => 'gray',
                                default => 'warning',
                            }),
                    ]),

                // 📅 BAGIAN 2: REKAM JEJAK AKADEMIK - FULL WIDTH VERTICAL
                Section::make('Rekam Jejak Akademik (Per Semester)')
                    ->icon('heroicon-o-clock')
                    ->columnSpanFull()
                    ->collapsed()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // 👈 KOLOM KIRI (1 kolom)
                                Group::make([
                                    // INFORMASI KELAS
                                    Section::make('Informasi Kelas')
                                        ->collapsible(false)
                                        ->schema([
                                            TextEntry::make('classroom_info')
                                                ->label('Wali Kelas')
                                                ->inlineLabel()
                                                ->state(function ($record) {
                                                    $activeClass = \App\Models\StudentClassroom::where('student_id', $record->id)
                                                        ->where('is_active', true)
                                                        ->with('classroom.teacher')
                                                        ->first();

                                                    return $activeClass?->classroom->teacher->name ?? '-';
                                                }),

                                            TextEntry::make('class_status')
                                                ->label('Status')
                                                ->inlineLabel()
                                                ->badge()
                                                ->state(function ($record) {
                                                    $activeClass = \App\Models\StudentClassroom::where('student_id', $record->id)
                                                        ->where('is_active', true)
                                                        ->first();

                                                    return $activeClass ? 'Aktif' : 'Arsip Lama';
                                                })
                                                ->color(function ($state) {
                                                    return $state === 'Aktif' ? 'success' : 'warning';
                                                }),
                                        ]),

                                    // RINGKASAN ABSENSI
                                    Section::make('Ringkasan Absensi')
                                        ->collapsible(false)
                                        ->columns(2)
                                        ->schema([
                                            TextEntry::make('hadir')
                                                ->label('Hadir')
                                                ->state(function ($record) {
                                                    $classroomId = \App\Models\StudentClassroom::where('student_id', $record->id)
                                                        ->where('is_active', true)
                                                        ->value('classroom_id');

                                                    if (! $classroomId) {
                                                        return '0';
                                                    }

                                                    $count = \App\Models\StudentPresence::where('student_id', $record->id)
                                                        ->whereHas('presence', fn ($q) => $q->where('classroom_id', $classroomId))
                                                        ->where('status', 'present')
                                                        ->count();

                                                    return (string) $count;
                                                }),

                                            TextEntry::make('sakit')
                                                ->label('Sakit')
                                                ->state(function ($record) {
                                                    $classroomId = \App\Models\StudentClassroom::where('student_id', $record->id)
                                                        ->where('is_active', true)
                                                        ->value('classroom_id');

                                                    if (! $classroomId) {
                                                        return '0';
                                                    }

                                                    $count = \App\Models\StudentPresence::where('student_id', $record->id)
                                                        ->whereHas('presence', fn ($q) => $q->where('classroom_id', $classroomId))
                                                        ->where('status', 'sick')
                                                        ->count();

                                                    return (string) $count;
                                                }),

                                            TextEntry::make('izin')
                                                ->label('Izin')
                                                ->state(function ($record) {
                                                    $classroomId = \App\Models\StudentClassroom::where('student_id', $record->id)
                                                        ->where('is_active', true)
                                                        ->value('classroom_id');

                                                    if (! $classroomId) {
                                                        return '0';
                                                    }

                                                    $count = \App\Models\StudentPresence::where('student_id', $record->id)
                                                        ->whereHas('presence', fn ($q) => $q->where('classroom_id', $classroomId))
                                                        ->where('status', 'permission')
                                                        ->count();

                                                    return (string) $count;
                                                }),

                                            TextEntry::make('alpa')
                                                ->label('Alpa')
                                                ->state(function ($record) {
                                                    $classroomId = \App\Models\StudentClassroom::where('student_id', $record->id)
                                                        ->where('is_active', true)
                                                        ->value('classroom_id');

                                                    if (! $classroomId) {
                                                        return '0';
                                                    }

                                                    $count = \App\Models\StudentPresence::where('student_id', $record->id)
                                                        ->whereHas('presence', fn ($q) => $q->where('classroom_id', $classroomId))
                                                        ->where('status', 'absent')
                                                        ->count();

                                                    return (string) $count;
                                                }),
                                        ]),
                                ])
                                    ->columnSpan(1),

                                // 👉 KOLOM KANAN (2 kolom) - TABEL TRANSKRIP
                                Group::make([
                                    Html::make('transkrip_nilai_html')
                                        ->content(function ($record) {
                                            $classroomId = \App\Models\StudentClassroom::where('student_id', $record->id)
                                                ->where('is_active', true)
                                                ->value('classroom_id');

                                            $scores = \App\Models\StudentAssesment::where('student_id', $record->id)
                                                ->whereHas('assessment', fn ($q) => $q->where('classroom_id', $classroomId ?? 0))
                                                ->with('assessment.subject')
                                                ->get();

                                            $html = '<div style="overflow-x:auto; border:1px solid #e5e7eb; border-radius:0.75rem;"><table style="width:100%; min-width:100%; border-collapse:collapse; font-size:0.875rem;"><colgroup><col style="width:50%"><col style="width:25%"><col style="width:25%"></colgroup><thead style="background:#f9fafb;"><tr><th style="padding:0.75rem 1rem; text-align:left; color:#374151; border:1px solid #e5e7eb;">Mata Pelajaran</th><th style="padding:0.75rem 1rem; text-align:center; color:#374151; border:1px solid #e5e7eb;">Tipe</th><th style="padding:0.75rem 1rem; text-align:right; color:#374151; border:1px solid #e5e7eb;">Nilai</th></tr></thead><tbody>';

                                            if ($scores->isEmpty()) {
                                                $html .= '<tr><td colspan="3" style="padding:0.75rem 1rem; text-align:center; color:#6b7280; font-style:italic;">Data nilai belum diinput oleh guru.</td></tr>';
                                            } else {
                                                foreach ($scores as $score) {
                                                    $color = $score->score < 75 ? 'color:#dc2626;' : 'color:#16a34a;';
                                                    
                                                    // 👇 KAMUS TRANSLATE BAHASA INDONESIA JON 👇
                                                    $tipeEnum = $score->assessment->type;
                                                    $rawType = strtolower(is_object($tipeEnum) ? ($tipeEnum->value ?? $tipeEnum->name) : $tipeEnum);
                                                    
                                                    $tipeIndo = match($rawType) {
                                                        'assignment' => 'Tugas',
                                                        'exam'       => 'Ujian',
                                                        'quiz'       => 'Kuis',
                                                        'project'    => 'Proyek',
                                                        'midterm'    => 'UTS',
                                                        'final'      => 'UAS',
                                                        'practice'   => 'Praktek',
                                                        default      => ucfirst($rawType),
                                                    };
                                                    // 👆 KAMUS TRANSLATE BAHASA INDONESIA JON 👆

                                                    $html .= '<tr><td style="padding:0.75rem 1rem; border:1px solid #e5e7eb; vertical-align:top; word-break:break-word;">'.e($score->assessment->subject->name).'</td><td style="padding:0.75rem 1rem; text-align:center; border:1px solid #e5e7eb; color:#6b7280; vertical-align:top; word-break:break-word;">'.e($tipeIndo).'</td><td style="padding:0.75rem 1rem; text-align:right; border:1px solid #e5e7eb; font-weight:700; '.$color.' vertical-align:top;">'.e($score->score).'</td></tr>';
                                                }
                                            }

                                            $html .= '</tbody></table></div>';

                                            return $html;
                                        }),
                                ])
                                    ->columnSpan(2),
                            ]),
                    ]),
            ]);
    }
}