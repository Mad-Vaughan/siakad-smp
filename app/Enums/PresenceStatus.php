<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PresenceStatus: string implements HasColor, HasIcon, HasLabel
{
    case PRESENT = 'present';
    case SICK = 'sick';
    case PERMISSION = 'permission';
    case LATE = 'late';
    case ABSENT = 'absent';

    public function getLabel(): string
    {
        return match ($this) {
            self::PRESENT => 'Hadir',
            self::SICK => 'Sakit',
            self::PERMISSION => 'Izin',
            self::LATE => 'Terlambat',
            self::ABSENT => 'Alpa',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PRESENT => 'heroicon-o-check-circle',
            self::SICK => 'heroicon-o-heart',
            self::PERMISSION => 'heroicon-o-document-text',
            self::LATE => 'heroicon-o-clock',
            self::ABSENT => 'heroicon-o-x-circle',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::PRESENT => 'success',
            self::SICK => 'warning',
            self::PERMISSION => 'primary',
            self::LATE => 'secondary',
            self::ABSENT => 'danger',
        };
    }
}
