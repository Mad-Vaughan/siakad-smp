<?php

namespace App\Filament\Imports;

use App\Models\User; // Kita pake model User biar aman masuk ke database
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            // Cukup 5 kolom ini aja yang wajib ada di Excel Jon
            ImportColumn::make('name')->requiredMapping(),
            ImportColumn::make('nisn')->requiredMapping(),
            ImportColumn::make('date_of_birth')->requiredMapping(),
            ImportColumn::make('gender'),
            ImportColumn::make('address'),
        ];
    }

    public function resolveRecord(): ?User
    {
        // Cari siswa berdasarkan NISN-nya
        $user = User::firstOrNew([
            'nisn' => $this->data['nisn'],
        ]);

        $user->name = $this->data['name'];
        $user->date_of_birth = $this->data['date_of_birth'];
        $user->gender = $this->data['gender'] ?? null;
        $user->address = $this->data['address'] ?? null;

        // JURUS SAKTI 1: Bikin email palsu kalau kosong
        if (empty($user->email)) {
            $user->email = $this->data['nisn'] . '@siswa.siakad.com';
        }

        // JURUS SAKTI 2: Bikin password otomatis pake Tanggal Lahir
        if (! $user->exists) {
            $user->password = Hash::make($this->data['date_of_birth']);
        }

        return $user;
    }

    protected function afterSave(): void
    {
        // Otomatis kasih peran 'student' biar ortu bisa login
        $user = $this->record;
        if (! $user->hasRole('student')) {
            $user->assignRole('student');
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data siswa selesai Jon! ' . number_format($import->successful_rows) . ' anak berhasil masuk.';
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Tapi ada ' . number_format($failedRowsCount) . ' baris yang gagal, coba cek lagi format Excelnya.';
        }
        return $body;
    }
}