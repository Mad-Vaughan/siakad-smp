<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\Classroom;
use App\Models\StudentClassroom;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use App\Models\Student;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable(),
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
                TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('assignToClass')
                    ->label('Masukkan ke Kelas')
                    ->form([
                        Select::make('classroom_id')
                            ->label('Pilih Kelas')
                            ->options(Classroom::query()->pluck('name', 'id'))
                            ->required(),
                        Select::make('is_active')
                            ->label('Jadikan Aktif di Kelas')
                            ->options([1 => 'Ya', 0 => 'Tidak'])
                            ->default(1)
                            ->required(),
                    ])
                    ->action(function ($records, $data) {
                        foreach ($records as $record) {
                            StudentClassroom::create([
                                'student_id' => $record->id,
                                'classroom_id' => $data['classroom_id'],
                                'is_active' => (bool) $data['is_active'],
                            ]);
                        }
                        Notification::make()->title('Berhasil masuk kelas!')->success()->send();
                    }),
            ])
            ->toolbarActions([
                Action::make('import')
                    ->label('Import Siswa')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->form([
                        FileUpload::make('file')
                            ->label('File Excel (.xlsx) / CSV')
                            ->storeFiles(false) 
                            ->acceptedFileTypes(['text/plain', 'text/csv', '.csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        try {
                            $uploadedFile = $data['file'] ?? null;
                            if (!$uploadedFile) {
                                Notification::make()->title('Error: File kaga masuk!')->danger()->send();
                                return;
                            }

                            if (is_array($uploadedFile)) {
                                $uploadedFile = array_values($uploadedFile)[0];
                            }

                            if (!is_object($uploadedFile) || !method_exists($uploadedFile, 'getRealPath')) {
                                Notification::make()->title('Error: Sistem nolak format filenya!')->danger()->send();
                                return;
                            }

                            $path = $uploadedFile->getRealPath();
                            $ext = strtolower($uploadedFile->getClientOriginalExtension());
                            
                            $fileContent = file_get_contents($path);
                            if (empty($fileContent)) {
                                Notification::make()->title('Error: File yang lo upload isinya kosong Jon!')->danger()->send();
                                return;
                            }

                            $fileContent = preg_replace('/\x{FEFF}/u', '', $fileContent);

                            $rows = [];
                            
                            if (in_array($ext, ['xlsx', 'xls']) || str_contains(substr($fileContent, 0, 10), 'PK')) {
                                $spreadsheet = IOFactory::load($path);
                                $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                            } else {
                                $lines = explode("\n", str_replace("\r", "", $fileContent));
                                $delimiter = strpos($lines[0], ';') !== false ? ';' : ',';
                                foreach ($lines as $line) {
                                    if (trim($line) === '') continue;
                                    $rows[] = str_getcsv($line, $delimiter);
                                }
                            }

                            if (count($rows) < 2) {
                                Notification::make()->title('Error: Datanya kurang!')->body('Minimal harus ada 1 baris judul dan 1 baris data siswa.')->danger()->send();
                                return;
                            }

                            $header = null;
                            $successCount = 0;
                            $failedCount = 0;
                            $headerSighted = [];

                            foreach ($rows as $row) {
                                $rowValues = array_values($row);
                                
                                if (! $header) {
                                    $header = array_map(function($h) {
                                        $bersih = strtolower(trim((string) $h));
                                        return preg_replace('/[^a-z0-9_]/', '', str_replace(' ', '_', $bersih));
                                    }, $rowValues);
                                    $headerSighted = $header;
                                    continue;
                                }

                                if (count(array_filter($rowValues, fn($v) => $v !== null && trim((string) $v) !== '')) === 0) continue;

                                $headerCount = count($header);
                                $rowValues = array_pad(array_slice($rowValues, 0, $headerCount), $headerCount, null);
                                $dataRow = array_combine($header, $rowValues);

                                if (! $dataRow || ! is_array($dataRow)) continue;

                                $nisn = $dataRow['nisn'] ?? $dataRow['n_i_s_n'] ?? $dataRow['nis'] ?? null;
                                $name = $dataRow['name'] ?? $dataRow['nama'] ?? $dataRow['namasiswa'] ?? null;
                                $email = $dataRow['email'] ?? $dataRow['surel'] ?? null;
                                $dob = $dataRow['date_of_birth'] ?? $dataRow['tanggal_lahir'] ?? $dataRow['tanggallahir'] ?? null;
                                $address = $dataRow['address'] ?? $dataRow['alamat'] ?? null;
                                $className = $dataRow['classroom'] ?? $dataRow['kelas'] ?? null;
                                
                                $genderRaw = strtolower(trim($dataRow['gender'] ?? $dataRow['jenis_kelamin'] ?? $dataRow['jeniskelamin'] ?? ''));
                                $gender = null;
                                if (in_array($genderRaw, ['laki-laki', 'l', 'male', 'pria'])) $gender = 'male'; 
                                elseif (in_array($genderRaw, ['perempuan', 'p', 'female', 'wanita'])) $gender = 'female'; 

                                if (!$nisn || !$name) {
                                    $failedCount++;
                                    continue; 
                                }

                                if (empty($email)) {
                                    $email = $nisn . '@siswa.siakad.com';
                                }

                                try {
                                    $student = Student::updateOrCreate(
                                        ['nisn' => $nisn],
                                        [
                                            'name' => $name,
                                            'email' => $email,
                                            'password' => Hash::make($dob ?: $nisn),
                                            'date_of_birth' => $dob,
                                            'gender' => $gender,
                                            'address' => $address,
                                        ]
                                    );

                                    if ($student && !$student->hasRole('student')) {
                                        $student->assignRole('student');
                                    }

                                    if ($className && $student) {
                                        $class = Classroom::query()->where('name', $className)->first();
                                        if ($class) {
                                            StudentClassroom::updateOrCreate([
                                                'student_id' => $student->id,
                                                'classroom_id' => $class->id,
                                            ], ['is_active' => true]);
                                        }
                                    }

                                    $successCount++;
                                } catch (\Throwable $e) {
                                    Log::error('Gagal simpan siswa: ' . $e->getMessage());
                                    $failedCount++;
                                }
                            }

                            if ($successCount > 0) {
                                Notification::make()
                                    ->title("MANTAP JON! $successCount siswa berhasil masuk.")
                                    ->success()
                                    ->duration(8000)
                                    ->send();
                            } else {
                                $headerTeks = implode(', ', $headerSighted);
                                Notification::make()
                                    ->title("Gagal total Jon! Kaga ada data yang valid.")
                                    ->body("Sistem baca judul kolom: [ $headerTeks ]. Pastiin baris di bawahnya diisi nama dan nisn, jangan dibiarin kosong.")
                                    ->danger()
                                    ->duration(10000)
                                    ->send();
                            }

                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Sistem Error Meledak!')
                                ->body($e->getMessage())
                                ->danger()
                                ->duration(10000)
                                ->send();
                        }
                    }),

                Action::make('downloadTemplate')
                    ->label('Download Template Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('warning')
                    ->action(function (): StreamedResponse {
                        return response()->streamDownload(function () {
                            $spreadsheet = new Spreadsheet();
                            $sheet = $spreadsheet->getActiveSheet();

                            $headers = ['nama', 'nisn', 'email', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'kelas'];
                            $sheet->fromArray($headers, null, 'A1');
                            $sheet->getStyle('A1:G1')->getFont()->setBold(true);
                            $sheet->getStyle('B')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                            $sheet->setCellValue('A2', 'Budi Santoso');
                            $sheet->setCellValueExplicit('B2', '0011223344', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValue('C2', '');
                            $sheet->setCellValue('D2', '2010-08-17');
                            $sheet->setCellValue('E2', 'Laki-laki');
                            $sheet->setCellValue('F2', 'Jl. Kenangan No 99');
                            $sheet->setCellValue('G2', '7A');

                            foreach (range('A', 'G') as $col) {
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }

                            $writer = new Xlsx($spreadsheet);
                            $writer->save('php://output');
                        }, 'Template_Data_Siswa.xlsx', [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]);
                    }),

                // 👇 INI DIA EXPORT EXCEL YANG BARU JON 👇
                Action::make('export')
                    ->label('Export Data Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->action(function (): StreamedResponse {
                        $students = Student::query()->with('classroom')->get();
                        
                        return response()->streamDownload(function () use ($students) {
                            $spreadsheet = new Spreadsheet();
                            $sheet = $spreadsheet->getActiveSheet();

                            // Bikin Header
                            $headers = ['nama', 'nisn', 'email', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'kelas'];
                            $sheet->fromArray($headers, null, 'A1');
                            
                            // Bikin Header Tebel & Rapi
                            $sheet->getStyle('A1:G1')->getFont()->setBold(true);

                            // Masukin Data Siswa satu-satu
                            $rowNum = 2;
                            foreach ($students as $student) {
                                $sheet->setCellValue('A' . $rowNum, $student->name);
                                // Set NISN sebagai Text biar nol di depan kaga musnah
                                $sheet->setCellValueExplicit('B' . $rowNum, $student->nisn, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                                $sheet->setCellValue('C' . $rowNum, $student->email ?? '');
                                $sheet->setCellValue('D' . $rowNum, $student->date_of_birth ?? '');
                                
                                // Rapihin output Gender biar jadi Bahasa Indonesia
                                $genderVal = '';
                                if ($student->gender) {
                                    $val = strtolower($student->gender->value);
                                    if (in_array($val, ['male', 'l'])) $genderVal = 'Laki-laki';
                                    elseif (in_array($val, ['female', 'p'])) $genderVal = 'Perempuan';
                                    else $genderVal = $student->gender->value;
                                }
                                $sheet->setCellValue('E' . $rowNum, $genderVal);
                                
                                $sheet->setCellValue('F' . $rowNum, $student->address ?? '');
                                $sheet->setCellValue('G' . $rowNum, optional($student->classroom)->name ?? '');
                                
                                $rowNum++;
                            }

                            // Lebarin Kolom Otomatis
                            foreach (range('A', 'G') as $col) {
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }

                            // Generate ke format .xlsx
                            $writer = new Xlsx($spreadsheet);
                            $writer->save('php://output');
                            
                        }, 'Export_Siswa_' . now()->format('Ymd_His') . '.xlsx', [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]);
                    }),

                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}