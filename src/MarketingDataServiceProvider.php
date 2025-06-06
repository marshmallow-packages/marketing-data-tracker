<?php

namespace Marshmallow\MarketingData;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MarketingDataServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('marketing-data-tracker')
            ->hasConfigFile()
            ->hasMigration('create_marketing_data_tracker_table');
    }
}
