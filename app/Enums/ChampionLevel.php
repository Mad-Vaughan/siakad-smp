<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ChampionLevel: string implements HasColor, HasIcon, HasLabel
{
    case SCHOOL = 'school';
    case DISTRICT = 'district';
    case PROVINCIAL = 'provincial';
    case NATIONAL = 'national';
    case INTERNATIONAL = 'international';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::SCHOOL => 'Sekolah',
            self::DISTRICT => 'Kecamatan',
            self::PROVINCIAL => 'Provinsi',
            self::NATIONAL => 'Nasional',
            self::INTERNATIONAL => 'Internasional',
            self::OTHER => 'Lainnya',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::SCHOOL => 'heroicon-o-academic-cap',
            self::DISTRICT => 'heroicon-o-map',
            self::PROVINCIAL => 'heroicon-o-globe-alt',
            self::NATIONAL => 'heroicon-o-flag',
            self::INTERNATIONAL => 'heroicon-o-globe',
            self::OTHER => 'heroicon-o-question-mark-circle',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::SCHOOL => 'success',
            self::DISTRICT => 'primary',
            self::PROVINCIAL => 'warning',
            self::NATIONAL => 'danger',
            self::INTERNATIONAL => 'purple',
            self::OTHER => 'gray',
        };
    }
}
