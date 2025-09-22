<?php

namespace Marshmallow\MarketingData\Events;

use Illuminate\Database\Eloquent\Model;

class MarketingDataCreated extends MarketingDataEvent
{
    public function __construct(
        Model $model,
        array $parameters = []
    ) {
        parent::__construct($model, $parameters);
    }

    /**
     * Get the event description
     */
    public function getDescription(): string
    {
        return "Marketing data was created for {$this->getModelClass()} with ID {$this->getModelId()}";
    }

    /**
     * Get the marketing data that was created
     */
    public function getCreatedData(): array
    {
        return $this->parameters['created_data'] ?? [];
    }

    /**
     * Check if specific UTM parameters were set
     */
    public function hasUtmSource(): bool
    {
        $utmData = $this->getUtmData();
        return !empty($utmData['utm_source']);
    }

    /**
     * Check if any click IDs were detected
     */
    public function hasClickIds(): bool
    {
        return !empty($this->getClickIdData());
    }

    /**
     * Get the acquisition channel
     */
    public function getAcquisitionChannel(): ?string
    {
        $utmData = $this->getUtmData();

        if (!empty($utmData['utm_source'])) {
            $source = $utmData['utm_source'];
            $medium = $utmData['utm_medium'] ?? '';

            return $medium ? "{$source} / {$medium}" : $source;
        }

        return null;
    }

    /**
     * Get campaign information
     */
    public function getCampaignInfo(): array
    {
        $utmData = $this->getUtmData();

        return [
            'campaign' => $utmData['utm_campaign'] ?? null,
            'source' => $utmData['utm_source'] ?? null,
            'medium' => $utmData['utm_medium'] ?? null,
            'term' => $utmData['utm_term'] ?? null,
            'content' => $utmData['utm_content'] ?? null,
        ];
    }

    /**
     * Convert to array with additional created-specific data
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'description' => $this->getDescription(),
            'created_data' => $this->getCreatedData(),
            'has_utm_source' => $this->hasUtmSource(),
            'has_click_ids' => $this->hasClickIds(),
            'acquisition_channel' => $this->getAcquisitionChannel(),
            'campaign_info' => $this->getCampaignInfo(),
        ]);
    }
}