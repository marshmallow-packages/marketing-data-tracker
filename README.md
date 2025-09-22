# 🎯 Laravel Marketing Data Tracker

### Comprehensive Marketing Attribution & UTM Parameter Tracking for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marshmallow/marketing-data-tracker.svg?style=flat-square)](https://packagist.org/packages/marshmallow/marketing-data-tracker)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/marshmallow-packages/marketing-data-tracker/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/marshmallow-packages/marketing-data-tracker/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/marshmallow/marketing-data-tracker.svg?style=flat-square)](https://packagist.org/packages/marshmallow/marketing-data-tracker)

**Track, store, and analyze marketing attribution data from Google Ads, Facebook, TikTok, LinkedIn, and 10+ other advertising platforms in your Laravel application.** Automatically capture UTM parameters, click IDs, and conversion data to understand your marketing performance and ROI.

Perfect for e-commerce sites, SaaS applications, and any Laravel project that needs comprehensive marketing attribution tracking.

---

## ✨ Features

🚀 **Multi-Platform Support** - Google Ads, Facebook/Meta, TikTok, LinkedIn, Twitter, Pinterest, Microsoft Ads, Reddit, Snapchat, and more

🎯 **Intelligent Click ID Tracking** - Automatically detect and prioritize `gclid`, `fbclid`, `ttclid`, `msclkid`, and other platform-specific identifiers

📊 **UTM Parameter Management** - Complete UTM tracking with automatic parameter extraction and storage

🔄 **Conversion Attribution** - Link conversions back to their original marketing source with full attribution data

🍪 **Advanced Cookie Support** - Track 60+ marketing cookies across all major advertising platforms

⚡ **Zero Configuration** - Works out of the box with sensible defaults, fully customizable

🛡️ **Privacy Compliant** - Built-in GDPR support with consent management and data export

📱 **Mobile Attribution** - iOS 14.5+ support with `gbraid` and `wbraid` tracking

🎨 **Laravel Nova Integration** - Beautiful admin interface for viewing marketing data

---

## 🚀 Quick Start

### Installation

Install via Composer:

```bash
composer require marshmallow/marketing-data-tracker
```

Publish and run migrations:

```bash
php artisan vendor:publish --tag="marketing-data-tracker-migrations"
php artisan migrate
```

### Basic Setup

**1. Add the trait to your models:**

```php
use Marshmallow\MarketingData\Traits\HasMarketingParameters;

class User extends Model
{
    use HasMarketingParameters;

    // Your model code...
}
```

**2. Add the middleware to your web routes:**

In `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \Marshmallow\MarketingData\Middleware\ParseMarketingParameters::class,
    ],
];
```

**3. Track marketing data on user registration:**

```php
// Automatically capture and store marketing data
$user = User::create($userData);
$user->setUtmSourceData(); // Captures all marketing parameters from session

// Access marketing data
echo $user->utm_source;     // "google"
echo $user->utm_campaign;   // "summer-sale-2024"
echo $user->primary_click_id; // "gclid_abc123..."
echo $user->platform_name;  // "Google Ads"
```

That's it! 🎉 The package will now automatically track marketing parameters from all major advertising platforms.

---

## 📖 Platform Support

### Enhanced Multi-Platform System

The package now features a comprehensive platform management system supporting **12+ advertising platforms** with configurable parameters, cookies, and tracking templates.

#### Supported Advertising Platforms

| Platform | Click ID | UTM Source | Custom Parameters | Status |
|----------|----------|------------|-------------------|---------|
| **Google Ads** | `gclid`, `gbraid`, `wbraid` | `google` | 20+ ValueTrack parameters | ✅ Enhanced |
| **Meta/Facebook** | `fbclid` | `facebook`, `meta` | Dynamic campaign parameters | ✅ Enhanced |
| **Microsoft Ads** | `msclkid` | `bing`, `microsoft` | UET tracking parameters | ✅ Enhanced |
| **LinkedIn Ads** | `li_fat_id` | `linkedin` | Professional network parameters | ✅ Enhanced |
| **Twitter/X Ads** | `twclid` | `twitter`, `x` | Social media parameters | ✅ Enhanced |
| **Pinterest Ads** | `epik` | `pinterest` | Visual platform parameters | ✅ Enhanced |
| **TikTok Ads** | `ttclid` | `tiktok` | Short-form video parameters | ✅ Enhanced |
| **Reddit Ads** | `rdt_cid` | `reddit` | Community platform parameters | ✅ Enhanced |
| **Snapchat Ads** | `sscid` | `snapchat` | Mobile-first parameters | ✅ Enhanced |
| **Amazon DSP** | `maas` | `amazon` | E-commerce advertising | ✅ Enhanced |
| **TradeTracker** | `ttid` | `tradetracker` | Affiliate network tracking | ✅ New |
| **Email Marketing** | `mc_cid`, `mc_eid` | `email` | Campaign tracking | ✅ New |

