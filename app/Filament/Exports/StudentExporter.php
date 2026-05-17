<?php

namespace App\Filament\Exports;

use App\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nama Siswa'),
            ExportColumn::make('nipd')->label('NIPD'),
            ExportColumn::make('gender')->label('JK Jenis Kelamin'),
            ExportColumn::make('nisn')->label('NISN'),
            ExportColumn::make('birth_place')->label('Tempat Lahir'),
            ExportColumn::make('date_of_birth')->label('Tanggal Lahir'),
            ExportColumn::make('nik')->label('NIK'),
            ExportColumn::make('religion')->label('Agama'),
            ExportColumn::make('address')->label('Alamat'),
            ExportColumn::make('phone')->label('No HP Siswa'),
            ExportColumn::make('email')->label('Email Siswa'),

            // 👇 INFO KELAS, TAHUN AJARAN & SEMESTER OTOMATIS NGGIKUT 👇
            ExportColumn::make('kelas')
                ->label('Kelas')
                ->state(fn ($record) => $record->studentClassrooms->first()?->classroom?->name ?? '-'),
            ExportColumn::make('tahun_ajaran')
                ->label('Tahun Ajaran')
                ->state(fn ($record) => $record->studentClassrooms->first()?->classroom?->academicYear?->name ?? '-'),
            ExportColumn::make('semester')
                ->label('Semester')
                ->state(fn ($record) => ucfirst($record->studentClassrooms->first()?->classroom?->academicYear?->semester ?? '-')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export data siswa selesai! '.number_format($export->successful_rows).' baris berhasil diekspor.';
    }
}
