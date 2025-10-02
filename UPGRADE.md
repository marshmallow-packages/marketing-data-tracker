# Upgrade Guide: v1 to v2

This guide will help you safely upgrade from MarketingDataTracker v1.x to v2.0 while taking advantage of the new enterprise-grade features.

## 🔒 Backward Compatibility Promise

**Version 2.0 is 100% backward compatible with v1.x**. Your existing code will continue to work without any modifications. All new features are opt-in and disabled by default.

---

## 🚀 Quick Upgrade (Minimal Changes)

If you want to upgrade with minimal changes and keep using v1 functionality:

```bash
# 1. Update the package
composer update marshmallow/marketing-data-tracker

# 2. Clear cache (recommended)
php artisan config:clear
php artisan cache:clear

# That's it! Your existing code continues to work.
```

---

## 🔧 Full Upgrade (Access New Features)

To access the new v2 features, follow these steps:

### Step 1: Backup Your Configuration

```bash
# Backup your current config
cp config/marketing-data-tracker.php config/marketing-data-tracker-v1-backup.php
```

### Step 2: Update the Package

```bash
composer update marshmallow/marketing-data-tracker
```

### Step 3: Publish New Configuration

```bash
php artisan vendor:publish --tag="marketing-data-tracker-config" --force
```

⚠️ **Warning**: This overwrites your existing configuration file.

### Step 4: Merge Your Custom Settings

If you had custom settings in v1, merge them with the new configuration structure:

```php
// config/marketing-data-tracker.php

// Your existing parameters still work in the legacy sections:
'store_marketing_parameters' => [
    // Your custom parameters here
],

'store_marketing_cookies' => [
    // Your custom cookies here
],

// Plus new platform-specific configuration:
'platforms' => [
    'google_ads' => [
        'enabled' => true,
        'parameters' => [/* Google-specific parameters */],
        'cookies' => [/* Google-specific cookies */],
    ],
    // ... other platforms
],
```

### Step 5: Enable Desired Features

Choose which new features to enable:

#### 5.1 Enhanced Platform Support

```php
'platforms' => [
    'google_ads' => ['enabled' => true],
    'meta' => ['enabled' => true],
    'microsoft' => ['enabled' => true],
    'linkedin' => ['enabled' => true],
    'twitter' => ['enabled' => true],
    'pinterest' => ['enabled' => true],
    'tiktok' => ['enabled' => true],
    'reddit' => ['enabled' => true],
    'snapchat' => ['enabled' => true],
    'amazon' => ['enabled' => true],
    'tradetracker' => ['enabled' => true],
    'email_marketing' => ['enabled' => true],
],
```

#### 5.2 Enhanced Click ID Management

```php
'click_id_management' => [
    'enabled' => true,
    'google_click_ids' => [
        'enabled' => true,
        'priority' => ['gclid', 'wbraid', 'gbraid'],
        'extract_gclid_value' => true, // Clean extraction from cookie format
    ],
    'platform_priority' => [
        'gclid' => 10,    // Google Ads - highest priority
        'fbclid' => 9,    // Facebook/Meta
        'msclkid' => 8,   // Microsoft/Bing
        'ttclid' => 7,    // TikTok
        'twclid' => 6,    // Twitter/X
        'li_fat_id' => 5, // LinkedIn
        'epik' => 4,      // Pinterest
        'rdt_cid' => 3,   // Reddit
        'sscid' => 2,     // Snapchat
        'gbraid' => 1,    // Google iOS tracking
        'wbraid' => 1,    // Google iOS web-to-app
    ],
],
```

#### 5.3 Wildcard Pattern Support

```php
'wildcard_patterns' => [
    'enabled' => true,
    'parameter_patterns' => ['gad_*', 'mm_*', 'utm_*', 'tm_*', 'tw_*', 'tt_*'],
    'cookie_patterns' => ['_ga*', '_gcl*', '_fbp*', '_fbc*', '_ttp*', '_epik*', '_uet*', 'li_*'],
],
```

