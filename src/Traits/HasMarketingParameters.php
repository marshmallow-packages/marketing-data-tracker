<?php

namespace Marshmallow\MarketingData\Traits;

use Illuminate\Support\Str;
use Marshmallow\MarketingData\Casts\MarketingDataCast;
use Marshmallow\MarketingData\Facades\MarketingDataTracker;

trait HasMarketingParameters
{
    use HasMarketingData;
    use HasTraitsWithCasts;

    public function getHasMarketingParametersCasts()
    {
        $parameters = MarketingDataTracker::getMarketingDataParameters();
        if (empty($parameters)) {
            return [];
        }
        $parameters = collect($parameters)->mapWithKeys(function ($parameter) {
            if (Str::endsWith($parameter, '_*')) {
                $parameter = Str::before($parameter, '_*');
            }

            return [$parameter => MarketingDataCast::class];
        })->toArray();

        return $parameters;
    }

    public function setUtmSourceData($forget = true)
    {
        try {
            $this->addUtmSessionData($forget);
            $this->addSourceData($forget);
        } catch (\Exception $exception) {
            throw new \Exception('Error setting Marketing data: '.$exception->getMessage());
        }
    }

    public function addSourceData($forget = true, $request = null)
    {
        if (! $request) {
            $request = request();
        }

        $session_key = 'mm_source_values';

        if (session()->has($session_key)) {
            if ($forget) {
                $source_values = session()->pull($session_key);
            } else {
                $source_values = session()->get($session_key);
            }

            $allowed_parameters = MarketingDataTracker::getMarketingDataParameters();

            if (is_array($source_values) && ! empty($source_values)) {
                foreach ($source_values as $key => $value) {
                    if (! in_array($key, $allowed_parameters)) {
                        continue;
                    }
                    $this->$key = $value;
                }
                $this->updateQuietly($source_values);
            }
        }
    }

    public function addUtmSessionData($forget = true)
    {
        $session_key = 'mm_utm_values';
        if (session()->has($session_key)) {
            if ($forget) {
                $utm_values = session()->pull($session_key);
            } else {
                $utm_values = session()->get($session_key);
            }

            $allowed_parameters = MarketingDataTracker::getMarketingDataParameters();

            if (is_array($utm_values) && ! empty($utm_values)) {
                foreach ($utm_values as $key => $value) {
                    if (! in_array($key, $allowed_parameters)) {
                        continue;
                    }
                    $this->$key = $value;
                }

                $this->updateQuietly($utm_values);
            }
        }
    }

    public function getUtmSourceMediumAttribute()
    {
        $field = $this->utm_source;
        if ($this->utm_medium) {
            $field .= ' - '.$this->utm_medium;
        }

        return Str::title($field);
    }

    public function getMarketingMediumAttribute()
    {
        return Str::title($this->utm_medium);
    }

    public function getMarketingSourceAttribute()
    {
        return Str::title($this->utm_source);
    }

    public function getUtmCampaignTermAttribute()
    {
        $field = $this->utm_campaign;
        if ($this->utm_term) {
            $field .= ' - '.$this->utm_term;
        }

        return Str::of($field)->limit(30)->headline()->toString();
    }

    public function getUtmMediumTermAttribute()
    {
        $field = $this->utm_medium;
        if ($this->utm_term) {
            $field .= ' - '.$this->utm_term;
        }

        return Str::of($field)->limit(30)->headline()->toString();
    }

    public function getNetwork($value)
    {
        return match ($value) {
            'g' => 'Google Search',
            's' => 'Search partner',
            'd' => 'Display',
            'u' => 'Smart Shopping',
            'ytv' => 'Youtube',
            'vp' => 'Video Partner',
            default => $value,
        };
    }

    public function getDevice($value)
    {
        return match ($value) {
            'm' => 'Mobile',
            't' => 'Tablet',
            'c' => 'Computer',
            default => $value,
        };
    }

    public function getMatchtype($value)
    {
        return match ($value) {
            'e' => 'Exact',
            'p' => 'Phrase',
            'b' => 'Broad',
            default => $value,
        };
    }

    public function hideFields()
    {
        return collect(config('marketing-data-tracker.hidden_marketing_parameters', []));
    }

    public function getMarketingParameterListAttribute()
    {
        $parameters = $this->getHasMarketingParametersCasts();
        $fields = collect($parameters)->keys()->reject(function ($field) {
            return $this->hideFields()->contains($field);
        });

        $fieldValues = $fields->mapWithKeys(function ($field) {
            $value = $this->$field;

            if ($value && Str::startsWith($field, 'mm_')) {

                $value = match ($field) {
                    'mm_matchtype' => $this->getMatchtype($value),
                    'mm_network' => $this->getNetwork($value),
                    'mm_device' => $this->getDevice($value),
                    default => $value,
                };
            }

            if (! $value) {
                return [];
            }

            $field = Str::of($field)
                ->replace('utm_', '')
                ->replace('mm_', '')
                ->replace('_', ' ')
                ->title()->toString();

            if ($value && is_string($value)) {
                $value = Str::of($value)->trim()->toString();
            }

            return [$field => $value];
        })->toArray() ?? [];

        return $fieldValues;
    }
}
