<?php

namespace Marshmallow\MarketingData\Traits;

use Illuminate\Support\Str;
use Marshmallow\MarketingData\Casts\MarketingDataCast;
use Marshmallow\MarketingData\Facades\MarketingDataTracker;

trait HasMarketingParameters
{
    use HasMarketingData;
    use HasTraitsWithCasts;

    public function getGoogleClickIdParameters(): array
    {
        return ['gclid', 'gbraid', 'wbraid'];
    }

    public function getMetaClickIdParameters(): array
    {
        return ['fbclid'];
    }

    public function getHasMarketingParametersCasts()
    {
        $parameters = MarketingDataTracker::getMarketingDataParameters();
        $cookies = MarketingDataTracker::getMarketingDataCookies();

        $casts = array_merge($parameters, $cookies);

        $casts[] = 'cookie_values';

        if (empty($casts)) {
            return [];
        }

        $casts = collect($casts)->mapWithKeys(function ($cast) {
            return [$cast => MarketingDataCast::class];
        });

        $casts->each(function ($class, $cast) use (&$casts) {
            if (Str::endsWith($cast, '_*')) {
                $cast = Str::before($cast, '_*');
                $casts[$cast] = MarketingDataCast::class;
            }
        });

        $casts->each(function ($class, $cast) use (&$casts) {
            if (Str::endsWith($cast, '*')) {
                $cast = Str::before($cast, '*');
                $casts[$cast] = MarketingDataCast::class;
            }
        });

        $casts->each(function ($class, $cast) use (&$casts) {
            $field = Str::of($cast)->trim()->toString();
            if (! Str::of($field)->endsWith('*')) {
                return;
            }
            $cast = Str::of($field)->before('*')->beforeLast('_');
            if ($cast->isEmpty()) {
                $cast = Str::of($field)->before('*');
            }
            if (Str::of($cast)->startsWith('_')) {
                $cast = Str::of($cast)->after('_');
            }
            $cast = $cast->toString();
            if ($cast) {
                $casts[$cast] = MarketingDataCast::class;
            }
        });

        $casts = $casts->toArray();

        return $casts;
    }

    public function setUtmSourceData($forget = true)
    {
        try {
            $this->addUtmSessionData($forget);
            $this->addSourceData($forget);
            $this->addCookieData($forget);
        } catch (\Exception $exception) {
            throw new \Exception('Error setting Marketing data: '.$exception->getMessage());
        }
    }