#### Platform Configuration

Each platform can be individually enabled/disabled and configured:

```php
// config/marketing-data-tracker.php
'platforms' => [
    'google_ads' => [
        'enabled' => true,
        'name' => 'Google Ads',
        'click_id_params' => ['gclid', 'gbraid', 'wbraid'],
        'click_id_cookies' => ['_gcl_aw', '_gcl_gb', '_gcl_ag'],
        'parameters' => [
            'gclid', 'gbraid', 'wbraid', 'gad_source', 'mm_campaignid',
            'mm_adgroupid', 'mm_keyword', 'mm_matchtype', 'mm_network',
            'mm_device', 'mm_placement', // ... and 15+ more
        ],
        'cookies' => ['_ga', '_gid', '_gcl_*', '_ga*'], // Supports wildcards
    ],
    // ... more platforms
],
```

### Automatic Parameter Detection

The package automatically detects and stores **175+ marketing parameters**:

- **Standard UTM**: `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`
- **Google Ads**: `gclid`, `gbraid`, `wbraid`, `mm_campaignid`, `mm_adgroupid`, `mm_keyword`, etc.
- **Facebook**: `fbclid`, `campaignid`, `adsetid`, `adid`, `placement`, `site_source`
- **Email Marketing**: `mc_cid` (Mailchimp), `utm_email`, `list_id`
- **Affiliate Programs**: `ref`, `affiliate_id`, `partner_id`

---

## 🎯 Usage Examples

### Basic Marketing Attribution

```php
// Create user and automatically capture marketing data
$user = User::create($userData);
$user->setUtmSourceData();

// Access marketing information
echo $user->marketing_source;        // "Google"
echo $user->utm_source_medium;       // "Google - Cpc"
echo $user->utm_campaign_term;       // "Summer Sale - running shoes"
```

### Advanced Click ID Management

The package now features **priority-based click ID detection** with configurable extraction and cookie support:

```php
// Get the highest priority click ID from any platform
$clickId = $user->primary_click_id;  // Automatically prioritizes gclid > fbclid > ttclid

// Get Google-specific click ID with priority handling
$googleClickId = $user->getPrimaryGoogleClickId(); // gclid > wbraid > gbraid

// Check specific platform attribution
if ($user->has_google_id) {
    $googleIds = $user->google_ids;   // ['gclid' => '...', 'gbraid' => '...']
}

// Platform detection
$platform = $user->platform_name;    // "Google Ads", "Meta/Facebook", "TikTok"
$platformKey = $user->detectPlatformFromMarketingData(); // "google_ads", "meta", "tiktok"
```

#### Click ID Priority Configuration

```php
// config/marketing-data-tracker.php
'click_id_management' => [
    'enabled' => true,
    'google_click_ids' => [
        'enabled' => true,
        'priority' => ['gclid', 'wbraid', 'gbraid'],
        'cookie_mapping' => [
            'gclid' => '_gcl_aw',
            'wbraid' => '_gcl_gb',
            'gbraid' => '_gcl_ag',
        ],
        'extract_gclid_value' => true, // Extract clean ID from cookie format
    ],
    'platform_priority' => [
        'gclid' => 10,    // Google Ads - highest priority
        'fbclid' => 9,    // Facebook/Meta
        'msclkid' => 8,   // Microsoft/Bing
        'ttclid' => 7,    // TikTok
        // ... more platforms
    ],
],
```

### E-commerce Conversion Tracking

Enhanced e-commerce tracking with **Google Tag Manager format** and **multiple platform support**:

