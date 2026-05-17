<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([

                // KOTAK 1: STATUS & JABATAN (KIRI)
                Section::make('Informasi Tugas & Status')
                    ->columnSpan(1)
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        TextEntry::make('active_status')
                            ->label('Status Keaktifan')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'Aktif' => 'success',
                                'Cuti' => 'warning',
                                'Pensiun' => 'gray',
                                'Meninggal Dunia', 'Meninggal' => 'danger',
                                default => 'gray',
                            })
                            ->placeholder('Aktif'),

                        TextEntry::make('employment_status')
                            ->label('Status Pegawai')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'PNS' => 'success',
                                'PPPK' => 'info',
                                'Honorer' => 'warning',
                                'GTY' => 'primary',
                                default => 'gray',
                            })
                            ->placeholder('Belum Diatur'),

                        TextEntry::make('subjects.name')
                            ->label('Mata Pelajaran Diampu')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->placeholder('Tidak mengampu mapel'),

                        // 👇 TUGAS WALI KELAS UDAH MUSNAH DARI SINI JUGA JON! 👇
                    ]),

                // KOTAK 2: DATA DIRI GURU (KANAN)
                Section::make('Data Diri Guru')
                    ->columnSpan(2)
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('nip')
                            ->label('NIP / NUPTK')
                            ->copyable()
                            ->placeholder('-'),

                        TextEntry::make('email')
                            ->label('Email Akses')
                            ->icon('heroicon-m-envelope'),

                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->placeholder('-'),

                        TextEntry::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->date('d F Y')
                            ->placeholder('-'),

                        TextEntry::make('address')
                            ->label('Alamat Domisili')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
