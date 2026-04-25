<?php

namespace App\Filament\Resources\Presences\Pages;

use App\Filament\Resources\Presences\PresenceResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewPresence extends ViewRecord
{
    protected static string $resource = PresenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printPdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function (): StreamedResponse {
                    $presence = $this->record->load([
                        'classroom.teacher',
                        'studentPresences.student',
                    ]);

                    $pdf = app('dompdf.wrapper')->loadView('prints.presences.report', [
                        'presence' => $presence,
                    ])->setPaper('a4', 'portrait');

                    $date = Carbon::parse($presence->date)->format('Ymd');
                    $classroomName = optional($presence->classroom)->name ?? 'kelas';
                    $classroom = Str::slug($classroomName);
                    $fileName = "presensi-{$classroom}-{$date}.pdf";

                    return response()->streamDownload(function () use ($pdf): void {
                        echo $pdf->output();
                    }, $fileName);
                }),
            EditAction::make(),
        ];
    }
}
