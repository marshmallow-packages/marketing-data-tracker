<?php

use Marshmallow\MarketingData\Builders\MarketingUrlBuilder;

it('can create builder instance', function () {
    $builder = MarketingUrlBuilder::make('https://example.com');

    expect($builder)->toBeInstanceOf(MarketingUrlBuilder::class)
        ->and($builder->build())->toBe('https://example.com');
});

it('can add utm parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withUTM('google', 'cpc', 'summer-sale', 'shoes', 'banner')
        ->build();

    expect($url)->toContain('utm_source=google')
        ->and($url)->toContain('utm_medium=cpc')
        ->and($url)->toContain('utm_campaign=summer-sale')
        ->and($url)->toContain('utm_term=shoes')
        ->and($url)->toContain('utm_content=banner');
});

it('can add partial utm parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withUTM('google', 'cpc')
        ->build();

    expect($url)->toContain('utm_source=google')
        ->and($url)->toContain('utm_medium=cpc')
        ->and($url)->not->toContain('utm_campaign');
});

it('can add google ads parameters', function () {
    config([
        'marketing-data-tracker.tracking_urls.google_ads' => 'utm_source=google&utm_medium=cpc&gclid={gclid}&mm_campaignid={campaignid}'
    ]);

    $url = MarketingUrlBuilder::make('https://example.com')
        ->withGoogleAds(['mm_keyword' => 'test-keyword'])
        ->build();

    expect($url)->toContain('utm_source=google')
        ->and($url)->toContain('utm_medium=cpc')
        ->and($url)->toContain('gclid={gclid}')
        ->and($url)->toContain('mm_campaignid={campaignid}')
        ->and($url)->toContain('mm_keyword=test-keyword');
});

it('can add meta ads parameters', function () {
    config([
        'marketing-data-tracker.tracking_urls.meta_ads' => 'utm_source=facebook&utm_medium=paid&fbclid={{fbclid}}'
    ]);

    $url = MarketingUrlBuilder::make('https://example.com')
        ->withMetaAds(['custom_param' => 'test'])
        ->build();

    expect($url)->toContain('utm_source=facebook')
        ->and($url)->toContain('utm_medium=paid')
        ->and($url)->toContain('fbclid={{fbclid}}')
        ->and($url)->toContain('custom_param=test');
});

it('can add platform specific parameters', function () {
    config([
        'marketing-data-tracker.platforms.google_ads' => [
            'enabled' => true,
            'parameters' => ['gclid', 'mm_campaignid', 'mm_keyword']
        ]
    ]);

    $url = MarketingUrlBuilder::make('https://example.com')
        ->withPlatform('google_ads', [
            'gclid' => 'test_gclid',
            'mm_campaignid' => '12345',
            'forbidden_param' => 'should_not_appear'
        ])
        ->build();

    expect($url)->toContain('gclid=test_gclid')
        ->and($url)->toContain('mm_campaignid=12345')
        ->and($url)->not->toContain('forbidden_param');
});

it('platform disabled returns no parameters', function () {
    config([
        'marketing-data-tracker.platforms.disabled_platform' => [
            'enabled' => false,
            'parameters' => ['test_param']
        ]
    ]);

    $url = MarketingUrlBuilder::make('https://example.com')
        ->withPlatform('disabled_platform', ['test_param' => 'value'])
        ->build();

    expect($url)->toBe('https://example.com');
});

it('can add google valuetrack parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withGoogleValueTrack(['custom_param' => '{custom}'])
        ->build();

    expect($url)->toContain('gclid={gclid}')
        ->and($url)->toContain('mm_campaignid={campaignid}')
        ->and($url)->toContain('mm_keyword={keyword}')
        ->and($url)->toContain('custom_param={custom}');
});

it('can add meta dynamic parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withMetaDynamicParams()
        ->build();

    expect($url)->toContain('mm_campaignid={{campaign.id}}')
        ->and($url)->toContain('mm_adgroupid={{adset.id}}')
        ->and($url)->toContain('fbclid={{fbclid}}');
});

it('can add microsoft ads parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withMicrosoftAds(['utm_mskeyword' => 'test-keyword'])
        ->build();

    expect($url)->toContain('utm_source=bing')
        ->and($url)->toContain('utm_medium=cpc')
        ->and($url)->toContain('msclkid={msclkid}')
        ->and($url)->toContain('utm_mskeyword=test-keyword');
});

it('can add linkedin ads parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withLinkedInAds()
        ->build();

    expect($url)->toContain('utm_source=linkedin')
        ->and($url)->toContain('utm_medium=paid')
        ->and($url)->toContain('li_fat_id={li_fat_id}');
});

