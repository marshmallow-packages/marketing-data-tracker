<?php

namespace Marshmallow\MarketingData\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class MarketingDataEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Model $model,
        public array $parameters = []
    ) {}

    /**
     * Get model class name
     */
    public function getModelClass(): string
    {
        return get_class($this->model);
    }

    /**
     * Get model ID
     */
    public function getModelId(): mixed
    {
        return $this->model->getKey();
    }

    /**
     * Get all marketing data from model
     */
    public function getMarketingData(): array
    {
        if (method_exists($this->model, 'getAllRawMarketingParametersAttribute')) {
            return $this->model->getAllRawMarketingParametersAttribute();
        }

        return [];
    }

    /**
     * Get UTM data from model
     */
    public function getUtmData(): array
    {
        $marketingData = $this->getMarketingData();
        $utmData = [];

        foreach ($marketingData as $key => $value) {
            if (str_starts_with($key, 'utm_')) {
                $utmData[$key] = $value;
            }
        }

        return $utmData;
    }

    /**
     * Get click ID data from model
     */
    public function getClickIdData(): array
    {
        $clickIds = [];

        if (method_exists($this->model, 'getAllClickIdsAttribute')) {
            $clickIds = $this->model->getAllClickIdsAttribute();
        }

        return $clickIds;
    }

    /**
     * Get the primary click ID
     */
    public function getPrimaryClickId(): ?string
    {
        if (method_exists($this->model, 'getPrimaryClickIdAttribute')) {
            return $this->model->getPrimaryClickIdAttribute();
        }

        return null;
    }

    /**
     * Get platform name
     */
    public function getPlatformName(): ?string
    {
        if (method_exists($this->model, 'getPlatformNameAttribute')) {
            return $this->model->getPlatformNameAttribute();
        }

        return null;
    }

    /**
     * Get event timestamp
     */
    public function getTimestamp(): \DateTime
    {
        return new \DateTime();
    }

    /**
     * Get event data for logging/analytics
     */
    public function toArray(): array
    {
        return [
            'event_type' => static::class,
            'model_class' => $this->getModelClass(),
            'model_id' => $this->getModelId(),
            'timestamp' => $this->getTimestamp()->format('Y-m-d H:i:s'),
            'marketing_data' => $this->getMarketingData(),
            'utm_data' => $this->getUtmData(),
            'click_ids' => $this->getClickIdData(),
            'primary_click_id' => $this->getPrimaryClickId(),
            'platform' => $this->getPlatformName(),
            'parameters' => $this->parameters,
        ];
    }
}