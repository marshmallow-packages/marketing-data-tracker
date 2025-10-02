<?php

namespace Marshmallow\MarketingData\Traits;

use Marshmallow\MarketingData\Events\ConversionTracked;

trait TracksEcommerceEvents
{
    /**
     * Track view item event
     */
    public function trackViewItem(): void
    {
        if (!$this->isEcommerceTrackingEnabled()) {
            return;
        }

        $this->trackEcommerceEvent('view_item', $this->getGTMData());
    }

    /**
     * Track add to cart event
     */
    public function trackAddToCart(int $quantity = 1): void
    {
        if (!$this->isEcommerceTrackingEnabled()) {
            return;
        }

        $this->trackEcommerceEvent('add_to_cart', $this->getGTMData($quantity));
    }

    /**
     * Track remove from cart event
     */
    public function trackRemoveFromCart(int $quantity = 1): void
    {
        if (!$this->isEcommerceTrackingEnabled()) {
            return;
        }

        $this->trackEcommerceEvent('remove_from_cart', $this->getGTMData($quantity));
    }

    /**
     * Track begin checkout event
     */
    public function trackBeginCheckout(array $items, float $total): void
    {
        if (!$this->isEcommerceTrackingEnabled()) {
            return;
        }

        $this->trackEcommerceEvent('begin_checkout', [
            'items' => $items,
            'value' => $total,
            'currency' => $this->getCurrency() ?? config('marketing-data-tracker.ecommerce.currency', 'EUR'),
        ]);
    }

    /**
     * Track purchase event
     */
    public function trackPurchase(string $transactionId, array $items, float $total, ?string $currency = null): void
    {
        if (!$this->isEcommerceTrackingEnabled()) {
            return;
        }

        $currency ??= $this->getCurrency() ?? config('marketing-data-tracker.ecommerce.currency', 'EUR');

        $this->trackEcommerceEvent('purchase', [
            'transaction_id' => $transactionId,
            'items' => $items,
            'value' => $total,
            'currency' => $currency,
        ]);

        // Also track as conversion if enabled
        if (config('marketing-data-tracker.conversions.enabled', false)) {
            $this->trackConversion('purchase', $total, $currency, $transactionId);
        }
    }

    /**
     * Track refund event
     */
    public function trackRefund(string $transactionId, ?array $items = null, ?float $value = null): void
    {
        if (!$this->isEcommerceTrackingEnabled()) {
            return;
        }

        $this->trackEcommerceEvent('refund', [
            'transaction_id' => $transactionId,
            'items' => $items,
            'value' => $value,
            'currency' => $this->getCurrency() ?? config('marketing-data-tracker.ecommerce.currency', 'EUR'),
        ]);
    }

    /**
     * Track generic ecommerce event
     */
    protected function trackEcommerceEvent(string $event, array $data): void
    {
        $eventConfig = config("marketing-data-tracker.ecommerce.events.{$event}", true);

        if (!$eventConfig) {
            return; // Event disabled in config
        }

        // Add standard ecommerce data
        $data = array_merge([
            'event_name' => $event,
            'timestamp' => now()->toISOString(),
        ], $data);

        // Add GTM format if enabled
        if (config('marketing-data-tracker.ecommerce.gtm_format', true)) {
            $data['gtm_formatted'] = true;
        }

        // Fire event for listeners
        event(new ConversionTracked(
            $this,
            $event,
            $data['value'] ?? null,
            $data['currency'] ?? null,
            $data['transaction_id'] ?? null,
            $this->getPrimaryClickId() ?? null,
            [
                'ecommerce_data' => $data,
                'event_type' => 'ecommerce',
            ]
        ));
    }

    /**
     * Track conversion (generic conversion tracking)
     */
    public function trackConversion(
        string $type,
        ?float $value = null,
        ?string $currency = null,
        ?string $conversionId = null
    ): void {
        if (!config('marketing-data-tracker.conversions.enabled', false)) {
            return;
        }

        $currency ??= config('marketing-data-tracker.ecommerce.currency', 'EUR');

        event(new ConversionTracked(
            $this,
            $type,
            $value,
            $currency,
            $conversionId,
            $this->getPrimaryClickId() ?? null,
            [
                'conversion_timestamp' => now()->toISOString(),
                'auto_tracked' => true,
            ]
        ));

        // Mark as tracked if model supports it
        if (method_exists($this, 'markConversionTracked')) {
            $this->markConversionTracked($type, $conversionId);
        }
    }

