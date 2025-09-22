<?php

use Marshmallow\MarketingData\Services\PlatformManager;

beforeEach(function () {
    // Note: We create new instances in tests after config changes
    $this->platformManager = new PlatformManager();
});

it('can get enabled platforms', function () {
    config([
        'marketing-data-tracker.platforms' => [
            'google_ads' => ['enabled' => true, 'name' => 'Google Ads'],
            'meta' => ['enabled' => false, 'name' => 'Meta'],
            'microsoft' => ['enabled' => true, 'name' => 'Microsoft Ads'],
        ]
    ]);

    $platformManager = new PlatformManager();
    $enabled = $platformManager->getEnabledPlatforms();

    expect($enabled)->toHaveKey('google_ads')
        ->and($enabled)->toHaveKey('microsoft')
        ->and($enabled)->not->toHaveKey('meta');
});

it('can check if platform is enabled', function () {
    config([
        'marketing-data-tracker.platforms.google_ads' => ['enabled' => true],
        'marketing-data-tracker.platforms.meta' => ['enabled' => false],
    ]);

    $platformManager = new PlatformManager();
    expect($platformManager->isPlatformEnabled('google_ads'))->toBeTrue()
        ->and($platformManager->isPlatformEnabled('meta'))->toBeFalse()
        ->and($platformManager->isPlatformEnabled('nonexistent'))->toBeFalse();
});

it('can get platform parameters', function () {
    config([
        'marketing-data-tracker.platforms.google_ads' => [
            'enabled' => true,
            'parameters' => ['gclid', 'gbraid', 'mm_campaignid']
        ],
        'marketing-data-tracker.platforms.meta' => [
            'enabled' => false,
            'parameters' => ['fbclid']
        ]
    ]);

    $platformManager = new PlatformManager();
    $googleParams = $platformManager->getPlatformParameters('google_ads');
    $metaParams = $platformManager->getPlatformParameters('meta');

    expect($googleParams)->toBe(['gclid', 'gbraid', 'mm_campaignid'])
        ->and($metaParams)->toBe([]); // Disabled platform returns empty
});

it('can get platform cookies', function () {
    config([
        'marketing-data-tracker.platforms.google_ads' => [
            'enabled' => true,
            'cookies' => ['_ga', '_gcl_aw', '_ga*']
        ]
    ]);

    $platformManager = new PlatformManager();
    $cookies = $platformManager->getPlatformCookies('google_ads');

    expect($cookies)->toBe(['_ga', '_gcl_aw', '_ga*']);
});

it('can get all tracking parameters', function () {
    config([
        'marketing-data-tracker.platforms' => [
            'google_ads' => [
                'enabled' => true,
                'parameters' => ['gclid', 'gbraid']
            ],
            'meta' => [
                'enabled' => true,
                'parameters' => ['fbclid']
            ],
            'microsoft' => [
                'enabled' => false,
                'parameters' => ['msclkid']
            ]
        ],
        'marketing-data-tracker.wildcard_patterns' => [
            'enabled' => true,
            'parameter_patterns' => ['utm_*', 'mm_*']
        ]
    ]);

    // Create a new instance after config changes
    $platformManager = new \Marshmallow\MarketingData\Services\PlatformManager();
    $allParams = $platformManager->getAllTrackingParameters();

    expect($allParams)->toContain('gclid')
        ->and($allParams)->toContain('gbraid')
        ->and($allParams)->toContain('fbclid')
        ->and($allParams)->not->toContain('msclkid') // Disabled platform
        ->and($allParams)->toContain('utm_*')
        ->and($allParams)->toContain('mm_*');
});

it('can match wildcard patterns', function () {
    config(['marketing-data-tracker.wildcard_patterns.enabled' => true]);

    $platformManager = new PlatformManager();
    $items = ['utm_source', 'utm_campaign', 'gclid', 'ga_test', 'ga_session'];
    $patterns = ['utm_*', 'ga_*'];
    $matches = $platformManager->matchWildcardPatterns($items, $patterns);

    expect($matches)->toContain('utm_source')
        ->and($matches)->toContain('utm_campaign')
        ->and($matches)->toContain('ga_test')
        ->and($matches)->toContain('ga_session')
        ->and($matches)->not->toContain('gclid');
});

it('returns empty array when wildcard patterns disabled', function () {
    config(['marketing-data-tracker.wildcard_patterns.enabled' => false]);

    $items = ['utm_source', 'utm_campaign'];
    $patterns = ['utm_*'];

    $platformManager = new PlatformManager();
    $matches = $platformManager->matchWildcardPatterns($items, $patterns);

    expect($matches)->toBeEmpty();
});