#### 5.4 Event System (for Analytics Integration)

```php
'events' => [
    'enabled' => true,
    'listeners' => [
        // Add custom event listeners here
        'Marshmallow\\MarketingData\\Events\\ConversionTracked' => [
            'App\\Listeners\\SendToGoogleAds',
            'App\\Listeners\\SendToFacebookConversions',
        ],
    ],
],
```

#### 5.5 Automatic Model Observation (Advanced)

```php
'observers' => [
    'enabled' => false, // Start with false, enable only if needed
    'models' => [
        // App\Models\Lead::class,
        // App\Models\Order::class,
    ],
    'auto_set_utm' => true,
    'auto_detect_click_ids' => true,
    'forget_after_save' => true,
],
```

#### 5.6 Conversion Tracking

```php
'conversions' => [
    'enabled' => true,
    'types' => [
        'lead' => ['value' => null, 'priority' => 1, 'description' => 'Lead generation'],
        'qualified_lead' => ['value' => null, 'priority' => 2, 'description' => 'Qualified lead'],
        'purchase' => ['value' => null, 'priority' => 5, 'description' => 'Purchase conversion'],
    ],
    'auto_track' => false,
    'track_value' => true,
],
```

#### 5.7 E-commerce Tracking

```php
'ecommerce' => [
    'enabled' => true,
    'currency' => 'EUR', // Your default currency
    'events' => [
        'view_item' => true,
        'add_to_cart' => true,
        'remove_from_cart' => true,
        'begin_checkout' => true,
        'purchase' => true,
    ],
    'gtm_format' => true,
    'platform_formats' => [
        'google_ads' => true,
        'meta' => true,
    ],
],
```

#### 5.8 Cookie Management & Consent

```php
'cookie_management' => [
    'enabled' => true,
    'groups' => [
        'analytics' => [
            'cookies' => ['_ga', '_gid', '_ga_*'],
            'required' => false,
            'description' => 'Analytics and performance tracking',
        ],
        'advertising' => [
            'cookies' => ['_gcl_*', '_fbp', 'fbc', 'ttclid', '_epik', '_uet*'],
            'required' => false,
            'description' => 'Advertising and marketing attribution',
        ],
        'functional' => [
            'cookies' => ['session_id'],
            'required' => true,
            'description' => 'Essential website functionality',
        ],
    ],
    'consent' => [
        'enabled' => false, // Enable if you need consent management
        'cookie_name' => 'cookie_consent',
        'respect_consent' => true,
        'default_consent' => [
            'functional' => true,
            'analytics' => false,
            'advertising' => false,
        ],
    ],
],
```

---

## 📝 Code Migration Examples

### Enhanced Click ID Access

```php
// v1 code (still works)
$clickId = $user->primary_click_id;

// v2 enhanced code
$googleClickId = $user->getPrimaryGoogleClickId(); // Priority: gclid > wbraid > gbraid
$anyClickId = $user->primary_click_id; // Cross-platform priority
```

### Add New Traits for Enhanced Functionality

```php
use Marshmallow\MarketingData\Traits\HasMarketingParameters;
use Marshmallow\MarketingData\Traits\TracksConversions;
use Marshmallow\MarketingData\Traits\TracksEcommerceEvents;

class Order extends Model
{
    use HasMarketingParameters; // Your existing trait
    use TracksConversions;      // NEW: Conversion tracking
    use TracksEcommerceEvents;  // NEW: E-commerce events

    // All your existing code remains unchanged
}

// New functionality available
$order->trackPurchase('TXN-123', $products, 299.99, 'EUR');
$order->trackConversion('purchase', 299.99);
$order->trackLeadConversion(50.00);
```

### Marketing URL Builder

