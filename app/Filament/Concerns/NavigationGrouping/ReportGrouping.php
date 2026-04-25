<?php

namespace App\Filament\Concerns\NavigationGrouping;

use App\Enums\Filament\NavigationGrouping;
use Filament\Panel;
use Filament\Resources\Resource;

/**
 * @mixin Resource
 */
trait ReportGrouping
{
    public static string $prefixSlug = 'report-management';

    public static function getSlug(?Panel $panel = null): string
    {
        return static::$prefixSlug.'/'.parent::getSlug($panel);
    }

    public static function getNavigationGroup(): ?string
    {
        return NavigationGrouping::ReportManagement->getLabel();
    }
}
