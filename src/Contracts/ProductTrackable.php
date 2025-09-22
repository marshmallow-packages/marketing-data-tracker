<?php

namespace Marshmallow\MarketingData\Contracts;

interface ProductTrackable
{
    /**
     * Get Google Tag Manager formatted data
     */
    public function getGTMData(int $quantity = 1): array;

    /**
     * Get data for specific platform tracking
     */
    public function getPlatformTrackingData(string $platform): array;

    /**
     * Get product ID for tracking
     */
    public function getTrackingProductId(): string;

    /**
     * Get product name for tracking
     */
    public function getTrackingProductName(): string;

    /**
     * Get product category for tracking
     */
    public function getTrackingProductCategory(): ?string;

    /**
     * Get product brand for tracking
     */
    public function getTrackingProductBrand(): ?string;

    /**
     * Get product price for tracking
     */
    public function getTrackingProductPrice(): ?float;

    /**
     * Get product currency for tracking
     */
    public function getTrackingProductCurrency(): ?string;

    /**
     * Get product variant for tracking (size, color, etc.)
     */
    public function getTrackingProductVariant(): ?string;

    /**
     * Check if product is in stock for tracking
     */
    public function isTrackingProductInStock(): bool;

    /**
     * Get additional custom tracking parameters
     */
    public function getCustomTrackingParameters(): array;
}