```php
use Marshmallow\MarketingData\Traits\TracksEcommerceEvents;
use Marshmallow\MarketingData\Traits\TracksConversions;

class Order extends Model
{
    use HasMarketingParameters, TracksEcommerceEvents, TracksConversions;
}

// Track purchase conversion with attribution
$order = Order::create($orderData);
$order->setUtmSourceData();
$order->trackPurchase('TXN-123', $products, 299.99, 'EUR');

// Track other e-commerce events
$product->trackViewItem();
$product->trackAddToCart(2); // quantity
$order->trackBeginCheckout($items, $total);

// Track conversions with attribution
$lead->trackLeadConversion(50.00);
$user->trackSignupConversion();
$subscription->trackSubscriptionConversion(99.99, 'USD');
```

#### E-commerce Configuration

```php
// config/marketing-data-tracker.php
'ecommerce' => [
    'enabled' => true,
    'currency' => 'EUR',
    'events' => [
        'view_item' => true,
        'add_to_cart' => true,
        'purchase' => true,
    ],
    'gtm_format' => true,
    'platform_formats' => [
        'google_ads' => true,
        'meta' => true,
    ],
],

'conversions' => [
    'enabled' => true,
    'types' => [
        'lead' => ['value' => null, 'priority' => 1],
        'purchase' => ['value' => null, 'priority' => 5],
    ],
    'auto_track' => false,
    'track_value' => true,
],
```

### Campaign Performance Analysis

```php
// Get all users from Google Ads campaigns
$googleUsers = User::whereNotNull('gclid')->get();

// Group by campaign
$campaignPerformance = User::select('utm_campaign')
    ->selectRaw('COUNT(*) as users, SUM(conversion_value) as revenue')
    ->groupBy('utm_campaign')
    ->get();

// Platform breakdown
$platformStats = User::selectRaw('
    CASE
        WHEN gclid IS NOT NULL THEN "Google Ads"
        WHEN fbclid IS NOT NULL THEN "Facebook"
        WHEN ttclid IS NOT NULL THEN "TikTok"
        ELSE "Other"
    END as platform,
    COUNT(*) as count
')->groupBy('platform')->get();
```

---

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="marketing-data-tracker-config"
```

### Platform-Specific Configuration

```php
// config/marketing-data-tracker.php

'platforms' => [
    'google_ads' => [
        'click_id_params' => ['gclid', 'wbraid', 'gbraid'],
        'click_id_cookies' => ['_gcl_aw', '_gcl_gb', '_gcl_ag'],
        'custom_params' => [
            'mm_campaignid' => '{campaignid}',
            'mm_keyword' => '{keyword}',
            // ... more ValueTrack parameters
        ],
    ],
    'meta' => [
        'click_id_params' => ['fbclid'],
        'click_id_cookies' => ['_fbp', 'fbc'],
        'custom_params' => [
            'mm_campaignid' => '{{campaign.id}}',
            'mm_placement' => '{{placement}}',
            // ... more dynamic parameters
        ],
    ],
    // ... other platforms
],
```

### Cookie Consent Management

```php
'cookie_consent' => [
    'enabled' => true,
    'default_consent' => [
        'functional' => true,
        'analytics' => false,
        'advertising' => false,
    ],
],
```

---

## 🔧 Advanced Features

### Marketing URL Builder

**NEW**: Fluent URL builder with platform-specific templates and ValueTrack parameter support:

```php
use Marshmallow\MarketingData\Builders\MarketingUrlBuilder;

// Build Google Ads URLs with ValueTrack parameters
$url = MarketingUrlBuilder::googleAds('https://example.com', 'summer-sale')
    ->withGoogleValueTrack()
    ->build();
// Result: https://example.com?utm_source=google&utm_medium=cpc&gclid={gclid}&mm_campaignid={campaignid}...

// Build Meta/Facebook Ads URLs with dynamic parameters
$url = MarketingUrlBuilder::metaAds('https://example.com', 'awareness-campaign')
    ->withMetaDynamicParams()
    ->build();
// Result: https://example.com?utm_source=facebook&fbclid={{fbclid}}&mm_campaignid={{campaign.id}}...

// Build Microsoft Ads URLs
$url = MarketingUrlBuilder::microsoftAds('https://example.com', 'search-campaign')
    ->build();