    public function addCookieData($forget = true, $request = null)
    {
        if (! $request) {
            $request = request();
        }

        $session_key = 'mm_cookie_values';

        if (session()->has($session_key)) {
            if ($forget) {
                $source_values = session()->pull($session_key);
            } else {
                $source_values = session()->get($session_key);
            }

            $allowed_parameters = MarketingDataTracker::getMarketingDataCookies();

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
            'fb' => 'Facebook',
            'ig' => 'Instagram',
            'an' => 'Audience Network',
            'msg' => 'Messenger',
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

    public function getPlacement($value)
    {
        return match ($value) {
            'facebook_desktop_feed' => 'Facebook Desktop Feed',
            'facebook_mobile_feed' => 'Facebook Mobile Feed',
            'facebook_right_column' => 'Facebook Right Column',
            'facebook_instant_article' => 'Facebook Instant Article',
            'facebook_instream_video' => 'Facebook In-Stream Video',
            'facebook_marketplace' => 'Facebook Marketplace',
            'facebook_stories' => 'Facebook Stories',
            'facebook_reels' => 'Facebook Reels',
            'instagram_feed' => 'Instagram Feed',
            'instagram_stories' => 'Instagram Stories',
            'instagram_reels' => 'Instagram Reels',
            'instagram_explore' => 'Instagram Explore',
            'messenger_inbox' => 'Messenger Inbox',
            'messenger_stories' => 'Messenger Stories',
            'audience_network_native' => 'Audience Network Native',
            'audience_network_banner' => 'Audience Network Banner',
            'audience_network_interstitial' => 'Audience Network Interstitial',
            'audience_network_rewarded_video' => 'Audience Network Rewarded Video',
            default => Str::title(str_replace('_', ' ', $value)),
        };
    }

    public function getSiteSourceName($value)
    {
        return match ($value) {
            'fb' => 'Facebook',
            'ig' => 'Instagram',
            'an' => 'Audience Network',
            'msg' => 'Messenger',
            default => $value,
        };
    }

    public function hideFields()
    {
        return collect(config('marketing-data-tracker.hidden_marketing_parameters', []));
    }

    public function getMarketingParametersList($include_hidden = false, $format = true, $for_cookies = false): array
    {
        $allowed_parameters = MarketingDataTracker::getMarketingDataParameters();
        if ($for_cookies) {
            $allowed_parameters = MarketingDataTracker::getMarketingDataCookies();
        }
        $fields = collect($allowed_parameters)->values();

        if (! $include_hidden) {
            $fields = $fields->reject(function ($field) {
                return $this->hideFields()->contains($field);
            });
        }

        $fieldValues = $fields->mapWithKeys(function ($field) use ($format) {
            if (Str::of($field)->endsWith('*')) {
                $field_group = $field;
                $field_group = Str::of($field)->before('*')->beforeLast('_');
                if ($field_group->isEmpty()) {
                    $field_group = Str::of($field)->before('*');
                }
                if (Str::of($field_group)->startsWith('_')) {
                    $field_group = Str::of($field_group)->after('_');
                }
                $field = $field_group->toString();
            }

            $value = $this->$field ?? null;
            if (is_array($value)) {
                $values = collect($value)->mapWithKeys(function ($sub_value, $sub_field) use ($format) {
                    return $this->parseFieldValue($sub_field, $sub_value, $format);
                })->toArray();

                return $values;
            }

            return $this->parseFieldValue($field, $value, $format);
        })->toArray() ?? [];

        return $fieldValues;
    }

    public function parseFieldValue($field, $value, $format = true): array
    {

        if ($value && Str::startsWith($field, 'mm_')) {
            $value = match ($field) {
                'mm_matchtype' => $this->getMatchtype($value),
                'mm_network' => $this->getNetwork($value),
                'mm_device' => $this->getDevice($value),
                'mm_placement' => $this->getPlacement($value),
                default => $value,
            };
        }

        if (! $value) {
            return [];
        }

        if ($format) {
            $field = Str::of($field)
                ->replace('utm_', '')
                ->replace('mm_', '')
                ->replace('_', ' ')
                ->title()->toString();
        } else {
            $field = Str::of($field)->toString();
        }

        if ($value && is_string($value)) {
            $value = Str::of($value)->trim()->toString();
        }

        return [$field => $value];
    }

    public function getAllRawMarketingParametersAttribute()
    {
        return $this->getMarketingParametersList(true, false);
    }

    public function getAllMarketingParametersAttribute()
    {
        return $this->getMarketingParametersList(true, true);
    }

    public function getMarketingParameterListAttribute()
    {
        return $this->getMarketingParametersList(false, true);
    }

    public function getAllRawMarketingCookiesAttribute()
    {
        return $this->getMarketingParametersList(true, false, true);
    }

    public function getAllMarketingCookiesAttribute()
    {
        return $this->getMarketingParametersList(true, true, true);
    }

    public function getAllRawMarketingListAttribute()
    {
        $parameters = $this->all_raw_marketing_parameters;
        $cookies = $this->all_raw_marketing_cookies;
        $total = array_merge($parameters, $cookies);

        return $total;
    }

    public function getHasGoogleIdAttribute(): bool
    {
        return collect($this->all_raw_marketing_list)->contains(function ($value, $parameter) {
            $allowed = collect($this->getGoogleClickIdParameters());
            if ($allowed->contains($parameter)) {
                return true;
            }
        }) ?? false;
    }

    public function getGoogleIdsAttribute(): array
    {
        if ($this->hasGoogleId) {
            return collect($this->all_raw_marketing_list)->mapWithKeys(function ($value, $parameter) {
                $allowed = collect($this->getGoogleClickIdParameters());
                if ($allowed->contains($parameter)) {
                    return [$parameter => $value];
                }

                return [];
            })->toArray();
        }

        return [];
    }

    public function getHasMetaIdAttribute(): bool
    {
        return collect($this->all_raw_marketing_list)->contains(function ($value, $parameter) {
            $allowed = collect($this->getMetaClickIdParameters());
            if ($allowed->contains($parameter)) {
                return true;
            }
        }) ?? false;
    }

    public function getMetaIdsAttribute(): array
    {
        if ($this->hasMetaId) {
            return collect($this->all_raw_marketing_list)->mapWithKeys(function ($value, $parameter) {
                $allowed = collect($this->getMetaClickIdParameters());
                if ($allowed->contains($parameter)) {
                    return [$parameter => $value];
                }

                return [];
            })->toArray();
        }

        return [];
    }
}
