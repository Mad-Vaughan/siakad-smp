<?php

namespace App\Filament\Concerns\NavigationGrouping;

use App\Enums\Filament\NavigationGrouping;
use Filament\Panel;
use Filament\Resources\Resource;

/**
 * @mixin Resource
 */
trait ClassAndSubjectManagement
{
    public static string $prefixSlug = 'class-and-subject-management';

    public static function getSlug(?Panel $panel = null): string
    {
        return static::$prefixSlug.'/'.parent::getSlug($panel);
    }

    public static function getNavigationGroup(): ?string
    {
        return NavigationGrouping::ClassAndSubjectManagement->getLabel();
    }
}
