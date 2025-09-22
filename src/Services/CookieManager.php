<?php

namespace Marshmallow\MarketingData\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Marshmallow\MarketingData\Services\PlatformManager;

class CookieManager
{
    protected array $config;
    protected PlatformManager $platformManager;

    public function __construct(PlatformManager $platformManager)
    {
        $this->config = config('marketing-data-tracker.cookie_management', []);
        $this->platformManager = $platformManager;
    }

    /**
     * Get all cookies that should be tracked
     */
    public function getTrackableCookies(): array
    {
        if (!($this->config['enabled'] ?? false)) {
            return [];
        }

        // Get cookies from all enabled platforms
        $platformCookies = $this->platformManager->getAllTrackingCookies();

        // Get cookies from cookie groups
        $groupCookies = $this->getAllGroupCookies();

        // Merge and remove duplicates
        $allCookies = array_unique(array_merge($platformCookies, $groupCookies));

        return $allCookies;
    }

    /**
     * Check if cookie tracking is allowed for a specific group
     */
    public function isTrackingAllowed(?string $group = null): bool
    {
        if (!($this->config['enabled'] ?? false)) {
            return false;
        }

        $consentConfig = $this->config['consent'] ?? [];

        if (!($consentConfig['enabled'] ?? false)) {
            return true; // No consent management, tracking allowed
        }

        if (!$consentConfig['respect_consent'] ?? true) {
            return true; // Not respecting consent, tracking allowed
        }

        $cookieName = $consentConfig['cookie_name'] ?? 'cookie_consent';
        $consent = $this->getConsentFromCookie($cookieName);

        if (!$group) {
            return true; // No specific group, check general consent
        }

        return $consent[$group] ?? false;
    }

    /**
     * Get cookies by group
     */
    public function getCookiesByGroup(string $group): array
    {
        $groups = $this->config['groups'] ?? [];

        if (!isset($groups[$group])) {
            return [];
        }

        $groupConfig = $groups[$group];
        $cookies = $groupConfig['cookies'] ?? [];

        // Expand wildcard patterns if enabled
        if ($this->config['wildcard_support'] ?? false) {
            $cookies = $this->expandWildcardCookies($cookies);
        }

        return $cookies;
    }

    /**
     * Get all cookies from all groups
     */
    protected function getAllGroupCookies(): array
    {
        $allCookies = [];
        $groups = $this->config['groups'] ?? [];

        foreach ($groups as $groupName => $groupConfig) {
            $cookies = $groupConfig['cookies'] ?? [];
            $allCookies = array_merge($allCookies, $cookies);
        }

        return array_unique($allCookies);
    }

    /**
     * Match wildcard patterns against available cookies
     */
    public function matchWildcardCookies(array $availableCookies, array $patterns): array
    {
        if (!($this->config['wildcard_support'] ?? false)) {
            return [];
        }

        $matches = [];

        foreach ($patterns as $pattern) {
            if (!str_contains($pattern, '*')) {
                // Exact match
                if (in_array($pattern, $availableCookies)) {
                    $matches[] = $pattern;
                }
                continue;
            }

            // Wildcard pattern matching
            $regex = str_replace('*', '.*', preg_quote($pattern, '/'));
            foreach ($availableCookies as $cookie) {
                if (preg_match("/^{$regex}$/", $cookie)) {
                    $matches[] = $cookie;
                }
            }
        }

        return array_unique($matches);
    }

    /**
     * Expand wildcard patterns in cookie list
     */
    protected function expandWildcardCookies(array $cookies): array
    {
        // For expansion, we would need access to actual available cookies
        // This is more useful in filtering scenarios
        return $cookies;
    }

    /**
     * Filter cookies based on consent and group settings
     */
    public function filterCookiesByConsent(array $cookies): array
    {
        if (!($this->config['enabled'] ?? false)) {
            return [];
        }

        $filteredCookies = [];
        $groups = $this->config['groups'] ?? [];

        foreach ($groups as $groupName => $groupConfig) {
            if (!$this->isTrackingAllowed($groupName)) {
                continue; // Skip this group if tracking not allowed
            }

            $groupCookies = $groupConfig['cookies'] ?? [];
            $matchingCookies = $this->matchWildcardCookies($cookies, $groupCookies);
            $filteredCookies = array_merge($filteredCookies, $matchingCookies);
        }

        return array_unique($filteredCookies);
    }

