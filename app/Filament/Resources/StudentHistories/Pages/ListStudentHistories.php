<?php

namespace App\Filament\Resources\StudentHistories\Pages;

use App\Filament\Resources\StudentHistories\StudentHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentHistories extends ListRecords
{
    protected static string $resource = StudentHistoryResource::class;

    protected function getHeaderActions(): array
    {
        // 👇 KOSONGIN BIAR TOMBOL BUAT STUDENT HILANG! 👇
        return [];
    }
}
