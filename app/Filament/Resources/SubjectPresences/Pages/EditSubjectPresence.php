<?php

namespace App\Filament\Resources\SubjectPresences\Pages;

use App\Filament\Resources\SubjectPresences\SubjectPresenceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubjectPresence extends EditRecord
{
    protected static string $resource = SubjectPresenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    // Obrolan halus (Kadang Filament pura-pura budeg)
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // 👇 JURUS TENDANGAN NUKLIR (Kalau masih bandel) 👇
    protected function afterSave(): void
    {
        // Paksa tendang ke halaman daftar (index) secara mutlak!
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