it('can match exact patterns', function () {
    config(['marketing-data-tracker.wildcard_patterns.enabled' => true]);

    $platformManager = new PlatformManager();
    $items = ['utm_source', 'gclid', 'fbclid'];
    $patterns = ['gclid', 'utm_*'];
    $matches = $platformManager->matchWildcardPatterns($items, $patterns);

    expect($matches)->toContain('gclid')
        ->and($matches)->toContain('utm_source')
        ->and($matches)->not->toContain('fbclid');
});

it('can get click id parameters', function () {
    config([
        'marketing-data-tracker.platforms' => [
            'google_ads' => [
                'enabled' => true,
                'click_id_params' => ['gclid', 'gbraid'],
                'click_id_cookies' => ['_gcl_aw']
            ],
            'meta' => [
                'enabled' => true,
                'click_id_params' => ['fbclid'],
                'click_id_cookies' => ['_fbp']
            ]
        ]
    ]);

    $platformManager = new PlatformManager();
    $allClickIds = $platformManager->getAllClickIdParameters();

    expect($allClickIds)->toContain('gclid')
        ->and($allClickIds)->toContain('gbraid')
        ->and($allClickIds)->toContain('_gcl_aw')
        ->and($allClickIds)->toContain('fbclid')
        ->and($allClickIds)->toContain('_fbp');
});

it('can get platform names', function () {
    config([
        'marketing-data-tracker.platforms' => [
            'google_ads' => ['enabled' => true, 'name' => 'Google Ads'],
            'meta' => ['enabled' => true, 'name' => 'Meta/Facebook'],
            'disabled_platform' => ['enabled' => false, 'name' => 'Disabled'],
        ]
    ]);

    $platformManager = new PlatformManager();
    $names = $platformManager->getAllPlatformNames();

    expect($names)->toBe([
        'google_ads' => 'Google Ads',
        'meta' => 'Meta/Facebook'
    ]);
});

it('can get click id priority', function () {
    config([
        'marketing-data-tracker.click_id_management.platform_priority' => [
            'gclid' => 10,
            'fbclid' => 9,
            'msclkid' => 8
        ]
    ]);

    $platformManager = new PlatformManager();
    $priority = $platformManager->getClickIdPriority();

    expect($priority)->toBe([
        'gclid' => 10,
        'fbclid' => 9,
        'msclkid' => 8
    ]);
});

it('can get google click id config', function () {
    config([
        'marketing-data-tracker.click_id_management.google_click_ids' => [
            'enabled' => true,
            'priority' => ['gclid', 'wbraid', 'gbraid']
        ]
    ]);

    $platformManager = new PlatformManager();
    $config = $platformManager->getGoogleClickIdConfig();

    expect($config['enabled'])->toBeTrue()
        ->and($config['priority'])->toBe(['gclid', 'wbraid', 'gbraid']);
});

it('filters parameters by patterns', function () {
    config([
        'marketing-data-tracker.wildcard_patterns' => [
            'enabled' => true,
            'parameter_patterns' => ['utm_*', 'gad_*']
        ]
    ]);

    $platformManager = new PlatformManager();
    $parameters = ['utm_source', 'utm_campaign', 'gad_source', 'fbclid', 'other_param'];
    $filtered = $platformManager->filterParametersByPatterns($parameters);

    expect($filtered)->toContain('utm_source')
        ->and($filtered)->toContain('utm_campaign')
        ->and($filtered)->toContain('gad_source')
        ->and($filtered)->not->toContain('fbclid')
        ->and($filtered)->not->toContain('other_param');
});

it('filters cookies by patterns', function () {
    config([
        'marketing-data-tracker.wildcard_patterns' => [
            'enabled' => true,
            'cookie_patterns' => ['_ga*', '_gcl*']
        ]
    ]);

    $platformManager = new PlatformManager();
    $cookies = ['_ga', '_ga_123', '_gcl_aw', '_fbp', 'other_cookie'];
    $filtered = $platformManager->filterCookiesByPatterns($cookies);

    expect($filtered)->toContain('_ga')
        ->and($filtered)->toContain('_ga_123')
        ->and($filtered)->toContain('_gcl_aw')
        ->and($filtered)->not->toContain('_fbp')
        ->and($filtered)->not->toContain('other_cookie');
});