    /**
     * Get GTM formatted product data
     */
    public function getGTMData(int $quantity = 1): array
    {
        if (!method_exists($this, 'getTrackingProductId')) {
            return [];
        }

        $data = [
            'item_id' => $this->getTrackingProductId(),
            'item_name' => $this->getTrackingProductName(),
            'quantity' => $quantity,
        ];

        // Optional fields
        if (method_exists($this, 'getTrackingProductCategory') && $this->getTrackingProductCategory()) {
            $data['item_category'] = $this->getTrackingProductCategory();
        }

        if (method_exists($this, 'getTrackingProductBrand') && $this->getTrackingProductBrand()) {
            $data['item_brand'] = $this->getTrackingProductBrand();
        }

        if (method_exists($this, 'getTrackingProductPrice') && $this->getTrackingProductPrice()) {
            $data['price'] = $this->getTrackingProductPrice();
        }

        if (method_exists($this, 'getTrackingProductVariant') && $this->getTrackingProductVariant()) {
            $data['item_variant'] = $this->getTrackingProductVariant();
        }

        if (method_exists($this, 'getTrackingProductCurrency') && $this->getTrackingProductCurrency()) {
            $data['currency'] = $this->getTrackingProductCurrency();
        }

        // Add custom parameters
        if (method_exists($this, 'getCustomTrackingParameters')) {
            $customParams = $this->getCustomTrackingParameters();
            $data = array_merge($data, $customParams);
        }

        return $data;
    }

    /**
     * Get platform-specific tracking data
     */
    public function getPlatformTrackingData(string $platform): array
    {
        $baseData = $this->getGTMData();

        return match ($platform) {
            'google_ads' => $this->getGoogleAdsTrackingData($baseData),
            'meta' => $this->getMetaTrackingData($baseData),
            'microsoft' => $this->getMicrosoftTrackingData($baseData),
            default => $baseData,
        };
    }

    /**
     * Get Google Ads specific tracking data
     */
    protected function getGoogleAdsTrackingData(array $baseData): array
    {
        return array_merge($baseData, [
            'google_business_vertical' => 'retail',
        ]);
    }

    /**
     * Get Meta/Facebook specific tracking data
     */
    protected function getMetaTrackingData(array $baseData): array
    {
        return array_merge($baseData, [
            'content_type' => 'product',
            'content_ids' => [$baseData['item_id']],
        ]);
    }

    /**
     * Get Microsoft Ads specific tracking data
     */
    protected function getMicrosoftTrackingData(array $baseData): array
    {
        return $baseData; // Microsoft uses standard format
    }

    /**
     * Check if ecommerce tracking is enabled
     */
    protected function isEcommerceTrackingEnabled(): bool
    {
        return config('marketing-data-tracker.ecommerce.enabled', false);
    }

    /**
     * Get currency for tracking (try to get from model first)
     */
    protected function getCurrency(): ?string
    {
        if (method_exists($this, 'getTrackingProductCurrency')) {
            return $this->getTrackingProductCurrency();
        }

        return null;
    }

    /**
     * Get primary click ID for attribution
     */
    protected function getPrimaryClickId(): ?string
    {
        if (method_exists($this, 'getPrimaryClickIdAttribute')) {
            return $this->getPrimaryClickIdAttribute();
        }

        return null;
    }

    /**
     * Track multiple items at once
     */
    public function trackMultipleItems(string $event, array $items): void
    {
        if (!$this->isEcommerceTrackingEnabled()) {
            return;
        }

        $totalValue = 0;
        $currency = config('marketing-data-tracker.ecommerce.currency', 'EUR');
        $gtmItems = [];

        foreach ($items as $item) {
            if (method_exists($item, 'getGTMData')) {
                $gtmData = $item->getGTMData($item->quantity ?? 1);
                $gtmItems[] = $gtmData;

                if (isset($gtmData['price'])) {
                    $totalValue += $gtmData['price'] * ($gtmData['quantity'] ?? 1);
                }

                if (isset($gtmData['currency'])) {
                    $currency = $gtmData['currency'];
                }
            }
        }

        $this->trackEcommerceEvent($event, [
            'items' => $gtmItems,
            'value' => $totalValue,
            'currency' => $currency,
        ]);
    }
}
