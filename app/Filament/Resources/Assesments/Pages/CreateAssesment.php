<?php

namespace App\Filament\Resources\Assesments\Pages;

use App\Filament\Resources\Assesments\AssesmentResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAssesment extends CreateRecord
{
    protected static string $resource = AssesmentResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Penilaian berhasil dibuat')
            ->body('Silahkan isi data penilaian.');
    }
}
