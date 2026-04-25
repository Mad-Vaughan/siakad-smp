<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ChampionType: string implements HasColor, HasIcon, HasLabel
{
    case INDIVIDUAL = 'individual';
    case TEAM = 'team';

    public function getLabel(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Individu',
            self::TEAM => 'Tim',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::INDIVIDUAL => 'heroicon-o-user',
            self::TEAM => 'heroicon-o-users',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::INDIVIDUAL => 'success',
            self::TEAM => 'primary',
        };
    }
}
