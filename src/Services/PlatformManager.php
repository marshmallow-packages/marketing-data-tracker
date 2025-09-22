<?php

namespace Marshmallow\MarketingData\Services;

use Illuminate\Support\Collection;

class PlatformManager
{
    protected array $config;
    protected array $wildcardPatterns;

    public function __construct()
    {
        $this->config = config('marketing-data-tracker.platforms', []);
        $this->wildcardPatterns = config('marketing-data-tracker.wildcard_patterns', []);
    }

    /**
     * Get all enabled platforms
     */
    public function getEnabledPlatforms(): array
    {
        return collect($this->config)
            ->filter(fn($platform) => $platform['enabled'] ?? false)
            ->toArray();
    }

    /**
     * Check if a specific platform is enabled
     */
    public function isPlatformEnabled(string $platform): bool
    {
        return $this->config[$platform]['enabled'] ?? false;
    }

    /**
     * Get parameters for a specific platform
     */
    public function getPlatformParameters(string $platform): array
    {
        if (!$this->isPlatformEnabled($platform)) {
            return [];
        }

        return $this->config[$platform]['parameters'] ?? [];
    }

    /**
     * Get cookies for a specific platform
     */
    public function getPlatformCookies(string $platform): array
    {
        if (!$this->isPlatformEnabled($platform)) {
            return [];
        }

        return $this->config[$platform]['cookies'] ?? [];
    }

    /**
     * Get click ID parameters for a specific platform
     */
    public function getPlatformClickIdParams(string $platform): array
    {
        if (!$this->isPlatformEnabled($platform)) {
            return [];
        }

        return $this->config[$platform]['click_id_params'] ?? [];
    }

    /**
     * Get click ID cookies for a specific platform
     */
    public function getPlatformClickIdCookies(string $platform): array
    {
        if (!$this->isPlatformEnabled($platform)) {
            return [];
        }

        return $this->config[$platform]['click_id_cookies'] ?? [];
    }

    /**
     * Get all tracking parameters from all enabled platforms
     */
    public function getAllTrackingParameters(): array
    {
        $allParameters = [];

        foreach ($this->getEnabledPlatforms() as $platformKey => $platform) {
            $parameters = $platform['parameters'] ?? [];
            $allParameters = array_merge($allParameters, $parameters);
        }

        // Add wildcard patterns if enabled
        if ($this->wildcardPatterns['enabled'] ?? false) {
            $patterns = $this->wildcardPatterns['parameter_patterns'] ?? [];
            $allParameters = array_merge($allParameters, $patterns);
        }

        return array_unique($allParameters);
    }

    /**
     * Get all tracking cookies from all enabled platforms
     */
    public function getAllTrackingCookies(): array
    {
        $allCookies = [];

        foreach ($this->getEnabledPlatforms() as $platformKey => $platform) {
            $cookies = $platform['cookies'] ?? [];
            $allCookies = array_merge($allCookies, $cookies);
        }

        // Add wildcard patterns if enabled
        if ($this->wildcardPatterns['enabled'] ?? false) {
            $patterns = $this->wildcardPatterns['cookie_patterns'] ?? [];
            $allCookies = array_merge($allCookies, $patterns);
        }

        return array_unique($allCookies);
    }

    /**
     * Get all click ID parameters from all enabled platforms
     */
    public function getAllClickIdParameters(): array
    {
        $allClickIds = [];

        foreach ($this->getEnabledPlatforms() as $platformKey => $platform) {
            $clickIds = array_merge(
                $platform['click_id_params'] ?? [],
                $platform['click_id_cookies'] ?? []
            );
            $allClickIds = array_merge($allClickIds, $clickIds);
        }

        return array_unique($allClickIds);
    }

    /**
     * Get platform name by key
     */
    public function getPlatformName(string $platform): ?string
    {
        return $this->config[$platform]['name'] ?? null;
    }

    /**
     * Get all platform names
     */
    public function getAllPlatformNames(): array
    {
        return collect($this->config)
            ->filter(fn($platform) => $platform['enabled'] ?? false)
            ->mapWithKeys(fn($platform, $key) => [$key => $platform['name'] ?? $key])
            ->toArray();
    }

    /**
     * Match wildcard patterns against given array
     */
    public function matchWildcardPatterns(array $items, array $patterns): array
    {
        if (!($this->wildcardPatterns['enabled'] ?? false)) {
            return [];
        }

        $matches = [];

        foreach ($patterns as $pattern) {
            if (!str_contains($pattern, '*')) {
                // Exact match
                if (in_array($pattern, $items)) {
                    $matches[] = $pattern;
                }
                continue;
            }

            // Wildcard pattern matching
            $regex = str_replace('*', '.*', preg_quote($pattern, '/'));
            foreach ($items as $item) {
                if (preg_match("/^{$regex}$/", $item)) {
                    $matches[] = $item;
                }
            }
        }

        return array_unique($matches);
    }

    /**
     * Filter parameters by wildcard patterns
     */
    public function filterParametersByPatterns(array $parameters): array
    {
        $patterns = $this->wildcardPatterns['parameter_patterns'] ?? [];
        return $this->matchWildcardPatterns($parameters, $patterns);
    }

    /**
     * Filter cookies by wildcard patterns
     */
    public function filterCookiesByPatterns(array $cookies): array
    {
        $patterns = $this->wildcardPatterns['cookie_patterns'] ?? [];
        return $this->matchWildcardPatterns($cookies, $patterns);
    }

    /**
     * Get platform configuration
     */
    public function getPlatformConfig(string $platform): array
    {
        return $this->config[$platform] ?? [];
    }

    /**
     * Get all platform configurations
     */
    public function getAllPlatformConfigs(): array
    {
        return $this->config;
    }

    /**
     * Check if wildcard patterns are enabled
     */
    public function isWildcardEnabled(): bool
    {
        return $this->wildcardPatterns['enabled'] ?? false;
    }

    /**
     * Get click ID priority configuration
     */
    public function getClickIdPriority(): array
    {
        return config('marketing-data-tracker.click_id_management.platform_priority', []);
    }

    /**
     * Get Google click ID configuration
     */
    public function getGoogleClickIdConfig(): array
    {
        return config('marketing-data-tracker.click_id_management.google_click_ids', []);
    }
}