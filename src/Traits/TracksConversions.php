<?php

namespace Marshmallow\MarketingData\Traits;

use DateTime;
use Marshmallow\MarketingData\Events\ConversionTracked;

trait TracksConversions
{
    /**
     * Track a conversion event
     */
    public function trackConversion(
        string $type,
        ?float $value = null,
        ?string $currency = null,
        ?string $conversionId = null,
        array $additionalData = []
    ): void {
        if (!$this->isConversionTrackingEnabled()) {
            return;
        }

        if (!$this->shouldTrackConversion()) {
            return;
        }

        // Get conversion configuration
        $conversionConfig = $this->getConversionConfig($type);

        if (!$conversionConfig) {
            return; // Conversion type not configured
        }

        // Use configured value if not provided
        $value ??= $conversionConfig['value'] ?? $this->getConversionValue();
        $currency ??= $this->getConversionCurrency() ?? 'EUR';
        $conversionId ??= $this->generateConversionId($type);

        // Fire conversion event
        event(new ConversionTracked(
            $this,
            $type,
            $value,
            $currency,
            $conversionId,
            $this->getPrimaryClickId(),
            array_merge([
                'conversion_timestamp' => now()->toISOString(),
                'conversion_config' => $conversionConfig,
                'attribution_data' => $this->getAttributionData(),
            ], $additionalData)
        ));

        // Mark as tracked
        $this->markConversionTracked($type, $conversionId);
    }

    /**
     * Track lead conversion
     */
    public function trackLeadConversion(?float $value = null): void
    {
        $this->trackConversion('lead', $value);
    }

    /**
     * Track qualified lead conversion
     */
    public function trackQualifiedLeadConversion(?float $value = null): void
    {
        $this->trackConversion('qualified_lead', $value);
    }

    /**
     * Track converted lead
     */
    public function trackConvertedLeadConversion(?float $value = null): void
    {
        $this->trackConversion('converted_lead', $value);
    }

    /**
     * Track purchase conversion
     */
    public function trackPurchaseConversion(float $value, ?string $currency = null, ?string $orderId = null): void
    {
        $this->trackConversion('purchase', $value, $currency, $orderId);
    }

    /**
     * Track signup conversion
     */
    public function trackSignupConversion(): void
    {
        $this->trackConversion('signup');
    }

    /**
     * Track subscription conversion
     */
    public function trackSubscriptionConversion(float $value, ?string $currency = null): void
    {
        $this->trackConversion('subscription', $value, $currency);
    }

    /**
     * Track custom conversion
     */
    public function trackCustomConversion(string $type, array $data = []): void
    {
        $value = $data['value'] ?? null;
        $currency = $data['currency'] ?? null;
        $conversionId = $data['conversion_id'] ?? null;

        unset($data['value'], $data['currency'], $data['conversion_id']);

        $this->trackConversion($type, $value, $currency, $conversionId, $data);
    }

    /**
     * Get conversion configuration for a type
     */
    protected function getConversionConfig(string $type): ?array
    {
        $conversionTypes = config('marketing-data-tracker.conversions.types', []);

        return $conversionTypes[$type] ?? null;
    }

    /**
     * Check if conversion tracking is enabled
     */
    protected function isConversionTrackingEnabled(): bool
    {
        return config('marketing-data-tracker.conversions.enabled', false);
    }

    /**
     * Default implementation for shouldTrackConversion
     */
    public function shouldTrackConversion(): bool
    {
        // Can be overridden in models to add custom logic
        return true;
    }

    /**
     * Default implementation for getConversionType
     */
    public function getConversionType(): string
    {
        // Default conversion type, can be overridden
        return 'lead';
    }

    /**
     * Default implementation for getConversionValue
     */
    public function getConversionValue(): ?float
    {
        // Can be overridden in models to return model-specific value
        return null;
    }

    /**
     * Default implementation for getConversionCurrency
     */
    public function getConversionCurrency(): ?string
    {
        return config('marketing-data-tracker.ecommerce.currency', 'EUR');
    }

