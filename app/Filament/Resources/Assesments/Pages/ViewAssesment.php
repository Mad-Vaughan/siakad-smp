<?php

namespace App\Filament\Resources\Assesments\Pages;

use App\Filament\Resources\Assesments\AssesmentResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewAssesment extends ViewRecord
{
    protected static string $resource = AssesmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printScores')
                ->label('Cetak Nilai')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function (): StreamedResponse {
                    $assesment = $this->record->load([
                        'classroom.teacher',
                        'subject',
                        'studentAssesments.student',
                    ]);

                    $pdf = app('dompdf.wrapper')->loadView('prints.assesments.report', [
                        'assesment' => $assesment,
                    ])->setPaper('a4', 'portrait');

                    $subject = Str::slug($assesment->subject->name ?? 'subject');
                    $classroom = Str::slug($assesment->classroom->name ?? 'kelas');
                    $timestamp = Carbon::now()->format('YmdHis');
                    $fileName = "penilaian-{$subject}-{$classroom}-{$timestamp}.pdf";

                    return response()->streamDownload(function () use ($pdf): void {
                        echo $pdf->output();
                    }, $fileName);
                }),
            EditAction::make(),
        ];
    }
}