// Build LinkedIn Ads URLs
$url = MarketingUrlBuilder::linkedInAds('https://example.com', 'b2b-campaign')
    ->build();

// Custom UTM URLs
$url = MarketingUrlBuilder::utm(
    'https://example.com',
    'newsletter',
    'email',
    'weekly-digest'
);

// Advanced usage with custom parameters
$url = MarketingUrlBuilder::make('https://example.com')
    ->withUTM('google', 'cpc', 'summer-sale')
    ->withPlatform('google_ads', ['mm_keyword' => 'running shoes'])
    ->withCustomParams(['custom_param' => 'value'])
    ->build();

// Use tracking templates from config
$url = MarketingUrlBuilder::fromTemplate(
    'https://example.com',
    'google_ads',
    ['campaignid' => '12345', 'keyword' => 'shoes']
);
```

#### URL Builder Features

- ✅ **Platform Templates**: Pre-configured URLs for Google Ads, Meta, Microsoft, LinkedIn, Twitter, TikTok, Pinterest
- ✅ **ValueTrack Support**: Full Google Ads ValueTrack parameter integration
- ✅ **Dynamic Parameters**: Meta/Facebook dynamic parameter placeholders
- ✅ **Fluent Interface**: Chainable methods for easy URL construction
- ✅ **Parameter Filtering**: Automatic filtering of empty/null values
- ✅ **Template System**: Use tracking URL templates from configuration

### Event-Driven Architecture

**NEW**: Comprehensive event system for marketing data lifecycle tracking:

```php
use Marshmallow\MarketingData\Events\MarketingDataCreated;
use Marshmallow\MarketingData\Events\MarketingDataUpdated;
use Marshmallow\MarketingData\Events\ConversionTracked;
use Marshmallow\MarketingData\Events\ClickIdDetected;

// Listen to marketing data creation
Event::listen(MarketingDataCreated::class, function ($event) {
    // Send to analytics when marketing data is first captured
    Analytics::trackAcquisition($event->model, $event->getAttributionData());
});

// Listen to click ID detection
Event::listen(ClickIdDetected::class, function ($event) {
    // Store click ID for server-side conversion tracking
    if ($event->isGoogleClickId()) {
        Redis::setex("gclid:{$event->clickId}", 3600, $event->getSessionData());
    }
});

// Listen to conversions
Event::listen(ConversionTracked::class, function ($event) {
    // Send conversion data to multiple platforms
    if ($event->hasValue()) {
        GoogleAds::trackConversion($event->getRevenueAttribution());
        FacebookConversions::track($event->getConversionData());
    }
});

// Listen to marketing data updates
Event::listen(MarketingDataUpdated::class, function ($event) {
    if ($event->hasClickIdChanges()) {
        // Handle click ID changes
        logger()->info('Click ID changed', $event->getClickIdChanges());
    }
});
```

#### Event Configuration

```php
// config/marketing-data-tracker.php
'events' => [
    'enabled' => true,
    'listeners' => [
        'Marshmallow\\MarketingData\\Events\\ConversionTracked' => [
            'App\\Listeners\\SendToGoogleAds',
            'App\\Listeners\\SendToFacebookConversions',
        ],
    ],
],
```

### Laravel Nova Integration

```php
use Marshmallow\MarketingData\Nova\Traits\MarketingDataFields;

class UserResource extends Resource
{
    use MarketingDataFields;

