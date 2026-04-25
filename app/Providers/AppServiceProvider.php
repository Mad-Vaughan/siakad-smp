<?php

namespace App\Providers;

use App\Settings\LetterheadSetting;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LetterheadSetting::class, function (): LetterheadSetting {
            try {
                return new LetterheadSetting;
            } catch (Throwable $exception) {
                return LetterheadSetting::fake(LetterheadSetting::defaults(), loadMissingValues: false);
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