```php
use Marshmallow\MarketingData\Builders\MarketingUrlBuilder;

// Build Google Ads campaign URLs
$url = MarketingUrlBuilder::googleAds('https://example.com', 'summer-sale')
    ->withGoogleValueTrack()
    ->build();

// Build Meta/Facebook Ads URLs
$url = MarketingUrlBuilder::metaAds('https://example.com', 'awareness-campaign')
    ->withMetaDynamicParams()
    ->build();

// Build custom URLs with fluent interface
$url = MarketingUrlBuilder::make('https://example.com')
    ->withUTM('google', 'cpc', 'campaign')
    ->withPlatform('google_ads', ['mm_keyword' => 'shoes'])
    ->withCustomParams(['custom' => 'value'])
    ->build();
```

### Event Listeners for Analytics Integration

```php
// app/Providers/EventServiceProvider.php
use Marshmallow\MarketingData\Events\ConversionTracked;
use Marshmallow\MarketingData\Events\ClickIdDetected;
use Marshmallow\MarketingData\Events\MarketingDataCreated;

protected $listen = [
    ConversionTracked::class => [
        App\Listeners\SendConversionToGoogleAds::class,
        App\Listeners\SendConversionToMeta::class,
    ],
    ClickIdDetected::class => [
        App\Listeners\StoreClickIdForServerSideTracking::class,
    ],
    MarketingDataCreated::class => [
        App\Listeners\NotifyAnalyticsOfNewAcquisition::class,
    ],
];
```

### Service Usage

```php
use Marshmallow\MarketingData\Services\PlatformManager;
use Marshmallow\MarketingData\Services\CookieManager;

// Platform management
$platformManager = app(PlatformManager::class);
$enabledPlatforms = $platformManager->getEnabledPlatforms();
$googleParams = $platformManager->getPlatformParameters('google_ads');

// Cookie management with consent
$cookieManager = app(CookieManager::class);
if ($cookieManager->isTrackingAllowed('advertising')) {
    $cookies = $cookieManager->getCookieValues($request);
}
```

---

## 🧪 Testing Your Upgrade

### 1. Test Existing Functionality

```php
// Ensure existing UTM tracking still works
public function test_existing_utm_tracking_still_works()
{
    $this->get('/?utm_source=google&utm_campaign=test&gclid=123');

    $user = User::factory()->create();
    $user->setUtmSourceData();

    $this->assertEquals('google', $user->utm_source);
    $this->assertEquals('test', $user->utm_campaign);
    $this->assertEquals('123', $user->gclid);
}
```

### 2. Test New Click ID Priority

```php
public function test_new_click_id_priority()
{
    $user = new User();
    // Simulate having multiple click IDs
    $user->gclid = 'test_gclid';
    $user->fbclid = 'test_fbclid';

    // Should return gclid (highest priority)
    $this->assertEquals('test_gclid', $user->primary_click_id);

    // Google-specific method
    $this->assertEquals('test_gclid', $user->getPrimaryGoogleClickId());
}
```

### 3. Test Platform Detection

```php
public function test_platform_detection()
{
    $user = new User();
    $user->gclid = 'test_gclid';

    $this->assertEquals('Google Ads', $user->platform_name);
    $this->assertEquals('google_ads', $user->detectPlatformFromMarketingData());
}
```

### 4. Test URL Builder

```php
public function test_url_builder()
{
    $url = MarketingUrlBuilder::googleAds('https://example.com', 'test-campaign');

    $this->assertStringContains('utm_source=google', $url);
    $this->assertStringContains('utm_campaign=test-campaign', $url);
    $this->assertStringContains('gclid={gclid}', $url);
}
```

---

## 🔧 Migration Checklist

### Pre-Migration

-   [ ] **Backup Configuration**: `cp config/marketing-data-tracker.php config/marketing-data-tracker-v1-backup.php`
-   [ ] **Review Current Usage**: Document which features you currently use
-   [ ] **Test Environment**: Perform migration in staging/testing environment first

### Migration Steps

-   [ ] **Update Package**: `composer update marshmallow/marketing-data-tracker`
-   [ ] **Clear Cache**: `php artisan config:clear && php artisan cache:clear`
-   [ ] **Publish Config**: `php artisan vendor:publish --tag="marketing-data-tracker-config" --force`
-   [ ] **Merge Settings**: Integrate custom settings from backup with new config
-   [ ] **Enable Platforms**: Configure platforms you use in new `platforms` section
-   [ ] **Enable Features**: Turn on desired new features (click ID management, events, etc.)

