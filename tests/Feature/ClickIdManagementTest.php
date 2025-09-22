<?php

use Marshmallow\MarketingData\Tests\Models\TestModel;

beforeEach(function () {
    // Set up test configuration
    config([
        'marketing-data-tracker.click_id_management' => [
            'enabled' => true,
            'google_click_ids' => [
                'enabled' => true,
                'priority' => ['gclid', 'wbraid', 'gbraid'],
                'cookie_mapping' => [
                    'gclid' => '_gcl_aw',
                    'wbraid' => '_gcl_gb',
                    'gbraid' => '_gcl_ag',
                ],
                'extract_gclid_value' => true,
            ],
            'platform_priority' => [
                'gclid' => 10,
                'fbclid' => 9,
                'msclkid' => 8,
                'ttclid' => 7,
                'twclid' => 6,
                'li_fat_id' => 5,
                'epik' => 4,
                'rdt_cid' => 3,
                'sscid' => 2,
                'gbraid' => 1,
                'wbraid' => 1,
            ],
        ],
    ]);
});

it('gets primary google click id with gclid priority', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => 'test_gclid_123',
        'gbraid' => 'test_gbraid_456',
        'wbraid' => 'test_wbraid_789',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBe('test_gclid_123');
});

it('gets primary google click id with wbraid when no gclid', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gbraid' => 'test_gbraid_456',
        'wbraid' => 'test_wbraid_789',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBe('test_wbraid_789');
});

it('gets primary google click id with gbraid when only available', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gbraid' => 'test_gbraid_456',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBe('test_gbraid_456');
});

it('extracts gclid value from cookie format', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => 'GA1.1.123456789.1234567890.test_gclid_value',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBe('test_gclid_value');
});

it('does not extract gclid when disabled', function () {
    config(['marketing-data-tracker.click_id_management.google_click_ids.extract_gclid_value' => false]);

    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => 'GA1.1.123456789.1234567890.test_gclid_value',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBe('GA1.1.123456789.1234567890.test_gclid_value');
});

it('prefers cookie value over session value', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => 'session_gclid',
        '_gcl_aw' => 'cookie_gclid',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBe('cookie_gclid');
});

it('returns null when google click ids disabled', function () {
    config(['marketing-data-tracker.click_id_management.google_click_ids.enabled' => false]);

    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => 'test_gclid_123',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBeNull();
});

it('returns null when no google click ids present', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'fbclid' => 'test_fbclid_123',
        'utm_source' => 'facebook',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBeNull();
});

it('gets primary click id with platform priority', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'fbclid' => 'test_fbclid_123',
        'msclkid' => 'test_msclkid_456',
        'ttclid' => 'test_ttclid_789',
    ]);

    $primaryId = $model->primary_click_id;

    expect($primaryId)->toBe('test_fbclid_123'); // fbclid has priority 9, highest
});

it('gets primary click id gclid highest priority', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => 'test_gclid_123',
        'fbclid' => 'test_fbclid_456',
        'msclkid' => 'test_msclkid_789',
    ]);

    $primaryId = $model->primary_click_id;

    expect($primaryId)->toBe('test_gclid_123'); // gclid has priority 10, highest
});

it('extracts click id value with google cookie format', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => 'GA1.1.123456789.1234567890.extracted_value',
    ]);

    $primaryId = $model->primary_click_id;

    expect($primaryId)->toBe('extracted_value');
});

it('does not extract non google click ids', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'fbclid' => 'FB.1.123456789.test_value',
    ]);

    $primaryId = $model->primary_click_id;

    expect($primaryId)->toBe('FB.1.123456789.test_value'); // No extraction for Facebook
});

it('returns null primary click id when none present', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'utm_source' => 'google',
        'utm_campaign' => 'test',
    ]);

    $primaryId = $model->primary_click_id;

    expect($primaryId)->toBeNull();
});

it('gets all click ids attribute', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => 'test_gclid',
        'fbclid' => 'test_fbclid',
        'utm_source' => 'google', // Not a click ID
    ]);

    $clickIds = $model->getAllClickIdsAttribute();

    expect($clickIds)->toHaveKey('gclid')
        ->and($clickIds)->toHaveKey('fbclid')
        ->and($clickIds)->not->toHaveKey('utm_source')
        ->and($clickIds['gclid'])->toBe('test_gclid')
        ->and($clickIds['fbclid'])->toBe('test_fbclid');
});

it('has any click id attribute', function () {
    $modelWithClickIds = new TestModel();
    $modelWithClickIds->setMarketingData(['gclid' => 'test_gclid']);

    $modelWithoutClickIds = new TestModel();
    $modelWithoutClickIds->setMarketingData(['utm_source' => 'google']);

    expect($modelWithClickIds->has_any_click_id)->toBeTrue()
        ->and($modelWithoutClickIds->has_any_click_id)->toBeFalse();
});

it('custom priority configuration', function () {
    // Test custom priority order
    config([
        'marketing-data-tracker.click_id_management.platform_priority' => [
            'ttclid' => 10, // TikTok highest
            'gclid' => 9,   // Google second
            'fbclid' => 8,  // Facebook third
        ]
    ]);

    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => 'test_gclid',
        'fbclid' => 'test_fbclid',
        'ttclid' => 'test_ttclid',
    ]);

    $primaryId = $model->primary_click_id;

    expect($primaryId)->toBe('test_ttclid'); // TikTok should win with custom priority
});

it('trims whitespace from click ids', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => '  test_gclid_with_whitespace  ',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBe('test_gclid_with_whitespace');
});

it('ignores empty click id values', function () {
    $model = new TestModel();
    $model->setMarketingData([
        'gclid' => '',
        'wbraid' => '   ', // Just whitespace
        'gbraid' => 'valid_gbraid',
    ]);

    $primaryId = $model->getPrimaryGoogleClickId();

    expect($primaryId)->toBe('valid_gbraid'); // Should skip empty values
});