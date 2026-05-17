<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentClassroom;
// 👇 IMPORT SUCI SESUAI PROJECT LU 👇
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('nipd')
                    ->label('NIPD')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birth_place')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('religion')
                    ->label('Agama')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('No. HP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(function ($state) {
                        $val = strtolower($state instanceof BackedEnum ? $state->value : $state);

                        return match ($val) {
                            'male', 'l' => 'Laki-laki',
                            'female', 'p' => 'Perempuan',
                            default => '-',
                        };
                    })
                    ->searchable(),
                TextColumn::make('kelas')
                    ->label('Kelas/Status')
                    ->badge()
                    ->color(fn ($record) => $record->active_status === 'alumni' ? 'success' : 'info')
                    ->getStateUsing(function ($record) {
                        if ($record->active_status === 'alumni') {
                            return 'Alumni';
                        }

                        $activeYear = AcademicYear::where('is_active', true)->first();
                        if (! $activeYear) {
                            return 'Belum Ada Kelas';
                        }

                        $activeClass = StudentClassroom::where('student_id', $record->id)
                            ->where('is_active', true)
                            ->whereHas('classroom', function ($q) use ($activeYear) {
                                $q->where('academic_year_id', $activeYear->id);
                            })
                            ->first();

                        return $activeClass?->classroom?->name ?? 'Belum Ada Kelas';
                    })
                    ->sortable(false),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->color(function () {
                        $activeYear = AcademicYear::where('is_active', true)->first();

                        return match (strtolower($activeYear?->semester ?? '')) {
                            'ganjil' => 'warning',
                            'genap' => 'success',
                            default => 'gray',
                        };
                    })
                    ->getStateUsing(function ($record) {
                        if ($record->active_status === 'alumni') {
                            return 'Lulus';
                        }

                        $activeYear = AcademicYear::where('is_active', true)->first();
                        if (! $activeYear) {
                            return '-';
                        }

                        return $activeYear->name.' - '.ucfirst($activeYear->semester);
                    })
                    ->sortable(false),
            ])
            ->filters([
                SelectFilter::make('active_status')
                    ->label('Status Siswa')
                    ->options([
                        'aktif' => 'Siswa Aktif',
                        'alumni' => 'Alumni / Lulus',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && $data['value'] === 'alumni') {
                            return $query->where('active_status', 'alumni');
                        } elseif (isset($data['value']) && $data['value'] === 'aktif') {
                            return $query->where(function ($q) {
                                $q->whereNull('active_status')
                                    ->orWhere('active_status', '!=', 'alumni');
                            });
                        }

                        return $query;
                    }),

                SelectFilter::make('kelas')
                    ->label('Filter Berdasarkan Kelas')
                    ->options(function () {
                        $activeYear = AcademicYear::where('is_active', true)->first();
                        if (! $activeYear) {
                            return [];
                        }

                        return Classroom::where('academic_year_id', $activeYear->id)->pluck('name', 'id')->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            $studentIds = StudentClassroom::where('classroom_id', $data['value'])
                                ->where('is_active', true)
                                ->pluck('student_id');

                            return $query->whereIn('id', $studentIds);
                        }

                        return $query;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'tu'])),
            ])
            ->toolbarActions([
                Action::make('import')
                    ->label('Import Siswa')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'tu']))
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
                            if (! $uploadedFile) {
                                return;
                            }

                            if (is_array($uploadedFile)) {
                                $uploadedFile = array_values($uploadedFile)[0];
                            }

                            $path = $uploadedFile->getRealPath();
                            $ext = strtolower($uploadedFile->getClientOriginalExtension());
                            $fileContent = preg_replace('/\x{FEFF}/u', '', file_get_contents($path));
                            $rows = [];

                            if (in_array($ext, ['xlsx', 'xls'], true)) {
                                $spreadsheet = IOFactory::load($path);
                                $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                            } else {
                                $lines = explode("\n", str_replace("\r", '', $fileContent));
                                $delimiter = strpos($lines[0], ';') !== false ? ';' : ',';
                                foreach ($lines as $line) {
                                    if (trim($line) === '') {
                                        continue;
                                    }
                                    $rows[] = str_getcsv($line, $delimiter);
                                }
                            }

                            $header = null;
                            $successCount = 0;
                            $activeYear = AcademicYear::where('is_active', true)->first();

                            foreach ($rows as $row) {
                                $rowValues = array_values($row);
                                if (! $header) {
                                    $header = array_map(fn ($h) => preg_replace('/[^a-z0-9_]/', '', str_replace(' ', '_', strtolower(trim((string) $h)))), $rowValues);

                                    continue;
                                }
                                if (empty(array_filter($rowValues))) {
                                    continue;
                                }

                                $dataRow = array_combine($header, array_pad(array_slice($rowValues, 0, count($header)), count($header), null));

                                $name = $dataRow['nama_siswa'] ?? $dataRow['nama'] ?? null;
                                $nisn = $dataRow['nisn'] ?? null;
                                $dob = $dataRow['tanggal_lahir'] ?? null;
                                $className = $dataRow['kelas'] ?? null;

                                if (! $nisn || ! $name) {
                                    continue;
                                }

                                $student = Student::updateOrCreate(['nisn' => $nisn], [
                                    'name' => $name,
                                    'email' => ($dataRow['email_siswa'] ?? $dataRow['email'] ?? null) ?: $nisn.'@siswa.siakad.com',
                                    'password' => Hash::make($dob ?: $nisn),
                                    'date_of_birth' => $dob,
                                    'nipd' => $dataRow['nipd'] ?? null,
                                    'nik' => $dataRow['nik'] ?? null,
                                    'birth_place' => $dataRow['tempat_lahir'] ?? null,
                                    'religion' => $dataRow['agama'] ?? null,
                                    'phone' => $dataRow['no_hp_siswa'] ?? null,
                                    'address' => $dataRow['alamat'] ?? null,
                                    'gender' => str_contains(strtolower($dataRow['jk_jenis_kelamin'] ?? ''), 'p') ? 'female' : 'male',
                                ]);

                                if ($student && ! $student->hasRole('student')) {
                                    $student->assignRole('student');
                                }

                                if ($className && $student && $activeYear) {
                                    $cleanClass = trim($className);
                                    $class = Classroom::where('academic_year_id', $activeYear->id)
                                        ->where(function ($q) use ($cleanClass) {
                                            $q->where('name', $cleanClass)
                                                ->orWhere('name', 'Kelas '.$cleanClass)
                                                ->orWhere('name', str_replace('Kelas ', '', $cleanClass));
                                        })->first();

                                    if ($class) {
                                        StudentClassroom::updateOrCreate(
                                            ['student_id' => $student->id, 'classroom_id' => $class->id],
                                            ['is_active' => true]
                                        );
                                    }
                                }
                                $successCount++;
                            }
                            Notification::make()->title("Sukses ! {$successCount} siswa berhasil masuk.")->success()->send();
                        } catch (Throwable $e) {
                            Notification::make()->title('Sistem Error !')->body($e->getMessage())->danger()->send();
                        }
                    }),

                Action::make('downloadTemplate')
                    ->label('Download Template Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'tu']))
                    ->action(function (): StreamedResponse {
                        return response()->streamDownload(function () {
                            $spreadsheet = new Spreadsheet;
                            $sheet = $spreadsheet->getActiveSheet();
                            $headers = ['Nama Siswa', 'NIPD', 'JK Jenis Kelamin', 'NISN', 'Tempat Lahir', 'Tanggal Lahir', 'NIK', 'Agama', 'Alamat', 'No HP Siswa', 'Email Siswa', 'Kelas'];
                            $sheet->fromArray($headers, null, 'A1');
                            $sheet->getStyle('A1:L1')->getFont()->setBold(true);

                            $sheet->getStyle('B')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                            $sheet->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                            $sheet->getStyle('G')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                            $sheet->getStyle('J')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                            $sheet->setCellValue('A2', 'Budi Santoso');
                            $sheet->setCellValueExplicit('B2', '4345', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValue('C2', 'L');
                            $sheet->setCellValueExplicit('D2', '0115832988', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValue('E2', 'JAKARTA');
                            $sheet->setCellValue('F2', '2011-04-16');
                            $sheet->setCellValueExplicit('G2', '3175075604111004', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValue('H2', 'Islam');
                            $sheet->setCellValue('I2', 'Jl. Kenangan No 99');
                            $sheet->setCellValueExplicit('J2', '081234567890', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            $sheet->setCellValue('K2', 'budi@siswa.siakad.com');
                            $sheet->setCellValueExplicit('L2', '7A', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                            foreach (range('A', 'L') as $col) {
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }

                            $writer = new Xlsx($spreadsheet);
                            $writer->save('php://output');
                        }, 'Template_Data_Siswa.xlsx', [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]);
                    }),

                Action::make('export')
                    ->label('Export Data Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'tu']))
                    ->action(function (\Filament\Tables\Contracts\HasTable $livewire): StreamedResponse {
                        $students = $livewire->getFilteredTableQuery()->get();

                        return response()->streamDownload(function () use ($students) {
                            $spreadsheet = new Spreadsheet;
                            $sheet = $spreadsheet->getActiveSheet();
                            $headers = ['Nama Siswa', 'NIPD', 'JK Jenis Kelamin', 'NISN', 'Tempat Lahir', 'Tanggal Lahir', 'NIK', 'Agama', 'Alamat', 'No HP Siswa', 'Email Siswa', 'Kelas', 'Tahun Ajaran', 'Semester'];
                            $sheet->fromArray($headers, null, 'A1');
                            $sheet->getStyle('A1:N1')->getFont()->setBold(true);

                            $sheet->getStyle('B')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                            $sheet->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                            $sheet->getStyle('G')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                            $sheet->getStyle('J')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                            $rowNum = 2;
                            foreach ($students as $student) {
                                $sheet->setCellValue('A'.$rowNum, $student->name);
                                $sheet->setCellValueExplicit('B'.$rowNum, $student->nipd, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                                $genderVal = '';
                                if ($student->gender) {
                                    $val = strtolower($student->gender->value ?? $student->gender);
                                    if (in_array($val, ['male', 'l'], true)) {
                                        $genderVal = 'L';
                                    } elseif (in_array($val, ['female', 'p'], true)) {
                                        $genderVal = 'P';
                                    } else {
                                        $genderVal = $val;
                                    }
                                }
                                $sheet->setCellValue('C'.$rowNum, $genderVal);
                                $sheet->setCellValueExplicit('D'.$rowNum, $student->nisn, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                                $sheet->setCellValue('E'.$rowNum, $student->birth_place ?? '');
                                $sheet->setCellValue('F'.$rowNum, $student->date_of_birth ?? '');
                                $sheet->setCellValueExplicit('G'.$rowNum, $student->nik, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                                $sheet->setCellValue('H'.$rowNum, $student->religion ?? '');
                                $sheet->setCellValue('I'.$rowNum, $student->address ?? '');
                                $sheet->setCellValueExplicit('J'.$rowNum, $student->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                                $sheet->setCellValue('K'.$rowNum, $student->email ?? '');

                                $activeClass = StudentClassroom::with(['classroom.academicYear'])->where('student_id', $student->id)->where('is_active', true)->first();

                                $sheet->setCellValueExplicit('L'.$rowNum, $activeClass?->classroom?->name ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                                $sheet->setCellValue('M'.$rowNum, $activeClass?->classroom?->academicYear?->name ?? '-');
                                $sheet->setCellValue('N'.$rowNum, ucfirst($activeClass?->classroom?->academicYear?->semester ?? '-'));

                                $rowNum++;
                            }

                            foreach (range('A', 'N') as $col) {
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }

                            $writer = new Xlsx($spreadsheet);
                            $writer->save('php://output');
                        }, 'Export_Siswa_'.now()->format('Ymd_His').'.xlsx');
                    }),

                // 👇 INI DIA GUDANGNYA TOMBOL CENTANG JON! SEMUA KUMPUL DI SINI! 👇
                BulkActionGroup::make([
                    BulkAction::make('assignToClass')
                        ->label('Masukkan / Naik Kelas')
                        ->icon('heroicon-o-home-modern')
                        ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'tu']))
                        ->form([
                            Select::make('classroom_id')
                                ->label('Pilih Kelas Tujuan')
                                ->options(function () {
                                    $activeYear = AcademicYear::where('is_active', true)->first();
                                    if (! $activeYear) {
                                        return [];
                                    }

                                    return Classroom::where('academic_year_id', $activeYear->id)->pluck('name', 'id')->toArray();
                                })
                                ->required(),
                        ])
                        ->action(function ($records, $data) {
                            foreach ($records as $record) {
                                StudentClassroom::where('student_id', $record->id)->update(['is_active' => false]);
                                StudentClassroom::updateOrCreate(
                                    ['student_id' => $record->id, 'classroom_id' => $data['classroom_id']],
                                    ['is_active' => true]
                                );
                                $record->update(['active_status' => 'aktif']);
                            }
                            Notification::make()->title('Siswa berhasil dipindahkan/naik kelas!')->success()->send();
                        }),

                    BulkAction::make('luluskanSiswa')
                        ->label('Luluskan Siswa (Alumni)')
                        ->icon('heroicon-o-academic-cap')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Luluskan Siswa Terpilih?')
                        ->modalDescription('Siswa yang diluluskan akan dinonaktifkan dari kelas saat ini dan statusnya menjadi Alumni.')
                        ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'tu']))
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                StudentClassroom::where('student_id', $record->id)->update(['is_active' => false]);
                                $record->update(['active_status' => 'alumni']);
                            }
                            Notification::make()->title('Siswa berhasil diluluskan menjadi Alumni!')->success()->send();
                        }),

                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'tu'])),
                ]),
            ]);
    }
}