    public function fields(Request $request)
    {
        return [
            // ... your fields

            ...$this->getMarketingDataFields(
                with_utm_data: true,
                with_google_ids: true,
                with_all_data: false,
            ),
        ];
    }
}
```

---

## 🎨 Platform-Specific Setup

### Google Ads Configuration

Add to your Google Ads account-level tracking template:

```text
?utm_source=google&utm_medium=cpc&utm_campaign={_campaign}&utm_term={keyword}&utm_content={creative}&mm_campaignid={campaignid}&mm_adgroupid={adgroupid}&mm_keyword={keyword}&mm_matchtype={matchtype}&mm_network={network}&mm_device={device}&mm_placement={placement}&gclid={gclid}&gbraid={gbraid}&wbraid={wbraid}
```

### Facebook Ads Configuration

Use Facebook's URL parameters in your ads:

```text
?utm_source=facebook&utm_medium=paid&utm_campaign={{campaign.name}}&utm_content={{ad.name}}&utm_term={{adset.name}}&fbclid={{fbclid}}
```

### Server-Side Tracking Setup

For accurate attribution, configure server-side tracking:

```php
// Track conversions server-side
$user->trackConversion('purchase', 99.99, 'USD', [
    'order_id' => 'ORD-123',
    'products' => $products,
]);
```

---

## 🏗️ Architecture

### Database Schema

The package creates a flexible marketing data storage system:

```sql
-- marketing_data stored as JSON in your existing models
{
    "utm_source": "google",
    "utm_campaign": "summer-sale",
    "gclid": "Cj0KCQiA...",
    "mm_keyword": "running shoes",
    "platform": "google_ads"
}
```

### Privacy & GDPR Compliance

- **Consent Management**: Built-in cookie consent handling
- **Data Anonymization**: Automatic PII detection and masking
- **Data Export**: GDPR-compliant data export functionality
- **Retention Policies**: Configurable data retention periods

---

## 📊 Analytics Integration

### Google Analytics 4

```php
// Send conversion data to GA4
$user->trackConversion('purchase', 199.99);
// Automatically includes marketing attribution data
```

### Facebook Conversions API

```php
// Track server-side Facebook conversions
$user->trackFacebookConversion('Purchase', 199.99, [
    'currency' => 'USD',
    'contents' => $products,
]);
```

### Custom Analytics Platforms

```php
// Send to any analytics platform
Event::listen(ConversionTracked::class, function ($event) {
    CustomAnalytics::track([
        'event' => $event->conversionType,
        'value' => $event->conversionValue,
        'attribution' => $event->getAttributionData(),
    ]);
});
```

---

## 🧪 Testing

```bash
# Run the test suite
composer test

# Run with coverage
composer test:coverage
```

### Testing Marketing Attribution

```php
// Test marketing data capture
$this->get('/?utm_source=google&utm_campaign=test&gclid=123');

$user = User::factory()->create();
$user->setUtmSourceData();

$this->assertEquals('google', $user->utm_source);
$this->assertEquals('123', $user->gclid);
```

---

## 🚀 Performance

- **Minimal Database Impact**: Efficient JSON storage with indexing
- **Lazy Loading**: Marketing data loaded only when needed
- **Caching**: Built-in caching for frequently accessed data
- **Bulk Processing**: Handle high-traffic scenarios efficiently

### Optimization Tips

```php
// Cache marketing data for high-traffic sites
$user->cacheMarketingData();

// Batch process marketing data
MarketingDataTracker::batchProcess($users);
```

---

## 🛠️ New Advanced Features

### Automatic Model Observation

**NEW**: Automatic UTM data capture and click ID detection:

```php
// config/marketing-data-tracker.php
'observers' => [
    'enabled' => true,
    'models' => [
        App\Models\Lead::class,
        App\Models\Order::class,
    ],
    'auto_set_utm' => true,
    'auto_detect_click_ids' => true,
    'forget_after_save' => true,
],
```

### Cookie Management System

**NEW**: Advanced cookie tracking with consent management:

```php
use Marshmallow\MarketingData\Services\CookieManager;

$cookieManager = app(CookieManager::class);

// Get trackable cookies with consent filtering
$cookies = $cookieManager->getCookieValues($request);

// Check consent for specific groups
if ($cookieManager->isTrackingAllowed('advertising')) {
    // Track advertising cookies
}

// Wildcard cookie matching
$matches = $cookieManager->matchWildcardCookies(
    ['_ga_123', '_ga_456', '_fbp'],
    ['_ga*']
); // Returns: ['_ga_123', '_ga_456']
```

### Platform Management Service

**NEW**: Centralized platform configuration management:

```php
use Marshmallow\MarketingData\Services\PlatformManager;

$platformManager = app(PlatformManager::class);

// Get enabled platforms
$platforms = $platformManager->getEnabledPlatforms();

// Get platform-specific parameters
$googleParams = $platformManager->getPlatformParameters('google_ads');

// Get all tracking parameters across platforms
$allParams = $platformManager->getAllTrackingParameters();