it('can add twitter ads parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withTwitterAds()
        ->build();

    expect($url)->toContain('utm_source=twitter')
        ->and($url)->toContain('utm_medium=paid')
        ->and($url)->toContain('twclid={twclid}');
});

it('can add custom parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withCustomParams(['custom1' => 'value1', 'custom2' => 'value2'])
        ->build();

    expect($url)->toContain('custom1=value1')
        ->and($url)->toContain('custom2=value2');
});

it('can set single parameter', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withParameter('test_param', 'test_value')
        ->build();

    expect($url)->toContain('test_param=test_value');
});

it('can remove parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withUTM('google', 'cpc', 'campaign')
        ->without('utm_campaign')
        ->build();

    expect($url)->toContain('utm_source=google')
        ->and($url)->toContain('utm_medium=cpc')
        ->and($url)->not->toContain('utm_campaign');
});

it('can remove multiple parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withUTM('google', 'cpc', 'campaign', 'term', 'content')
        ->withoutParameters(['utm_term', 'utm_content'])
        ->build();

    expect($url)->toContain('utm_source=google')
        ->and($url)->not->toContain('utm_term')
        ->and($url)->not->toContain('utm_content');
});

it('can clear all parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withUTM('google', 'cpc', 'campaign')
        ->clearParameters()
        ->build();

    expect($url)->toBe('https://example.com');
});

it('filters empty parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com')
        ->withUTM('google', '', null)
        ->withParameter('empty', '')
        ->withParameter('null_param', null)
        ->withParameter('valid', 'value')
        ->build();

    expect($url)->toContain('utm_source=google')
        ->and($url)->toContain('valid=value')
        ->and($url)->not->toContain('utm_medium')
        ->and($url)->not->toContain('utm_campaign')
        ->and($url)->not->toContain('empty')
        ->and($url)->not->toContain('null_param');
});

it('handles existing query parameters', function () {
    $url = MarketingUrlBuilder::make('https://example.com?existing=param')
        ->withUTM('google', 'cpc')
        ->build();

    expect($url)->toContain('existing=param')
        ->and($url)->toContain('utm_source=google')
        ->and($url)->toContain('&utm_source'); // Should use & not ?
});

it('static google ads method', function () {
    config([
        'marketing-data-tracker.tracking_urls.google_ads' => 'gclid={gclid}'
    ]);

    $url = MarketingUrlBuilder::googleAds('https://example.com', 'test-campaign');

    expect($url)->toContain('utm_source=google')
        ->and($url)->toContain('utm_medium=cpc')
        ->and($url)->toContain('utm_campaign=test-campaign')
        ->and($url)->toContain('gclid={gclid}');
});

it('static meta ads method', function () {
    config([
        'marketing-data-tracker.tracking_urls.meta_ads' => 'fbclid={{fbclid}}'
    ]);

    $url = MarketingUrlBuilder::metaAds('https://example.com', 'awareness-campaign');

    expect($url)->toContain('utm_source=facebook')
        ->and($url)->toContain('utm_medium=paid')
        ->and($url)->toContain('utm_campaign=awareness-campaign')
        ->and($url)->toContain('fbclid={{fbclid}}');
});

it('static utm method', function () {
    $url = MarketingUrlBuilder::utm(
        'https://example.com',
        'newsletter',
        'email',
        'weekly-digest',
        'banner',
        'top'
    );

    expect($url)->toContain('utm_source=newsletter')
        ->and($url)->toContain('utm_medium=email')
        ->and($url)->toContain('utm_campaign=weekly-digest')
        ->and($url)->toContain('utm_term=banner')
        ->and($url)->toContain('utm_content=top');
});

it('from template method', function () {
    config([
        'marketing-data-tracker.tracking_urls.custom_template' => 'source={source}&campaign={campaign}'
    ]);

    $url = MarketingUrlBuilder::fromTemplate(
        'https://example.com',
        'custom_template',
        ['source' => 'google', 'campaign' => 'test']
    );

    expect($url)->toContain('source=google')
        ->and($url)->toContain('campaign=test');
});

it('toString method', function () {
    $builder = MarketingUrlBuilder::make('https://example.com')
        ->withUTM('google', 'cpc');

    $url = (string) $builder;

    expect($url)->toContain('utm_source=google')
        ->and($url)->toContain('utm_medium=cpc');
});

it('get parameters method', function () {
    $builder = MarketingUrlBuilder::make('https://example.com')
        ->withUTM('google', 'cpc', 'campaign');

    $params = $builder->getParameters();

    expect($params)->toBe([
        'utm_source' => 'google',
        'utm_medium' => 'cpc',
        'utm_campaign' => 'campaign'
    ]);
});