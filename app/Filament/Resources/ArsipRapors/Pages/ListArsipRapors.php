<?php

namespace App\Filament\Resources\ArsipRapors\Pages;

use App\Filament\Resources\ArsipRapors\ArsipRaporResource;
use App\Models\StudentAssesment;
use App\Models\StudentClassroom;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListArsipRapors extends ListRecords
{
    protected static string $resource = ArsipRaporResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_rapor_header')
                ->label('Cetak Rapor (PDF)')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalWidth('md')
                ->form([
                    Select::make('student_id')
                        ->label('1. Pilih Nama Siswa')
                        ->options(\App\Models\Student::pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->required(),

                    Select::make('student_classroom_id')
                        ->label('2. Pilih Tahun & Kelas')
                        ->options(function ($get) {
                            $studentId = $get('student_id');
                            if (! $studentId) {
                                return [];
                            }

                            return StudentClassroom::where('student_id', $studentId)
                                ->with(['classroom.academicYear'])
                                ->get()
                                ->mapWithKeys(fn ($r) => [$r->id => 'Kelas '.$r->classroom->name.' | '.$r->classroom->academicYear->name.' ('.ucfirst($r->classroom->academicYear->semester).')']);
                        })
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    $record = StudentClassroom::with(['student', 'classroom'])->find($data['student_classroom_id']);
                    $pdf = Pdf::loadView('pdf.rapor', [
                        'student' => $record->student,
                        'classroom' => $record->classroom,
                        'assessments' => StudentAssesment::where('student_id', $record->student_id)
                            ->whereHas('assessment', fn ($q) => $q->where('classroom_id', $record->classroom_id))
                            ->get(),
                    ]);

                    return response()->streamDownload(fn () => print ($pdf->output()), "Rapor_{$record->student->name}.pdf");
                }),
        ];
    }
}
