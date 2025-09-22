<?php

namespace Marshmallow\MarketingData\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Marshmallow\MarketingData\Traits\HasMarketingParameters;
use Marshmallow\MarketingData\Traits\TracksConversions;
use Marshmallow\MarketingData\Traits\TracksEcommerceEvents;
use Marshmallow\MarketingData\Contracts\ConversionTrackable;
use Marshmallow\MarketingData\Contracts\ProductTrackable;

class TestModel extends Model implements ConversionTrackable, ProductTrackable
{
    use HasMarketingParameters, TracksConversions, TracksEcommerceEvents {
        TracksConversions::trackConversion insteadof TracksEcommerceEvents;
    }

    protected $fillable = ['*'];
    protected $guarded = [];

    protected $casts = [
        'marketing_data' => 'array',
        'conversions_tracked' => 'array',
    ];

    /**
     * Helper method to set marketing data for testing
     */
    public function setMarketingData(array $data): void
    {
        $this->attributes = array_merge($this->attributes, $data);
    }

    /**
     * Implementation of ConversionTrackable interface
     */
    public function shouldTrackConversion(): bool
    {
        return true;
    }

    public function getConversionType(): string
    {
        return 'test_conversion';
    }

    public function getConversionValue(): ?float
    {
        return $this->attributes['conversion_value'] ?? 100.0;
    }

    public function getConversionCurrency(): ?string
    {
        return 'EUR';
    }

    public function getConversionTimestamp(): ?\DateTime
    {
        return new \DateTime();
    }

    /**
     * Implementation of ProductTrackable interface
     */
    public function getTrackingProductId(): string
    {
        return $this->attributes['product_id'] ?? 'test_product_123';
    }

    public function getTrackingProductName(): string
    {
        return $this->attributes['product_name'] ?? 'Test Product';
    }

    public function getTrackingProductCategory(): ?string
    {
        return $this->attributes['category'] ?? 'Electronics';
    }

    public function getTrackingProductBrand(): ?string
    {
        return $this->attributes['brand'] ?? 'Test Brand';
    }

    public function getTrackingProductPrice(): ?float
    {
        return $this->attributes['price'] ?? 99.99;
    }

    public function getTrackingProductCurrency(): ?string
    {
        return 'EUR';
    }

    public function getTrackingProductVariant(): ?string
    {
        return $this->attributes['variant'] ?? 'Red/Large';
    }

    public function isTrackingProductInStock(): bool
    {
        return $this->attributes['in_stock'] ?? true;
    }

    public function getCustomTrackingParameters(): array
    {
        return $this->attributes['custom_tracking'] ?? [];
    }

    /**
     * Helper method to check if attribute exists
     */
    public function hasAttribute($key): bool
    {
        return array_key_exists($key, $this->attributes) ||
               in_array($key, $this->fillable) ||
               array_key_exists($key, $this->casts);
    }
}