### Code Updates (Optional)

-   [ ] **Add New Traits**: Include `TracksConversions` and `TracksEcommerceEvents` if needed
-   [ ] **Implement Event Listeners**: Set up analytics integration via events
-   [ ] **Use URL Builder**: Replace manual URL construction with `MarketingUrlBuilder`
-   [ ] **Update Method Calls**: Use new `getPrimaryGoogleClickId()` method where appropriate

### Testing

-   [ ] **Test Existing Functionality**: Verify UTM tracking still works
-   [ ] **Test New Click ID Priority**: Verify priority-based selection
-   [ ] **Test Platform Detection**: Ensure platforms are detected correctly
-   [ ] **Test Events**: Verify events fire correctly if enabled
-   [ ] **Test URL Builder**: Verify URLs are generated correctly
-   [ ] **Performance Test**: Ensure no performance degradation

### Post-Migration

-   [ ] **Monitor Logs**: Check for any errors or warnings
-   [ ] **Validate Data**: Ensure marketing data is captured correctly
-   [ ] **Update Documentation**: Document your configuration choices
-   [ ] **Train Team**: Brief team on new features and capabilities

---

## 🚨 Troubleshooting Common Issues

### Issue 1: Configuration Override

**Problem**: New config overwrites custom settings
**Solution**: Merge your custom settings from the backup file into the new config structure

```php
// Restore custom parameters
'store_marketing_parameters' => [
    // ... standard parameters
    'custom_param_1',
    'custom_param_2',
],

// And use new platform structure
'platforms' => [
    'google_ads' => [
        'enabled' => true,
        'parameters' => ['custom_param_1', /* ... */],
    ],
],
```

### Issue 2: Events Not Firing

**Problem**: Events enabled but listeners not receiving them
**Solution**:

1. Ensure `'events' => ['enabled' => true]`
2. Check listener registration in `EventServiceProvider`
3. Clear cache: `php artisan config:clear`

### Issue 3: Platform Not Detected

**Problem**: `platform_name` returns null
**Solution**: Ensure the platform is enabled in config:

```php
'platforms' => [
    'google_ads' => ['enabled' => true], // Must be true
],
```

### Issue 4: Click IDs Not Prioritized

**Problem**: `getPrimaryGoogleClickId()` returns null
**Solution**: Enable click ID management:

```php
'click_id_management' => [
    'enabled' => true,
    'google_click_ids' => ['enabled' => true],
],
```

### Issue 5: Wildcard Patterns Not Working

**Problem**: `_ga*` patterns not matching cookies
**Solution**: Enable wildcard patterns:

```php
'wildcard_patterns' => [
    'enabled' => true,
    'cookie_patterns' => ['_ga*', '_gcl*'],
],
```

### Issue 6: Observer Conflicts

**Problem**: Observer causing model save issues
**Solution**: Disable observers or configure carefully:

```php
'observers' => [
    'enabled' => false, // Disable if causing issues
],
```

### Issue 7: Performance Issues

**Problem**: Slower response times after upgrade
**Solution**:

1. Disable unused features
2. Optimize wildcard patterns
3. Use caching for platform configurations

### Issue 8: Cookie Values Empty/Null

**Problem**: Marketing cookies show as empty even though they exist in browser
**Solution**: v2 automatically handles encrypted cookies, but you can configure:

```php
'cookie_management' => [
    'auto_register_exceptions' => true, // Automatically excludes marketing cookies from encryption
],
```

**What it does**:

-   Automatically registers marketing cookies with Laravel's `EncryptCookies` middleware
-   Falls back to reading directly from `$_COOKIE` if cookies are encrypted
-   Ensures `_fbp`, `_gcl_aw`, `gclid` and other marketing cookies are readable

**Manual Alternative**: If automatic registration doesn't work, manually add cookies to your `EncryptCookies` middleware:

