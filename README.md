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

### Supported Advertising Platforms

| Platform | Click ID | UTM Source | Custom Parameters |
|----------|----------|------------|-------------------|
| **Google Ads** | `gclid`, `gbraid`, `wbraid` | `google` | ValueTrack parameters |
| **Facebook/Meta** | `fbclid` | `facebook`, `meta` | Dynamic parameters |
| **TikTok Ads** | `ttclid` | `tiktok` | Campaign parameters |
| **Microsoft Ads** | `msclkid` | `bing`, `microsoft` | UET parameters |
| **LinkedIn Ads** | `li_fat_id` | `linkedin` | Sponsored content parameters |
| **Twitter/X Ads** | `twclid` | `twitter`, `x` | Promoted content parameters |
| **Pinterest Ads** | `epik` | `pinterest` | Shopping parameters |
| **Reddit Ads** | `rdt_cid` | `reddit` | Promoted posts parameters |
| **Snapchat Ads** | `sscid` | `snapchat` | Story ads parameters |
| **Amazon DSP** | — | `amazon` | DSP parameters |

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

```php
// Get the highest priority click ID from any platform
$clickId = $user->primary_click_id;  // Automatically prioritizes gclid > fbclid > ttclid

// Check specific platform attribution
if ($user->has_google_id) {
    $googleIds = $user->google_ids;   // ['gclid' => '...', 'gbraid' => '...']
}

// Platform detection
$platform = $user->platform_name;    // "Google Ads", "Meta/Facebook", "TikTok"
$platformKey = $user->detectPlatformFromMarketingData(); // "google_ads", "meta", "tiktok"
```

### E-commerce Conversion Tracking

```php
use Marshmallow\MarketingData\Traits\TracksEcommerceEvents;

class Order extends Model
{
    use HasMarketingParameters, TracksEcommerceEvents;
}

// Track purchase conversion with attribution
$order = Order::create($orderData);
$order->setUtmSourceData();
$order->trackPurchase('TXN-123', $products, 299.99);
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

### URL Builder for Marketing Campaigns

```php
use Marshmallow\MarketingData\Builders\MarketingUrlBuilder;

// Build Google Ads URLs with ValueTrack parameters
$url = MarketingUrlBuilder::googleAds('https://example.com', 'summer-sale')
    ->withGoogleValueTrack()
    ->build();

// Build Facebook Ads URLs
$url = MarketingUrlBuilder::metaAds('https://example.com', 'awareness-campaign')
    ->withMetaDynamicParams()
    ->build();

// Custom UTM URLs
$url = MarketingUrlBuilder::utm(
    'https://example.com',
    'newsletter',
    'email',
    'weekly-digest'
);
```

### Event-Driven Architecture

```php
use Marshmallow\MarketingData\Events\ConversionTracked;

// Listen to marketing events
Event::listen(ConversionTracked::class, function ($event) {
    // Send conversion data to analytics platforms
    Analytics::track($event->model, $event->conversionType, $event->conversionValue);
});
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

## 📚 API Reference

### HasMarketingParameters Trait

| Method | Description | Example |
|--------|-------------|---------|
| `setUtmSourceData()` | Capture marketing parameters | `$user->setUtmSourceData()` |
| `primary_click_id` | Get highest priority click ID | `$user->primary_click_id` |
| `platform_name` | Get platform name | `$user->platform_name` |
| `detectPlatformFromMarketingData()` | Detect advertising platform | `$user->detectPlatformFromMarketingData()` |
| `marketing_parameter_list` | Get formatted parameters | `$user->marketing_parameter_list` |

### MarketingUrlBuilder

| Method | Description | Example |
|--------|-------------|---------|
| `googleAds()` | Build Google Ads URL | `MarketingUrlBuilder::googleAds($url, $campaign)` |
| `metaAds()` | Build Facebook Ads URL | `MarketingUrlBuilder::metaAds($url, $campaign)` |
| `withUTM()` | Add UTM parameters | `$builder->withUTM($params)` |

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