// Wildcard pattern matching
$matches = $platformManager->matchWildcardPatterns(
    ['utm_source', 'utm_campaign', 'gclid'],
    ['utm_*']
); // Returns: ['utm_source', 'utm_campaign']
```

---

## 📚 API Reference

### Enhanced HasMarketingParameters Trait

| Method | Description | Example |
|--------|-------------|---------|
| `setUtmSourceData()` | Capture marketing parameters | `$user->setUtmSourceData()` |
| `getPrimaryGoogleClickId()` | **NEW**: Get Google click ID with priority | `$user->getPrimaryGoogleClickId()` |
| `primary_click_id` | Get highest priority click ID | `$user->primary_click_id` |
| `platform_name` | Get platform name | `$user->platform_name` |
| `detectPlatformFromMarketingData()` | Detect advertising platform | `$user->detectPlatformFromMarketingData()` |
| `marketing_parameter_list` | Get formatted parameters | `$user->marketing_parameter_list` |

### MarketingUrlBuilder

| Method | Description | Example |
|--------|-------------|---------|
| `googleAds()` | Build Google Ads URL | `MarketingUrlBuilder::googleAds($url, $campaign)` |
| `metaAds()` | Build Facebook Ads URL | `MarketingUrlBuilder::metaAds($url, $campaign)` |
| `microsoftAds()` | **NEW**: Build Microsoft Ads URL | `MarketingUrlBuilder::microsoftAds($url, $campaign)` |
| `linkedInAds()` | **NEW**: Build LinkedIn Ads URL | `MarketingUrlBuilder::linkedInAds($url, $campaign)` |
| `withUTM()` | Add UTM parameters | `$builder->withUTM($params)` |
| `withGoogleValueTrack()` | **NEW**: Add ValueTrack parameters | `$builder->withGoogleValueTrack()` |
| `withPlatform()` | **NEW**: Add platform-specific params | `$builder->withPlatform('google_ads', $params)` |

### New Services & Events

| Service/Event | Description | Example |
|---------------|-------------|---------|
| `PlatformManager` | **NEW**: Platform configuration management | `$manager->getEnabledPlatforms()` |
| `CookieManager` | **NEW**: Cookie tracking with consent | `$manager->getCookieValues($request)` |
| `MarketingDataCreated` | **NEW**: Marketing data creation event | `Event::listen(MarketingDataCreated::class, ...)` |
| `ConversionTracked` | **NEW**: Conversion tracking event | `Event::listen(ConversionTracked::class, ...)` |
| `ClickIdDetected` | **NEW**: Click ID detection event | `Event::listen(ClickIdDetected::class, ...)` |

### New Traits

| Trait | Description | Example |
|-------|-------------|---------|
| `TracksConversions` | **NEW**: Conversion tracking functionality | `$model->trackLeadConversion(50.00)` |
| `TracksEcommerceEvents` | **NEW**: E-commerce event tracking | `$product->trackViewItem()` |

---

## 🔄 Upgrading from v1 to v2

### Migration Overview

Version 2.0 introduces **major new features** while maintaining **100% backward compatibility**. Your existing code will continue to work without any changes, but you can opt-in to enhanced features for improved functionality.

### Step 1: Update the Package

```bash
composer update marshmallow/marketing-data-tracker
```

### Step 2: Publish New Configuration (Optional)

To access new features, publish the updated configuration:

```bash
php artisan vendor:publish --tag="marketing-data-tracker-config" --force
```

⚠️ **Important**: This will overwrite your existing config. Back up your current configuration first if you have custom settings.

### Step 3: Enable New Features (Optional)

The new configuration includes several opt-in features. Enable them as needed:

#### Enable Enhanced Platform Support

```php
// config/marketing-data-tracker.php
'platforms' => [
    'google_ads' => ['enabled' => true],
    'meta' => ['enabled' => true],
    'microsoft' => ['enabled' => true],
    'linkedin' => ['enabled' => true],
    'twitter' => ['enabled' => true],
    'pinterest' => ['enabled' => true],
    'tiktok' => ['enabled' => true],
    // ... enable platforms you use
],
```

#### Enable Click ID Management

```php
'click_id_management' => [
    'enabled' => true,
    'google_click_ids' => [
        'enabled' => true,
        'extract_gclid_value' => true, // Clean extraction from cookie format
    ],
],
```

#### Enable Events (for Analytics Integration)

```php
'events' => [
    'enabled' => true,
    'listeners' => [
        // Add your custom event listeners here
    ],
],
```

#### Enable Auto-Observation (Advanced)

```php
'observers' => [
    'enabled' => true, // Only enable if you want automatic UTM capture
    'models' => [
        App\Models\Lead::class,
        App\Models\Order::class,
        // Add models that should auto-capture marketing data
    ],
    'auto_set_utm' => true,
    'auto_detect_click_ids' => true,
],
```

#### Enable Conversion Tracking

```php
'conversions' => [
    'enabled' => true,
    'types' => [
        'lead' => ['value' => null, 'priority' => 1],
        'purchase' => ['value' => null, 'priority' => 5],
        // Add your conversion types
    ],
],
```

#### Enable E-commerce Tracking

```php
'ecommerce' => [
    'enabled' => true,
    'currency' => 'EUR', // Your default currency
    'events' => [
        'view_item' => true,
        'add_to_cart' => true,
        'purchase' => true,
    ],
],
```

### Step 4: Update Your Code (Optional)

Your existing code will work without changes, but you can enhance it with new features:

#### Enhanced Click ID Access

```php
// v1 - Still works
$clickId = $user->primary_click_id;