```php
// In app/Http/Middleware/EncryptCookies.php
protected $except = [
    '_ga', '_gid', '_gcl_aw', '_gcl_gb', '_fbp', 'fbc',
    '_uetsid', '_uetvid', '_uetmsclkid', '_epik', // Add marketing cookies
];
```

---

## 🆘 Getting Help

If you encounter issues during migration:

### 1. Check Configuration

-   Ensure all required config sections are present
-   Verify platform enable/disable settings
-   Check for typos in configuration keys

### 2. Review Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

### 3. Test Incrementally

-   Enable one feature at a time
-   Test each feature individually
-   Isolate issues by disabling problematic features

### 4. Fallback Strategy

If you encounter serious issues:

```php
// Disable all new features temporarily
'platforms' => [], // Empty array disables platform system
'click_id_management' => ['enabled' => false],
'events' => ['enabled' => false],
'observers' => ['enabled' => false],
'conversions' => ['enabled' => false],
'ecommerce' => ['enabled' => false],
```

### 5. AI-Assisted Migration with Claude

For complex migration scenarios, you can use Claude AI to help with the upgrade process. Use this prompt:

**Claude Prompt for MarketingDataTracker v1 to v2 Migration:**

```
I'm upgrading MarketingDataTracker from v1 to v2 and need help with the configuration migration. Here's my current v1 configuration:

[Paste your current config/marketing-data-tracker.php here]

Please help me:
1. Migrate my existing configuration to v2 format
2. Identify any missing configuration sections for v2 features
3. Suggest optimal platform configurations based on my current setup
4. Recommend click ID management settings
5. Configure event system for my use case
6. Set up cookie management and consent handling
7. Provide a step-by-step migration checklist

I'm particularly interested in:
- [Describe your specific requirements, e.g., "Google Ads tracking", "Multi-platform attribution", "E-commerce conversion tracking"]
- [Mention any custom parameters or cookies you use]
- [Note any specific issues you're experiencing]

My Laravel version is [X.X] and I use the following related packages:
- [List any relevant packages like Laravel Nova, analytics packages, etc.]
```

### 6. Community Support

-   Check the [GitHub Issues](https://github.com/marshmallow-packages/marketing-data-tracker/issues)
-   Contact support at support@marshmallow.dev

---

## 📊 What's New in v2

### Core Enhancements

-   **12+ Platform Support**: Google Ads, Meta, Microsoft, LinkedIn, Twitter, Pinterest, TikTok, Reddit, Snapchat, Amazon, TradeTracker, Email Marketing
-   **Priority-Based Click ID Management**: Configurable priority system (gclid > fbclid > msclkid, etc.)
-   **Wildcard Pattern Matching**: Support for `_ga*`, `utm_*`, `_gcl*` patterns
-   **Event-Driven Architecture**: Complete event system for analytics integration
-   **Marketing URL Builder**: Fluent URL construction with platform templates
-   **Cookie Management**: Advanced cookie tracking with consent management
-   **Conversion Tracking**: Revenue attribution and conversion funnel tracking
-   **E-commerce Events**: GTM-compatible product and transaction tracking
-   **Automatic Observation**: Auto-capture UTM data on model creation

### Technical Improvements

-   **New Services**: `PlatformManager`, `CookieManager`, `MarketingUrlBuilder`
-   **New Events**: `MarketingDataCreated`, `ConversionTracked`, `ClickIdDetected`
-   **New Traits**: `TracksConversions`, `TracksEcommerceEvents`
-   **Enhanced Configuration**: Platform-specific settings with individual controls
-   **Comprehensive Testing**: Full test suite with 90%+ coverage

### Backward Compatibility

-   **100% Compatible**: All v1 functionality preserved
-   **Opt-In Features**: New features disabled by default
-   **No Breaking Changes**: Existing code works without modification
-   **Database Compatible**: No schema changes required

---

**🎉 Congratulations! You've successfully upgraded to MarketingDataTracker v2.0 with enterprise-grade marketing attribution capabilities.**
