<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Roles: string implements HasColor, HasIcon, HasLabel
{
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case TU = 'tu';
    case STUDENT = 'student';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::TEACHER => 'Teacher',
            self::TU => 'Tata Usaha',
            self::STUDENT => 'Student',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ADMIN => 'heroicon-o-shield-check',
            self::TEACHER => 'heroicon-o-academic-cap',
            self::TU => 'heroicon-o-briefcase',
            self::STUDENT => 'heroicon-o-user-group',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::TEACHER => 'primary',
            self::TU => 'warning',
            self::STUDENT => 'success',
        };
    }
}