    /**
     * Get cookie values from request
     */
    public function getCookieValues(Request $request, ?array $cookieNames = null): array
    {
        if ($cookieNames === null) {
            $cookieNames = $this->getTrackableCookies();
        }

        $cookieValues = [];
        $availableCookies = array_keys($request->cookies->all());

        foreach ($cookieNames as $cookieName) {
            if (str_contains($cookieName, '*')) {
                // Handle wildcard patterns
                $matches = $this->matchWildcardCookies($availableCookies, [$cookieName]);
                foreach ($matches as $match) {
                    $value = $request->cookie($match);
                    if ($value !== null) {
                        $cookieValues[$match] = $value;
                    }
                }
            } else {
                // Exact cookie name
                $value = $request->cookie($cookieName);
                if ($value !== null) {
                    $cookieValues[$cookieName] = $value;
                }
            }
        }

        // Filter by consent if enabled
        return $this->filterCookiesByConsent($cookieValues);
    }

    /**
     * Get consent from cookie
     */
    protected function getConsentFromCookie(string $cookieName): array
    {
        $consentCookie = request()->cookie($cookieName);

        if (!$consentCookie) {
            // No consent cookie, use defaults
            $defaultConsent = $this->config['consent']['default_consent'] ?? [];
            return array_merge([
                'functional' => true,
                'analytics' => false,
                'advertising' => false,
            ], $defaultConsent);
        }

        // Parse consent cookie (assume JSON format)
        $consent = json_decode($consentCookie, true);

        if (!is_array($consent)) {
            // Invalid consent cookie, use defaults
            return $this->config['consent']['default_consent'] ?? [
                'functional' => true,
                'analytics' => false,
                'advertising' => false,
            ];
        }

        return $consent;
    }

    /**
     * Check if a specific cookie is trackable
     */
    public function isCookieTrackable(string $cookieName): bool
    {
        $trackableCookies = $this->getTrackableCookies();

        // Direct match
        if (in_array($cookieName, $trackableCookies)) {
            return true;
        }

        // Wildcard match
        $wildcardPatterns = array_filter($trackableCookies, fn($cookie) => str_contains($cookie, '*'));
        $matches = $this->matchWildcardCookies([$cookieName], $wildcardPatterns);

        return !empty($matches);
    }

    /**
     * Get cookie group for a specific cookie
     */
    public function getCookieGroup(string $cookieName): ?string
    {
        $groups = $this->config['groups'] ?? [];

        foreach ($groups as $groupName => $groupConfig) {
            $groupCookies = $groupConfig['cookies'] ?? [];
            $matches = $this->matchWildcardCookies([$cookieName], $groupCookies);

            if (!empty($matches)) {
                return $groupName;
            }
        }

        return null;
    }

    /**
     * Get all cookie groups
     */
    public function getAllGroups(): array
    {
        return array_keys($this->config['groups'] ?? []);
    }

    /**
     * Get group configuration
     */
    public function getGroupConfig(string $group): array
    {
        return $this->config['groups'][$group] ?? [];
    }

    /**
     * Check if consent management is enabled
     */
    public function isConsentManagementEnabled(): bool
    {
        return $this->config['consent']['enabled'] ?? false;
    }

    /**
     * Get consent cookie name
     */
    public function getConsentCookieName(): string
    {
        return $this->config['consent']['cookie_name'] ?? 'cookie_consent';
    }

    /**
     * Get default consent settings
     */
    public function getDefaultConsent(): array
    {
        return $this->config['consent']['default_consent'] ?? [
            'functional' => true,
            'analytics' => false,
            'advertising' => false,
        ];
    }

    /**
     * Set consent cookie
     */
    public function setConsentCookie(array $consent, int $minutes = 43200): void // 30 days default
    {
        $cookieName = $this->getConsentCookieName();
        $cookieValue = json_encode($consent);

        cookie()->queue(
            cookie($cookieName, $cookieValue, $minutes, '/', null, true, false)
        );
    }

    /**
     * Clear consent cookie
     */
    public function clearConsentCookie(): void
    {
        $cookieName = $this->getConsentCookieName();
        cookie()->queue(cookie()->forget($cookieName));
    }
}