<?php

namespace Marshmallow\MarketingData\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Marshmallow\MarketingData\Facades\MarketingDataTracker;

trait HasMarketingData
{
    protected array $queuedMarketingData = [];

    public static function bootHasMarketingData(): void
    {
        static::created(function (Model $marketing_datableModel): void {
            if (count($marketing_datableModel->queuedMarketingData) === 0) {
                return;
            }

            DB::afterCommit(function () use ($marketing_datableModel): void {
                collect($marketing_datableModel->queuedMarketingData)->each(function ($value, $key) use ($marketing_datableModel): void {
                    $marketing_datableModel->setMarketingData($key, $value);
                });
                $marketing_datableModel->queuedMarketingData = [];
            });
        });

        static::deleted(function (Model $deletedModel): void {
            $deletedModel->marketing_data()->delete();
        });
    }

    public function marketing_data(): MorphOne
    {
        return $this->morphOne(MarketingDataTracker::getMarketingDataClassName(), 'marketing_datable');
    }

    public function getCacheKeyForMarketingDataCasts(): string
    {
        return Str::of('marshmallow_marketing_casts_')->append(class_basename($this))->lower();
    }

    public function getMarketingDataCasts(): array
    {
        $cacheKey = $this->getCacheKeyForMarketingDataCasts();

        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        $marketingDataCasts = collect($this->casts)
            ->filter(fn($cast) => $cast == MarketingDataTracker::getMarketingDataCastClassName())
            ->map(fn($cast, $key) => $key)
            ->toArray();

        cache()->put($cacheKey, $marketingDataCasts, now()->addDay());

        return $marketingDataCasts;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getMarketingDataCasts())) {
            return $this->setMarketingData($key, $value);
        }

        parent::setAttribute($key, $value);
    }

    public function setMarketingData($key, $value)
    {
        $encoded_value = $this->maybeEncodeMarketingDataValue($value);

        if (!$this->exists) {
            $this->queuedMarketingData[$key] = $encoded_value;

            return;
        }

        $marketing_data = $this->marketing_data()->firstOrCreate([
            'marketing_datable_id' => $this->id,
            'marketing_datable_type' => get_class($this),
        ]);

        $marketing_data->addMarketingData($key, $encoded_value);

        return $this;
    }

    public function getMarketingData($key)
    {
        if (!in_array($key, $this->getMarketingDataCasts())) {
            return null;
        }

        if (empty($this->marketing_data)) {
            return null;
        }

        $value = $this->marketing_data->getMarketingData($key);

        return $this->maybeDecodeMarketingDataValue($value);
    }

    protected function maybeDecodeMarketingDataValue($value)
    {
        if (empty($value)) {
            return null;
        }

        $object = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $object;
        }

        return $value;
    }

    protected function maybeEncodeMarketingDataValue($value)
    {
        if (is_object($value) || is_array($value)) {
            return json_encode($value, true);
        }

        return $value;
    }
}
