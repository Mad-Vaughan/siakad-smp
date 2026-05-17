<?php

namespace App\Filament\Imports;

use App\Models\Classroom;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\DB;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->label('Nama Siswa')->requiredMapping(),
            ImportColumn::make('nipd')->label('NIPD'),
            ImportColumn::make('gender')->label('JK Jenis Kelamin'),
            ImportColumn::make('nisn')->label('NISN')->requiredMapping(),
            ImportColumn::make('birth_place')->label('Tempat Lahir'),
            ImportColumn::make('date_of_birth')
                ->label('Tanggal Lahir')
                ->fillRecordUsing(function ($record, $state) {
                    try {
                        $record->date_of_birth = Carbon::parse($state)->format('Y-m-d');
                    } catch (Exception $e) {
                        $record->date_of_birth = null;
                    }
                }),
            ImportColumn::make('nik')->label('NIK'),
            ImportColumn::make('religion')->label('Agama'),
            ImportColumn::make('address')->label('Alamat'),
            ImportColumn::make('phone')->label('No HP Siswa'),
            ImportColumn::make('email')->label('Email Siswa'),
            ImportColumn::make('kelas_name')
                ->label('Kelas')
                ->fillRecordUsing(fn () => null), // Biarkan kosong, diurus di afterSave
        ];
    }

    public function resolveRecord(): ?Student
    {
        // Cek kalo NISN udah ada, dia bakal Update. Kalo belum, dia Bikin Baru.
        return Student::firstOrNew(['nisn' => $this->data['nisn']]);
    }

    protected function afterSave(): void
    {
        // 1. Otomatis kasih Role 'student'
        if (! $this->record->hasRole('student')) {
            $this->record->assignRole('student');
        }

        // 2. Otomatis masukin ke Kelas yang diketik di Excel
        $kelasName = $this->data['kelas_name'] ?? null;
        if ($kelasName) {
            $classroom = Classroom::where('name', trim($kelasName))
                ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
                ->first();

            if ($classroom) {
                $exists = DB::table('student_classrooms')->where('student_id', $this->record->id)->where('classroom_id', $classroom->id)->exists();
                if (! $exists) {
                    DB::table('student_classrooms')->insert([
                        'student_id' => $this->record->id, 'classroom_id' => $classroom->id,
                        'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import data siswa selesai! '.number_format($import->successful_rows).' baris berhasil diimpor.';
    }
}