    /**
     * Mark conversion as tracked
     */
    public function markConversionTracked(string $type, ?string $conversionId = null): void
    {
        // Store in model attributes if column exists
        if ($this->hasAttribute('conversions_tracked')) {
            $tracked = $this->conversions_tracked ?? [];
            $tracked[$type] = [
                'tracked_at' => now()->toISOString(),
                'conversion_id' => $conversionId,
            ];
            $this->update(['conversions_tracked' => $tracked]);
        } else {
            // Store in session as fallback
            $sessionKey = "conversions_tracked_{$this->getKey()}";
            $tracked = session()->get($sessionKey, []);
            $tracked[$type] = [
                'tracked_at' => now()->toISOString(),
                'conversion_id' => $conversionId,
            ];
            session()->put($sessionKey, $tracked);
        }
    }

    /**
     * Check if conversion has been tracked
     */
    public function isConversionTracked(string $type): bool
    {
        // Check model attributes first
        if ($this->hasAttribute('conversions_tracked')) {
            $tracked = $this->conversions_tracked ?? [];

            return isset($tracked[$type]);
        }

        // Check session as fallback
        $sessionKey = "conversions_tracked_{$this->getKey()}";
        $tracked = session()->get($sessionKey, []);

        return isset($tracked[$type]);
    }

    /**
     * Get all tracked conversions
     */
    public function getTrackedConversions(): array
    {
        // Check model attributes first
        if ($this->hasAttribute('conversions_tracked')) {
            return $this->conversions_tracked ?? [];
        }

        // Check session as fallback
        $sessionKey = "conversions_tracked_{$this->getKey()}";

        return session()->get($sessionKey, []);
    }

    /**
     * Get attribution data for conversions
     */
    public function getAttributionData(): array
    {
        $data = [];

        if (method_exists($this, 'getAllRawMarketingParametersAttribute')) {
            $data['marketing_parameters'] = $this->getAllRawMarketingParametersAttribute();
        }

        if (method_exists($this, 'getUtmData')) {
            $data['utm_data'] = $this->getUtmData();
        }

        if (method_exists($this, 'getAllClickIdsAttribute')) {
            $data['click_ids'] = $this->getAllClickIdsAttribute();
        }

        if (method_exists($this, 'getPlatformNameAttribute')) {
            $data['platform'] = $this->getPlatformNameAttribute();
        }

        return $data;
    }

    /**
     * Get the primary click ID for conversion attribution
     */
    public function getPrimaryClickId(): ?string
    {
        if (method_exists($this, 'getPrimaryClickIdAttribute')) {
            return $this->getPrimaryClickIdAttribute();
        }

        return null;
    }

    /**
     * Get conversion timestamp
     */
    public function getConversionTimestamp(): ?DateTime
    {
        // Return creation time as default conversion timestamp
        if ($this->created_at) {
            return $this->created_at instanceof DateTime
                ? $this->created_at
                : new DateTime($this->created_at);
        }

        return new DateTime;
    }

    /**
     * Generate a unique conversion ID
     */
    protected function generateConversionId(string $type): string
    {
        $modelClass = class_basename($this);
        $modelId = $this->getKey();
        $timestamp = now()->timestamp;

        return "{$modelClass}_{$modelId}_{$type}_{$timestamp}";
    }

    /**
     * Check if model has attribute
     */
    protected function hasAttribute(string $attribute): bool
    {
        return in_array($attribute, $this->fillable) ||
               in_array($attribute, $this->guarded) ||
               array_key_exists($attribute, $this->casts) ||
               array_key_exists($attribute, $this->attributes);
    }

    /**
     * Get conversion priority
     */
    public function getConversionPriority(string $type): int
    {
        $config = $this->getConversionConfig($type);

        return $config['priority'] ?? 0;
    }

    /**
     * Track time-delayed conversion
     */
    public function trackDelayedConversion(string $type, int $delaySeconds, array $data = []): void
    {
        // This would typically use a job queue to track conversion after delay
        dispatch(function () use ($type, $data): void {
            $this->trackCustomConversion($type, $data);
        })->delay(now()->addSeconds($delaySeconds));
    }

    /**
     * Get conversion funnel position
     */
    public function getConversionFunnelPosition(string $type): int
    {
        $config = $this->getConversionConfig($type);

        return $config['funnel_position'] ?? 1;
    }

    /**
     * Check if conversion is higher in funnel than existing conversions
     */
    public function isHigherFunnelConversion(string $type): bool
    {
        $newPriority = $this->getConversionPriority($type);
        $tracked = $this->getTrackedConversions();

        foreach (array_keys($tracked) as $trackedType) {
            $trackedPriority = $this->getConversionPriority($trackedType);
            if ($trackedPriority >= $newPriority) {
                return false;
            }
        }

        return true;
    }
}
