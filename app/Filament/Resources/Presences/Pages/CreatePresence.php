<?php

namespace App\Filament\Resources\Presences\Pages;

use App\Filament\Resources\Presences\PresenceResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePresence extends CreateRecord
{
    protected static string $resource = PresenceResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Presensi berhasil dibuat')
            ->body('Silahkan isi data presensi.');
    }
}
