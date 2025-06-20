# Parse and store marketing parameters like gclid, utm\_\*, and adid, and automatically attach them to user conversions or models in Laravel.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marshmallow/marketing-data-tracker.svg?style=flat-square)](https://packagist.org/packages/marshmallow/marketing-data-tracker)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/marshmallow-packages/marketing-data-tracker/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/marshmallow-packages/marketing-data-tracker/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/marshmallow/marketing-data-tracker.svg?style=flat-square)](https://packagist.org/packages/marshmallow/marketing-data-tracker)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require marshmallow/marketing-data-tracker
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="marketing-data-tracker-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="marketing-data-tracker-config"
```

After add the HasMarketingParameters trait to your model. And add the midleware `\Marshmallow\MarketingData\Middleware\ParseMarketingParameters::class,` to your `web` middleware group within your `app/Http/Kernel.php` file.

For a Nova resource add the MarketingDataFields trait to your resource and add the functino to your fields array:

```php
use Marshmallow\MarketingData\Fields\MarketingDataFields;

$this->getMarketingDataFields(
    with_utm_data: true,
    with_google_ids: true,
    with_all_data: false,
    with_raw_data: true,
    with_cookie_data: false,
),

```

### Google Ads

On campaign level set the 'custom parameter' to 'campaign' and the value to the campaign Name without spaces.

After, add the following to the addon url on the account level;

```text
'utm_source=google&utm_medium=cpc&utm_term={keyword}&utm_content={creative}&mm_campaignid={campaignid}&mm_adgroupid={adgroupid}&mm_feedid={feeditemid}&mm_position={adposition}&mm_linterest={loc_interest_ms}&mm_lphys={loc_physical_ms}&mm_matchtype={matchtype}&mm_network={network}&mm_device={device}&mm_devicemodel={devicemodel}&mm_creative={creative}&mm_keyword={keyword}&mm_placement={placement}&mm_targetid={target}&mm_version=G2&gclid={gclid}&utm_campaign={\_campaign}';
```

## Usage

Add the trait and add the function to your model to save the marketing parameters:

```php
    use Marshmallow\MarketingData\Traits\HasMarketingParameters;

    $model->setUtmSourceData(forget: false);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Marshmallow](https://github.com/marshmallow-packages)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