// v2 - Enhanced with priority and extraction
$googleClickId = $user->getPrimaryGoogleClickId(); // gclid > wbraid > gbraid
```

#### Add New Traits for Enhanced Functionality

```php
use Marshmallow\MarketingData\Traits\HasMarketingParameters;
use Marshmallow\MarketingData\Traits\TracksConversions;
use Marshmallow\MarketingData\Traits\TracksEcommerceEvents;

class Order extends Model
{
    use HasMarketingParameters, TracksConversions, TracksEcommerceEvents;

    // Your existing code remains unchanged
}

// New functionality available
$order->trackPurchase('TXN-123', $products, 299.99, 'EUR');
$order->trackConversion('purchase', 299.99);
```

#### Use New URL Builder

```php
use Marshmallow\MarketingData\Builders\MarketingUrlBuilder;

// Build campaign URLs with platform-specific parameters
$url = MarketingUrlBuilder::googleAds('https://example.com', 'summer-sale')
    ->withGoogleValueTrack()
    ->build();

// Or use the fluent interface
$url = MarketingUrlBuilder::make('https://example.com')
    ->withUTM('google', 'cpc', 'campaign')
    ->withPlatform('google_ads', ['mm_keyword' => 'shoes'])
    ->build();
```

#### Implement Event Listeners

```php
// app/Providers/EventServiceProvider.php
use Marshmallow\MarketingData\Events\ConversionTracked;
use Marshmallow\MarketingData\Events\ClickIdDetected;

protected $listen = [
    ConversionTracked::class => [
        App\Listeners\SendConversionToAnalytics::class,
    ],
    ClickIdDetected::class => [
        App\Listeners\StoreClickIdForServerSideTracking::class,
    ],
];
```

### Step 5: Test Your Implementation

After upgrading, verify everything works correctly:

#### Test Existing Functionality

```php
// Ensure your existing UTM tracking still works
$this->get('/?utm_source=google&utm_campaign=test&gclid=123');

$user = User::factory()->create();
$user->setUtmSourceData();

$this->assertEquals('google', $user->utm_source);
$this->assertEquals('123', $user->gclid);
```

#### Test New Click ID Priority

```php
$user = new User();
$user->setUtmSourceData(); // Assume session has gclid, wbraid, gbraid

// Should return gclid (highest priority)
$primaryGoogle = $user->getPrimaryGoogleClickId();

// Should return highest priority overall
$primaryAny = $user->primary_click_id;
```

#### Test Platform Detection

```php
$user->gclid = 'test_gclid';
$platform = $user->platform_name; // Should return "Google Ads"

