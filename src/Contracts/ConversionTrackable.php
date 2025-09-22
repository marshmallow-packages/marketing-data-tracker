<?php

namespace Marshmallow\MarketingData\Contracts;

interface ConversionTrackable
{
    /**
     * Get the primary click ID for conversion tracking
     */
    public function getPrimaryClickId(): ?string;

    /**
     * Determine if conversion should be tracked
     */
    public function shouldTrackConversion(): bool;

    /**
     * Get conversion type/action name
     */
    public function getConversionType(): string;

    /**
     * Get conversion value
     */
    public function getConversionValue(): ?float;

    /**
     * Get conversion currency
     */
    public function getConversionCurrency(): ?string;

    /**
     * Mark conversion as tracked
     *
     * @param string $type The conversion type
     * @param string|null $conversionId Optional external conversion ID
     * @return void
     */
    public function markConversionTracked(string $type, ?string $conversionId = null): void;

    /**
     * Check if conversion has already been tracked
     */
    public function isConversionTracked(string $type): bool;

    /**
     * Get all marketing attribution data for the conversion
     */
    public function getAttributionData(): array;

    /**
     * Get the conversion timestamp
     */
    public function getConversionTimestamp(): ?\DateTime;
}