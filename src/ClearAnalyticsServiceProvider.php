<?php

namespace ClearAnalytics\Filament;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ClearAnalyticsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'clear-analytics';

    public function configurePackage(Package $package): void
    {
        /*
         * This package provides the ClearAnalytics.eu Filament v5 integration:
         * a cached API client, a dedicated dashboard page, and analytics widgets.
         */
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews('clear-analytics')
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ClearAnalytics::class);
    }
}
