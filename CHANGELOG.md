# Changelog

All notable changes to `marketing-data-tracker` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased] - v2.0.0 - Major Platform Enhancement Update

### 🚀 Major New Features

#### Multi-Platform Support System
- **Enhanced Platform Configuration**: Comprehensive support for 12+ advertising platforms with individual enable/disable controls
- **Platform Manager Service**: New `PlatformManager` service for managing platform configurations and parameters
- **Wildcard Pattern Support**: Advanced pattern matching for cookies and parameters (e.g., `_ga*`, `utm_*`, `_gcl*`)
- **Cookie Management System**: New `CookieManager` service with consent management and group-based organization
- **Automatic Cookie Exception Registration**: Package automatically registers marketing cookies with Laravel's `EncryptCookies` middleware
- **$_COOKIE Fallback**: Falls back to reading directly from `$_COOKIE` superglobal when cookies are encrypted

#### Click ID Management Enhancement
- **Priority-Based Click ID Detection**: Configurable priority system for click ID selection (gclid > fbclid > msclkid, etc.)
- **Google Click ID Optimization**: Enhanced `getPrimaryGoogleClickId()` method with gclid > wbraid > gbraid priority
- **Cookie Extraction**: Automatic extraction of clean click ID values from Google cookie format
- **Session-Based Storage**: Click ID storage in session for custom handling and attribution

#### Marketing URL Builder
- **Fluent URL Builder**: New `MarketingUrlBuilder` class with chainable methods for URL construction
- **Platform Templates**: Pre-configured URL templates for Google Ads, Meta, Microsoft, LinkedIn, Twitter, TikTok, Pinterest
- **ValueTrack Integration**: Full Google Ads ValueTrack parameter support with placeholders
- **Dynamic Parameters**: Meta/Facebook dynamic parameter integration with campaign placeholders

#### Event-Driven Architecture
- **Marketing Data Events**: New event system for tracking marketing data lifecycle
  - `MarketingDataCreated` - Fired when marketing data is first captured
  - `MarketingDataUpdated` - Fired when marketing data changes
  - `ConversionTracked` - Fired when conversions are tracked
  - `ClickIdDetected` - Fired when click IDs are detected
- **Configurable Event Listeners**: Configure custom event listeners via configuration
- **Rich Event Data**: Events include attribution data, conversion details, and platform information

#### E-commerce & Conversion Tracking
- **E-commerce Event Tracking**: New `TracksEcommerceEvents` trait with GTM-compatible format
- **Conversion Tracking Framework**: New `TracksConversions` trait with configurable conversion types
- **Revenue Attribution**: Track purchase values and revenue attribution by marketing source
- **Multiple Platform Support**: Platform-specific tracking data for Google Ads, Meta, Microsoft

#### Observer System
- **Automatic Model Observation**: New `MarketingDataObserver` for automatic UTM data capture
- **Configurable Auto-Detection**: Automatic click ID detection and session storage
- **Model Registration**: Configure which models should be automatically observed

### 🔧 Enhanced Existing Features

#### Platform Support Expansion
- **Google Ads**: Complete ValueTrack parameter support (20+ new parameters)
- **Meta/Facebook**: Enhanced with internal campaign tracking parameters
- **Microsoft/Bing**: Comprehensive UET parameter support
- **LinkedIn**: Professional network ad tracking with li_fat_id
- **Twitter/X**: Social media campaign tracking with twclid
- **Pinterest**: Visual platform tracking with epik
- **TikTok**: Short-form video platform tracking with ttclid
- **Reddit**: Community platform tracking with rdt_cid
- **Snapchat**: Mobile-first platform tracking with sscid
- **Amazon**: E-commerce advertising tracking with maas
- **TradeTracker**: Affiliate network tracking (NEW)
- **Email Marketing**: Mailchimp integration (NEW)

#### Configuration Enhancements
- **Platform-Specific Configs**: Individual platform enable/disable controls
- **Click ID Management**: Comprehensive priority and extraction configuration
- **Wildcard Patterns**: Pattern-based parameter and cookie matching
- **Cookie Management**: Group-based organization with consent controls and automatic encryption exception registration
- **Event System**: Configurable event listeners and firing controls

### 🏗️ New Services & Classes

#### Services
- **`PlatformManager`**: Centralized platform configuration management
- **`CookieManager`**: Advanced cookie tracking with consent management
- **`MarketingUrlBuilder`**: Fluent URL construction with platform templates

#### Events
- **`MarketingDataCreated`**: Marketing data creation event
- **`MarketingDataUpdated`**: Marketing data update event
- **`ConversionTracked`**: Conversion tracking event
- **`ClickIdDetected`**: Click ID detection event

#### Traits
- **`TracksConversions`**: Conversion tracking functionality
- **`TracksEcommerceEvents`**: E-commerce event tracking

#### Contracts
- **`ConversionTrackable`**: Interface for conversion tracking
- **`ProductTrackable`**: Interface for product tracking

#### Observers
- **`MarketingDataObserver`**: Automatic model observation

### 📊 New Configuration Sections

```php
'platforms' => [...],                    // Platform-specific configurations
'click_id_management' => [...],         // Click ID priority and extraction
'wildcard_patterns' => [...],           // Pattern matching configuration
'events' => [...],                      // Event system configuration
'observers' => [...],                   // Model observation configuration
'cookie_management' => [...],           // Cookie tracking and consent
'conversions' => [...],                 // Conversion tracking types
'ecommerce' => [...],                   // E-commerce event configuration
```

### 🧪 Testing & Quality

#### Comprehensive Test Suite
- **Unit Tests**: Complete coverage for new services
- **Feature Tests**: End-to-end testing for click ID management and platform detection
- **Test Models**: Helper models for testing scenarios
- **Edge Case Coverage**: Testing for empty configurations and error conditions

### 🔄 Backward Compatibility

#### 100% Backward Compatible
- **No Breaking Changes**: All existing functionality preserved
- **Opt-In Features**: New features disabled by default
- **Configuration Migration**: Existing configs continue to work
- **Method Preservation**: All existing methods maintain behavior

### 📚 Documentation Updates

#### Enhanced Documentation
- **Updated README**: Comprehensive documentation for all new features
- **API Reference**: Complete API documentation for new services and methods
- **Configuration Guide**: Detailed examples for all platforms
- **Migration Guide**: Step-by-step upgrade instructions

---

## v1.1.0 - Add Cookies - 2025-06-23

**Full Changelog**: https://github.com/marshmallow-packages/marketing-data-tracker/compare/v1.0.2...v1.1.0

## v1.0.2 - 2025-06-16

**Full Changelog**: https://github.com/marshmallow-packages/marketing-data-tracker/compare/v1.0.1...v1.0.2

**Full Changelog**: https://github.com/marshmallow-packages/marketing-data-tracker/compare/v1.0.1...v1.0.2

## v1.0.1 - 2025-06-06

**Full Changelog**: https://github.com/marshmallow-packages/marketing-data-tracker/compare/v1.0.0...v1.0.1

## v1.0.0 - Init release - 2025-06-06

**Full Changelog**: https://github.com/marshmallow-packages/marketing-data-tracker/commits/v1.0.0
