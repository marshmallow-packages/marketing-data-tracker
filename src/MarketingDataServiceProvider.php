<?php

namespace Marshmallow\MarketingData;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Marshmallow\MarketingData\Services\PlatformManager;
use Marshmallow\MarketingData\Services\CookieManager;

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
            ->hasMigration('create_marketing_data_tracker_table')
            ->hasViews()
            ->hasRoute('web');
    }

    public function bootingPackage(): void
    {
        Blade::component('marketing-data-tracker::components.marketing-cookies', 'marshmallow-marketing-cookies');

        $this->registerCookieExceptions();
    }

    /**
     * Automatically register marketing cookies as exceptions to EncryptCookies middleware
     */
    protected function registerCookieExceptions(): void
    {
        // Check if we should automatically register cookie exceptions
        if (!config('marketing-data-tracker.cookie_management.auto_register_exceptions', true)) {
            return;
        }

        // Only register if EncryptCookies middleware is bound in the container
        if (!$this->app->bound(EncryptCookies::class)) {
            return;
        }

        // Get all marketing cookies from platform configurations
        $marketingCookies = $this->getMarketingCookies();

        if (empty($marketingCookies)) {
            return;
        }

        // Automatically add them to EncryptCookies exceptions when middleware is resolved
        $this->app->resolving(EncryptCookies::class, function ($middleware) use ($marketingCookies) {
            $middleware->disableFor($marketingCookies);
        });
    }

    /**
     * Get all marketing cookies from platform configurations
     */
    protected function getMarketingCookies(): array
    {
        try {
            // Get cookies from platform configurations
            $platformManager = new PlatformManager();
            $platformCookies = $platformManager->getAllTrackingCookies();

            // Get legacy cookies from old configuration
            $legacyCookies = config('marketing-data-tracker.store_marketing_cookies', []);

            // Get cookies from cookie manager if available
            $cookieManagerCookies = [];
            if (class_exists(CookieManager::class)) {
                $cookieManager = new CookieManager($platformManager);
                $cookieManagerCookies = $cookieManager->getTrackableCookies();
            }

            // Merge all cookie sources
            $allCookies = array_merge($platformCookies, $legacyCookies, $cookieManagerCookies);

            return array_unique($allCookies);
        } catch (\Exception $e) {
            // Fail silently if there are any configuration issues
            return [];
        }
    }
}
