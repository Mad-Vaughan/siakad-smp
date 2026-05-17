<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\StudentAssesment;
use App\Models\StudentClassroom;
use App\Models\StudentPresence;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 👇 BAGIAN 1: DATA DIRI SISWA (UDAH FULL DAPODIK) 👇
                Section::make('Data Diri Siswa')
                    ->description('Informasi pribadi lengkap dan status kelas aktif siswa.')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(), // Biar nama dapet space luas

                        TextEntry::make('kelas_diri')
                            ->label('Kelas Aktif / Status')
                            ->badge()
                            // 👇 Warnanya nyesuaiin status Alumni 👇
                            ->color(fn ($record) => $record->active_status === 'alumni' ? 'success' : 'info')
                            ->icon(fn ($record) => $record->active_status === 'alumni' ? 'heroicon-m-academic-cap' : 'heroicon-m-home-modern')
                            ->getStateUsing(function ($record) {
                                // 👇 LOGIKA BARU ANTI-GHOIB! 👇
                                // Kalo dia udah Alumni, pamerin status Lulus-nya!
                                if ($record->active_status === 'alumni') {
                                    return 'Alumni (Lulus)';
                                }

                                // Kalo belum lulus, baru cari kelas aktifnya
                                $activeClass = StudentClassroom::where('student_id', $record->id)
                                    ->where('is_active', true)
                                    ->latest('id')
                                    ->first();

                                return $activeClass?->classroom?->name ?? 'Belum Ada Kelas';
                            }),

                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->badge()
                            ->formatStateUsing(fn ($state) => match (strtolower($state->value ?? $state)) {
                                'l', 'male', 'laki-laki' => 'Laki-laki',
                                'p', 'female', 'perempuan' => 'Perempuan',
                                default => $state,
                            })
                            ->color(fn ($state) => match (strtolower($state->value ?? $state)) {
                                'l', 'male', 'laki-laki' => 'info',
                                'p', 'female', 'perempuan' => 'warning',
                                default => 'gray',
                            }),

                        // 👇 JURUS KELOMPOK IDENTITAS 👇
                        TextEntry::make('nisn')
                            ->label('NISN')
                            ->placeholder('-'),

                        TextEntry::make('nipd')
                            ->label('NIPD')
                            ->placeholder('-'),

                        TextEntry::make('nik')
                            ->label('NIK')
                            ->placeholder('-'),

                        TextEntry::make('religion')
                            ->label('Agama')
                            ->placeholder('-'),

                        // 👇 JURUS KELOMPOK KELAHIRAN 👇
                        TextEntry::make('birth_place')
                            ->label('Tempat Lahir')
                            ->placeholder('-'),

                        TextEntry::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->date('d F Y')
                            ->placeholder('-'),

                        // 👇 JURUS KELOMPOK KONTAK 👇
                        TextEntry::make('phone')
                            ->label('No. HP / WhatsApp')
                            ->icon('heroicon-m-phone')
                            ->placeholder('-'),

                        TextEntry::make('email')
                            ->label('Email / Username')
                            ->icon('heroicon-m-envelope')
                            ->placeholder('-'),

                        TextEntry::make('address')
                            ->columnSpanFull()
                            ->label('Alamat Lengkap')
                            ->placeholder('-'),
                    ]),

                // 👇 BAGIAN 2: REKAPITULASI AKADEMIK (Tetep Aman) 👇
                Section::make('Rekapitulasi Akademik')
                    ->description('Ringkasan kehadiran, wali kelas, dan capaian nilai.')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('wali_kelas')
                            ->label('Wali Kelas')
                            ->icon('heroicon-m-user-group')
                            ->getStateUsing(function ($record) {
                                // 👇 LOGIKA WALI KELAS ALUMNI 👇
                                if ($record->active_status === 'alumni') {
                                    return 'Lulus / Tidak Ada';
                                }

                                $activeClass = StudentClassroom::where('student_id', $record->id)
                                    ->where('is_active', true)
                                    ->latest('id')
                                    ->first();

                                return $activeClass?->classroom?->teacher?->name ?? '-';
                            }),

                        TextEntry::make('total_kehadiran')
                            ->label('Total Kehadiran')
                            ->icon('heroicon-m-check-badge')
                            ->getStateUsing(function ($record) {
                                $hadir = StudentPresence::where('student_id', $record->id)
                                    ->whereIn('status', ['present', 'hadir'])
                                    ->count();

                                return $hadir.' Pertemuan';
                            }),

                        TextEntry::make('rata_rata_nilai')
                            ->label('Rata-rata Nilai')
                            ->icon('heroicon-m-star')
                            ->badge()
                            ->getStateUsing(function ($record) {
                                $avg = StudentAssesment::where('student_id', $record->id)->avg('score');

                                return $avg ? number_format($avg, 1) : '0';
                            })
                            ->color(fn ($state) => $state >= 75 ? 'success' : 'warning'),

                        TextEntry::make('info_detail')
                            ->label('Catatan Rekapitulasi')
                            ->columnSpanFull()
                            ->color('gray')
                            ->getStateUsing(fn () => 'Data rekapitulasi di atas diambil dari akumulasi kehadiran harian dan nilai tugas/ujian pada semester aktif.'),
                    ]),
            ]);
    }
}
