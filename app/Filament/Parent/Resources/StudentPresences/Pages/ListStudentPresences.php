<?php

namespace App\Filament\Parent\Resources\StudentPresences\Pages;

use App\Filament\Parent\Resources\StudentPresences\StudentPresenceResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentPresences extends ListRecords
{
    protected static string $resource = StudentPresenceResource::class;

    // WIDGET KITA HAPUS TOTAL BIAR LIVEWIRE KAGA NGAMBEK!
    protected function getHeaderActions(): array
    {
        return [];
    }
}
