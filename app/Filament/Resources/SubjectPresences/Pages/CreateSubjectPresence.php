<?php

namespace App\Filament\Resources\SubjectPresences\Pages;

use App\Filament\Resources\SubjectPresences\SubjectPresenceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubjectPresence extends CreateRecord
{
    protected static string $resource = SubjectPresenceResource::class;

    // 👇 INI SUNTIKAN MAGIC-NYA JON! 👇
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Paksa isi kolom 'type' jadi 'mapel' secara gaib sebelum masuk database
        $data['type'] = 'mapel';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Paksa sistem buat ngebuka halaman EDIT dari Resource MAPEL, bukan Harian
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
