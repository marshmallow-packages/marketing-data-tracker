<?php

namespace Marshmallow\MarketingData\Events;

use Illuminate\Database\Eloquent\Model;

class ConversionTracked extends MarketingDataEvent
{
    public function __construct(
        Model $model,
        public string $conversionType,
        public ?float $conversionValue = null,
        public ?string $conversionCurrency = null,
        public ?string $conversionId = null,
        public ?string $clickId = null,
        array $parameters = []
    ) {
        parent::__construct($model, $parameters);
    }

    /**
     * Get the event description
     */
    public function getDescription(): string
    {
        $value = $this->conversionValue ? " worth {$this->conversionValue} {$this->conversionCurrency}" : '';

        return "Conversion '{$this->conversionType}' tracked for {$this->getModelClass()} with ID {$this->getModelId()}{$value}";
    }

    /**
     * Get conversion data
     */
    public function getConversionData(): array
    {
        return [
            'type' => $this->conversionType,
            'value' => $this->conversionValue,
            'currency' => $this->conversionCurrency,
            'conversion_id' => $this->conversionId,
            'click_id' => $this->clickId,
        ];
    }

    /**
     * Get the conversion value in a specific currency
     */
    public function getConversionValueIn(string $currency): ?float
    {
        if (!$this->conversionValue) {
            return null;
        }

        if ($this->conversionCurrency === $currency) {
            return $this->conversionValue;
        }

        // In a real implementation, you might want to add currency conversion here
        return $this->conversionValue;
    }

    /**
     * Check if conversion has monetary value
     */
    public function hasValue(): bool
    {
        return $this->conversionValue !== null && $this->conversionValue > 0;
    }

    /**
     * Get attribution data for the conversion
     */
    public function getAttributionData(): array
    {
        $marketingData = $this->getMarketingData();
        $utmData = $this->getUtmData();
        $clickIds = $this->getClickIdData();

        return [
            'utm_source' => $utmData['utm_source'] ?? null,
            'utm_medium' => $utmData['utm_medium'] ?? null,
            'utm_campaign' => $utmData['utm_campaign'] ?? null,
            'utm_term' => $utmData['utm_term'] ?? null,
            'utm_content' => $utmData['utm_content'] ?? null,
            'primary_click_id' => $this->getPrimaryClickId(),
            'click_ids' => $clickIds,
            'platform' => $this->getPlatformName(),
            'all_marketing_data' => $marketingData,
        ];
    }

    /**
     * Get the acquisition cost per conversion (if available)
     */
    public function getCostPerConversion(): ?float
    {
        // This would need to be calculated based on ad spend data
        // Implementation depends on how cost data is stored
        return $this->parameters['cost_per_conversion'] ?? null;
    }

    /**
     * Get the time to conversion (if available)
     */
    public function getTimeToConversion(): ?int
    {
        // Calculate time between first touch and conversion
        $firstTouchTime = $this->parameters['first_touch_time'] ?? null;

        if ($firstTouchTime) {
            return time() - strtotime($firstTouchTime);
        }

        return null;
    }

    /**
     * Get conversion priority based on configuration
     */
    public function getConversionPriority(): int
    {
        $conversionTypes = config('marketing-data-tracker.conversions.types', []);

        return $conversionTypes[$this->conversionType]['priority'] ?? 0;
    }

    /**
     * Check if this is a high-value conversion
     */
    public function isHighValue(): bool
    {
        $threshold = $this->parameters['high_value_threshold'] ?? 100;

        return $this->hasValue() && $this->conversionValue >= $threshold;
    }

    /**
     * Get revenue attribution
     */
    public function getRevenueAttribution(): array
    {
        if (!$this->hasValue()) {
            return [];
        }

        $attribution = $this->getAttributionData();

        return [
            'revenue' => $this->conversionValue,
            'currency' => $this->conversionCurrency,
            'utm_source' => $attribution['utm_source'],
            'utm_medium' => $attribution['utm_medium'],
            'utm_campaign' => $attribution['utm_campaign'],
            'platform' => $attribution['platform'],
            'click_id' => $attribution['primary_click_id'],
        ];
    }

    /**
     * Convert to array with additional conversion-specific data
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'description' => $this->getDescription(),
            'conversion_type' => $this->conversionType,
            'conversion_value' => $this->conversionValue,
            'conversion_currency' => $this->conversionCurrency,
            'conversion_id' => $this->conversionId,
            'click_id' => $this->clickId,
            'conversion_data' => $this->getConversionData(),
            'has_value' => $this->hasValue(),
            'is_high_value' => $this->isHighValue(),
            'attribution_data' => $this->getAttributionData(),
            'revenue_attribution' => $this->getRevenueAttribution(),
            'time_to_conversion' => $this->getTimeToConversion(),
            'conversion_priority' => $this->getConversionPriority(),
        ]);
    }
}