$user->fbclid = 'test_fbclid';
$platform = $user->platform_name; // Should return "Meta/Facebook"
```

### Migration Checklist

- [ ] **Backup Configuration**: Save your current `config/marketing-data-tracker.php`
- [ ] **Update Package**: Run `composer update marshmallow/marketing-data-tracker`
- [ ] **Publish Config**: Run publish command if you want new features
- [ ] **Enable Platforms**: Configure the platforms you use
- [ ] **Enable Features**: Turn on click ID management, events, etc. as needed
- [ ] **Update Models**: Add new traits if you want conversion/e-commerce tracking
- [ ] **Test Functionality**: Verify existing UTM tracking still works
- [ ] **Test New Features**: Verify new click ID priority and platform detection
- [ ] **Monitor Events**: Check that events fire correctly if enabled
- [ ] **Performance Test**: Ensure no performance degradation

### Common Migration Issues

#### Issue: Configuration Override
**Problem**: New config overwrites custom settings
**Solution**: Merge your custom settings with the new configuration structure

#### Issue: Events Not Firing
**Problem**: Events enabled but listeners not receiving them
**Solution**: Ensure `'events' => ['enabled' => true]` and check listener registration

#### Issue: Platform Not Detected
**Problem**: Platform detection returns null
**Solution**: Ensure the platform is enabled in the new `platforms` configuration

#### Issue: Click IDs Not Prioritized
**Problem**: `getPrimaryGoogleClickId()` returns null
**Solution**: Enable click ID management: `'click_id_management' => ['enabled' => true]`

### Getting Help

If you encounter issues during migration:

1. **Check Configuration**: Ensure new config sections are properly configured
2. **Review Logs**: Look for error messages in Laravel logs
3. **Test Incrementally**: Enable one feature at a time to isolate issues
4. **Fallback**: Disable new features if needed - v1 functionality will still work

### What Stays the Same

✅ **All existing methods** work exactly as before
✅ **Database structure** remains unchanged
✅ **UTM parameter capture** works identically
✅ **Laravel Nova integration** continues to work
✅ **Basic click ID detection** functions as before

### What Gets Better

🚀 **Enhanced click ID priority** with configurable extraction
🚀 **12+ platform support** with individual controls
🚀 **Advanced cookie tracking** with consent management
🚀 **Event-driven architecture** for analytics integration
🚀 **Conversion & e-commerce tracking** with attribution
🚀 **URL builder** for campaign management
🚀 **Wildcard pattern matching** for flexible parameter capture

---

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Setup

```bash
git clone https://github.com/marshmallow-packages/marketing-data-tracker
cd marketing-data-tracker
composer install
composer test
```

---

## 📄 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## 🔒 Security

If you discover any security-related issues, please email security@marshmallow.dev instead of using the issue tracker.

---

## 💡 Use Cases

### E-commerce Attribution
Track customer acquisition costs, measure ROAS, and optimize ad spend across platforms.

### SaaS Conversion Tracking
Monitor trial-to-paid conversion rates by marketing channel and campaign.

### Lead Generation
Attribute leads to their original marketing source for accurate ROI calculation.

### Content Marketing
Track which content pieces drive the most valuable conversions.

### Multi-Touch Attribution
Understand the complete customer journey across multiple touchpoints.

---

## 🏆 Why Choose This Package?

✅ **Battle-Tested** - Used in production by hundreds of Laravel applications

✅ **Comprehensive** - Supports more platforms than any other Laravel marketing package

✅ **Developer Friendly** - Clean API, excellent documentation, and Laravel conventions

✅ **Performance Optimized** - Minimal overhead with intelligent caching

✅ **Privacy Compliant** - Built-in GDPR support and consent management

✅ **Actively Maintained** - Regular updates and new platform support

---

## 📞 Support

- **Documentation**: [Full documentation](https://docs.marshmallow.dev/marketing-data-tracker)
- **Issues**: [GitHub Issues](https://github.com/marshmallow-packages/marketing-data-tracker/issues)
- **Discussions**: [GitHub Discussions](https://github.com/marshmallow-packages/marketing-data-tracker/discussions)
- **Email**: [support@marshmallow.dev](mailto:support@marshmallow.dev)

---

## 📜 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## 👏 Credits

- **[Marshmallow](https://github.com/marshmallow-packages)** - Package development and maintenance
- **[All Contributors](../../contributors)** - Community contributions and feedback

---

**Made with ❤️ by the Marshmallow team**

*Star ⭐ this repo if you find it helpful!*