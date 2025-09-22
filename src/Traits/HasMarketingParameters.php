<?php

namespace Marshmallow\MarketingData\Traits;

use Exception;
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

    /**
     * Get all click ID parameters for all platforms
     */
    public function getAllClickIdParameters(): array
    {
        return [
            // Google Ads
            'gclid', 'gbraid', 'wbraid',
            // Facebook/Meta
            'fbclid',
            // Microsoft/Bing
            'msclkid',
            // LinkedIn
            'li_fat_id',
            // Twitter/X
            'twclid',
            // Pinterest
            'epik',
            // TikTok
            'ttclid',
            // Reddit
            'rdt_cid',
            // Snapchat
            'sscid',
        ];
    }

    /**
     * Get click ID parameters by platform
     */
    public function getClickIdParametersByPlatform(): array
    {
        return [
            'google_ads' => ['gclid', 'gbraid', 'wbraid'],
            'meta' => ['fbclid'],
            'microsoft' => ['msclkid'],
            'linkedin' => ['li_fat_id'],
            'twitter' => ['twclid'],
            'pinterest' => ['epik'],
            'tiktok' => ['ttclid'],
            'reddit' => ['rdt_cid'],
            'snapchat' => ['sscid'],
        ];
    }

    /**
     * Detect platform from click ID
     */
    public function detectPlatformFromClickId(string $clickId): ?string
    {
        $platformMappings = [
            'gclid' => 'google_ads',
            'gbraid' => 'google_ads',
            'wbraid' => 'google_ads',
            'fbclid' => 'meta',
            'msclkid' => 'microsoft',
            'li_fat_id' => 'linkedin',
            'twclid' => 'twitter',
            'epik' => 'pinterest',
            'ttclid' => 'tiktok',
            'rdt_cid' => 'reddit',
            'sscid' => 'snapchat',
        ];

        return $platformMappings[$clickId] ?? null;
    }

    /**
     * Detect platform from UTM source
     */
    public function detectPlatformFromUtmSource(?string $utmSource): ?string
    {
        if (!$utmSource) {
            return null;
        }

        $utmSource = mb_strtolower($utmSource);

        $sourceMappings = [
            // Google variants
            'google' => 'google_ads',
            'google-ads' => 'google_ads',
            'googleads' => 'google_ads',
            'adwords' => 'google_ads',

            // Facebook/Meta variants
            'facebook' => 'meta',
            'facebook_ads' => 'meta',
            'facebook-ads' => 'meta',
            'fb' => 'meta',
            'meta' => 'meta',
            'instagram' => 'meta',
            'ig' => 'meta',

            // Microsoft/Bing variants
            'bing' => 'microsoft',
            'microsoft' => 'microsoft',
            'bing-ads' => 'microsoft',
            'bingads' => 'microsoft',
            'msn' => 'microsoft',

            // LinkedIn variants
            'linkedin' => 'linkedin',
            'linkedin_ads' => 'linkedin',
            'li' => 'linkedin',

            // Twitter/X variants
            'twitter' => 'twitter',
            'twitter_ads' => 'twitter',
            'x' => 'twitter',
            'twitterads' => 'twitter',

            // Pinterest variants
            'pinterest' => 'pinterest',
            'pinterest_ads' => 'pinterest',
            'pin' => 'pinterest',

            // TikTok variants
            'tiktok' => 'tiktok',
            'tiktok_ads' => 'tiktok',
            'tiktokads' => 'tiktok',
            'tt' => 'tiktok',

            // Reddit variants
            'reddit' => 'reddit',
            'reddit_ads' => 'reddit',

            // Snapchat variants
            'snapchat' => 'snapchat',
            'snapchat_ads' => 'snapchat',
            'snap' => 'snapchat',

            // Amazon variants
            'amazon' => 'amazon',
            'amazon_ads' => 'amazon',
            'dsp' => 'amazon',

            // YouTube variants (separate from Google)
            'youtube' => 'youtube',
            'yt' => 'youtube',
        ];

        // Check exact matches first
        if (isset($sourceMappings[$utmSource])) {
            return $sourceMappings[$utmSource];
        }

        // Check partial matches
        foreach ($sourceMappings as $pattern => $platform) {
            if (str_contains($utmSource, $pattern)) {
                return $platform;
            }
        }

        return null;
    }

    /**
     * Detect platform from marketing data
     */
    public function detectPlatformFromMarketingData(array $marketingData = []): ?string
    {
        // Use model's marketing data if no array provided
        if (empty($marketingData) && method_exists($this, 'getAllRawMarketingParametersAttribute')) {
            $marketingData = $this->getAllRawMarketingParametersAttribute();
        }

        // First check for click IDs (most reliable)
        foreach ($this->getAllClickIdParameters() as $clickIdParam) {
            if (!empty($marketingData[$clickIdParam])) {
                $platform = $this->detectPlatformFromClickId($clickIdParam);
                if ($platform) {
                    return $platform;
                }
            }
        }

        // Then check UTM source
        $utmSource = $marketingData['utm_source'] ?? null;
        if ($utmSource) {
            return $this->detectPlatformFromUtmSource($utmSource);
        }

        return null;
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

        $casts->each(function ($class, $cast) use (&$casts): void {
            if (Str::endsWith($cast, '_*')) {
                $cast = Str::before($cast, '_*');
                $casts[$cast] = MarketingDataCast::class;
            }
        });

        $casts->each(function ($class, $cast) use (&$casts): void {
            if (Str::endsWith($cast, '*')) {
                $cast = Str::before($cast, '*');
                $casts[$cast] = MarketingDataCast::class;
            }
        });

        $casts->each(function ($class, $cast) use (&$casts): void {
            $field = Str::of($cast)->trim()->toString();
            if (!Str::of($field)->endsWith('*')) {
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

    public function setUtmSourceData($forget = true): void
    {
        try {
            $this->addUtmSessionData($forget);
            $this->addSourceData($forget);
            $this->addCookieData($forget);
        } catch (Exception $exception) {
            throw new Exception('Error setting Marketing data: '.$exception->getMessage());
        }
    }

    public function addCookieData($forget = true, $request = null): void
    {
        if (!$request) {
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

            if (is_array($source_values) && !empty($source_values)) {
                foreach ($source_values as $key => $value) {
                    if (!in_array($key, $allowed_parameters)) {
                        continue;
                    }
                    $this->{$key} = $value;
                }
                $this->updateQuietly($source_values);
            }
        }
    }

    public function addSourceData($forget = true, $request = null): void
    {
        if (!$request) {
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

            if (is_array($source_values) && !empty($source_values)) {
                foreach ($source_values as $key => $value) {
                    if (!in_array($key, $allowed_parameters)) {
                        continue;
                    }
                    $this->{$key} = $value;
                }
                $this->updateQuietly($source_values);
            }
        }
    }

    public function addUtmSessionData($forget = true): void
    {
        $session_key = 'mm_utm_values';
        if (session()->has($session_key)) {
            if ($forget) {
                $utm_values = session()->pull($session_key);
            } else {
                $utm_values = session()->get($session_key);
            }

            $allowed_parameters = MarketingDataTracker::getMarketingDataParameters();

            if (is_array($utm_values) && !empty($utm_values)) {
                foreach ($utm_values as $key => $value) {
                    if (!in_array($key, $allowed_parameters)) {
                        continue;
                    }
                    $this->{$key} = $value;
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
            // Google Ads networks
            'g' => 'Google Search',
            's' => 'Search partner',
            'd' => 'Display',
            'u' => 'Smart Shopping',
            'ytv' => 'Youtube',
            'vp' => 'Video Partner',

            // Facebook/Meta networks
            'fb' => 'Facebook',
            'ig' => 'Instagram',
            'an' => 'Audience Network',
            'msg' => 'Messenger',

            // Microsoft/Bing networks
            'o' => 'Bing Search',
            'partner' => 'Bing Partner',
            'content' => 'Microsoft Content Network',
            'audience' => 'Microsoft Audience Network',

            // LinkedIn networks
            'sponsored_content' => 'LinkedIn Sponsored Content',
            'message_ads' => 'LinkedIn Message Ads',
            'dynamic_ads' => 'LinkedIn Dynamic Ads',
            'text_ads' => 'LinkedIn Text Ads',

            // Twitter/X networks
            'timeline' => 'Twitter Timeline',
            'search' => 'Twitter Search',
            'profile' => 'Twitter Profile',

            // Pinterest networks
            'browse' => 'Pinterest Browse',
            'search_pinterest' => 'Pinterest Search',
            'shopping' => 'Pinterest Shopping',

            // TikTok networks
            'for_you' => 'TikTok For You',
            'following' => 'TikTok Following',
            'discover' => 'TikTok Discover',

            // Reddit networks
            'feed' => 'Reddit Feed',
            'conversation' => 'Reddit Conversation',

            // Snapchat networks
            'snap_ads' => 'Snapchat Ads',
            'story_ads' => 'Snapchat Story Ads',

            default => Str::title(str_replace('_', ' ', $value)),
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

        if (!$include_hidden) {
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

            $value = $this->{$field} ?? null;
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

        if (!$value) {
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

    /**
     * Check if model has any click IDs from all platforms
     */
    public function getHasAnyClickIdAttribute(): bool
    {
        return collect($this->all_raw_marketing_list)->contains(function ($value, $parameter) {
            $allowed = collect($this->getAllClickIdParameters());
            if ($allowed->contains($parameter)) {
                return true;
            }
        }) ?? false;
    }

    /**
     * Get all click IDs from all platforms
     */
    public function getAllClickIdsAttribute(): array
    {
        if ($this->hasAnyClickId) {
            return collect($this->all_raw_marketing_list)->mapWithKeys(function ($value, $parameter) {
                $allowed = collect($this->getAllClickIdParameters());
                if ($allowed->contains($parameter)) {
                    return [$parameter => $value];
                }

                return [];
            })->toArray();
        }

        return [];
    }

    /**
     * Get the primary Google Click ID using configurable priority
     * Uses priority order from config (default: gclid > wbraid > gbraid)
     */
    public function getPrimaryGoogleClickId(): ?string
    {
        $config = config('marketing-data-tracker.click_id_management.google_click_ids', []);

        if (!($config['enabled'] ?? false)) {
            return null;
        }

        $priority = $config['priority'] ?? ['gclid', 'wbraid', 'gbraid'];
        $cookieMapping = $config['cookie_mapping'] ?? [];
        $extractGclidValue = $config['extract_gclid_value'] ?? true;

        $allMarketingData = $this->getAllRawMarketingListAttribute();

        foreach ($priority as $key) {
            // Check both session value and cookie value
            $sessionValue = $allMarketingData[$key] ?? null;
            $cookieKey = $cookieMapping[$key] ?? null;
            $cookieValue = $cookieKey ? ($allMarketingData[$cookieKey] ?? null) : null;

            $value = $cookieValue ?? $sessionValue;

            if ($value && !empty(trim($value))) {
                // Special handling for gclid extraction
                if ($key === 'gclid' && $extractGclidValue && str_contains($value, '.')) {
                    $value = substr($value, strrpos($value, '.') + 1);
                }
                return $value;
            }
        }

        return null;
    }

    /**
     * Get the primary click ID from any platform (prioritized by platform importance)
     */
    public function getPrimaryClickIdAttribute(): ?string
    {
        $platformPriority = config('marketing-data-tracker.click_id_management.platform_priority', [
            'gclid' => 10,      // Google Ads - highest priority
            'fbclid' => 9,      // Facebook/Meta
            'msclkid' => 8,     // Microsoft/Bing
            'ttclid' => 7,      // TikTok
            'twclid' => 6,      // Twitter/X
            'li_fat_id' => 5,   // LinkedIn
            'epik' => 4,        // Pinterest
            'rdt_cid' => 3,     // Reddit
            'sscid' => 2,       // Snapchat
            'gbraid' => 1,      // Google iOS tracking
            'wbraid' => 1,      // Google iOS web-to-app
        ]);

        $allClickIds = $this->getAllClickIdsAttribute();

        if (empty($allClickIds)) {
            return null;
        }

        // Sort by priority and return the highest priority click ID
        $sortedClickIds = collect($allClickIds)
            ->sortByDesc(function ($value, $parameter) use ($platformPriority) {
                return $platformPriority[$parameter] ?? 0;
            });

        $primaryParam = $sortedClickIds->keys()->first();
        $primaryValue = $sortedClickIds->first();

        // Extract clean value (handle Google cookie format)
        return $this->extractClickIdValue($primaryValue, $primaryParam);
    }

    /**
     * Extract clean click ID value from cookie format
     */
    protected function extractClickIdValue(?string $value, string $parameter): ?string
    {
        if (!$value) {
            return null;
        }

        // Handle Google click ID extraction from cookie format
        $config = config('marketing-data-tracker.click_id_management.google_click_ids', []);
        $extractGclidValue = $config['extract_gclid_value'] ?? true;

        if ($parameter === 'gclid' && $extractGclidValue && str_contains($value, '.')) {
            return substr($value, strrpos($value, '.') + 1);
        }

        return trim($value);
    }

    /**
     * Get platform name from detected platform
     */
    public function getPlatformNameAttribute(): ?string
    {
        $platform = $this->detectPlatformFromMarketingData();

        $platformNames = [
            'google_ads' => 'Google Ads',
            'meta' => 'Meta/Facebook',
            'microsoft' => 'Microsoft Ads',
            'linkedin' => 'LinkedIn',
            'twitter' => 'Twitter/X',
            'pinterest' => 'Pinterest',
            'tiktok' => 'TikTok',
            'reddit' => 'Reddit',
            'snapchat' => 'Snapchat',
            'amazon' => 'Amazon',
            'youtube' => 'YouTube',
        ];

        return $platformNames[$platform] ?? $platform;
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
