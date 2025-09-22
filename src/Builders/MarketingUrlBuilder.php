<?php

namespace Marshmallow\MarketingData\Builders;

class MarketingUrlBuilder
{
    protected string $baseUrl;
    protected array $parameters = [];

    public function __construct(string $url)
    {
        $this->baseUrl = $url;
    }

    /**
     * Create a new marketing URL builder instance
     */
    public static function make(string $url): self
    {
        return new self($url);
    }

    /**
     * Add UTM parameters to the URL
     */
    public function withUTM(
        ?string $source = null,
        ?string $medium = null,
        ?string $campaign = null,
        ?string $term = null,
        ?string $content = null,
        ?string $id = null
    ): self {
        if ($source) {
            $this->parameters['utm_source'] = $source;
        }
        if ($medium) {
            $this->parameters['utm_medium'] = $medium;
        }
        if ($campaign) {
            $this->parameters['utm_campaign'] = $campaign;
        }
        if ($term) {
            $this->parameters['utm_term'] = $term;
        }
        if ($content) {
            $this->parameters['utm_content'] = $content;
        }
        if ($id) {
            $this->parameters['utm_id'] = $id;
        }

        return $this;
    }

    /**
     * Add Google Ads tracking parameters using the template
     */
    public function withGoogleAds(array $customParams = []): self
    {
        $trackingUrl = config('marketing-data-tracker.tracking_urls.google_ads', '');

        if ($trackingUrl) {
            parse_str($trackingUrl, $defaultParams);
            $this->parameters = array_merge($this->parameters, $defaultParams, $customParams);
        }

        return $this;
    }

    /**
     * Add Meta/Facebook Ads tracking parameters using the template
     */
    public function withMetaAds(array $customParams = []): self
    {
        $trackingUrl = config('marketing-data-tracker.tracking_urls.meta_ads', '');

        if ($trackingUrl) {
            parse_str($trackingUrl, $defaultParams);
            $this->parameters = array_merge($this->parameters, $defaultParams, $customParams);
        }

        return $this;
    }

    /**
     * Add platform-specific parameters
     */
    public function withPlatform(string $platform, array $params = []): self
    {
        $platformConfig = config("marketing-data-tracker.platforms.{$platform}");

        if (!$platformConfig || !($platformConfig['enabled'] ?? false)) {
            return $this;
        }

        $allowedParams = $platformConfig['parameters'] ?? [];

        foreach ($params as $key => $value) {
            if (in_array($key, $allowedParams)) {
                $this->parameters[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Add Google Ads ValueTrack parameters
     */
    public function withGoogleValueTrack(array $customMappings = []): self
    {
        $defaultMappings = [
            'mm_campaignid' => '{campaignid}',
            'mm_adgroupid' => '{adgroupid}',
            'mm_keyword' => '{keyword}',
            'mm_matchtype' => '{matchtype}',
            'mm_network' => '{network}',
            'mm_device' => '{device}',
            'mm_placement' => '{placement}',
            'mm_creative' => '{creative}',
            'gclid' => '{gclid}',
            'gbraid' => '{gbraid}',
            'wbraid' => '{wbraid}',
        ];

        $mappings = array_merge($defaultMappings, $customMappings);
        $this->parameters = array_merge($this->parameters, $mappings);

        return $this;
    }

    /**
     * Add Meta/Facebook dynamic parameters
     */
    public function withMetaDynamicParams(array $customMappings = []): self
    {
        $defaultMappings = [
            'mm_campaignid' => '{{campaign.id}}',
            'mm_adgroupid' => '{{adset.id}}',
            'mm_creative' => '{{ad.id}}',
            'mm_placement' => '{{placement}}',
            'mm_network' => '{{site_source_name}}',
            'fbclid' => '{{fbclid}}',
        ];

        $mappings = array_merge($defaultMappings, $customMappings);
        $this->parameters = array_merge($this->parameters, $mappings);

        return $this;
    }

    /**
     * Add Microsoft/Bing Ads parameters
     */
    public function withMicrosoftAds(array $customParams = []): self
    {
        $defaultParams = [
            'utm_source' => 'bing',
            'utm_medium' => 'cpc',
            'msclkid' => '{msclkid}',
            'utm_mscampaign' => '{CampaignName}',
            'utm_msadgroup' => '{AdGroupName}',
            'utm_mskeyword' => '{keyword}',
        ];

        $this->parameters = array_merge($this->parameters, $defaultParams, $customParams);

        return $this;
    }

    /**
     * Add LinkedIn Ads parameters
     */
    public function withLinkedInAds(array $customParams = []): self
    {
        $defaultParams = [
            'utm_source' => 'linkedin',
            'utm_medium' => 'paid',
            'li_fat_id' => '{li_fat_id}',
        ];

        $this->parameters = array_merge($this->parameters, $defaultParams, $customParams);

        return $this;
    }

    /**
     * Add Twitter/X Ads parameters
     */
    public function withTwitterAds(array $customParams = []): self
    {
        $defaultParams = [
            'utm_source' => 'twitter',
            'utm_medium' => 'paid',
            'twclid' => '{twclid}',
        ];

        $this->parameters = array_merge($this->parameters, $defaultParams, $customParams);

        return $this;
    }

    /**
     * Add TikTok Ads parameters
     */
    public function withTikTokAds(array $customParams = []): self
    {
        $defaultParams = [
            'utm_source' => 'tiktok',
            'utm_medium' => 'paid',
            'ttclid' => '{ttclid}',
        ];

        $this->parameters = array_merge($this->parameters, $defaultParams, $customParams);

        return $this;
    }

    /**
     * Add Pinterest Ads parameters
     */
    public function withPinterestAds(array $customParams = []): self
    {
        $defaultParams = [
            'utm_source' => 'pinterest',
            'utm_medium' => 'paid',
            'epik' => '{epik}',
        ];

        $this->parameters = array_merge($this->parameters, $defaultParams, $customParams);

        return $this;
    }

    /**
     * Add custom parameters
     */
    public function withCustomParams(array $params): self
    {
        $this->parameters = array_merge($this->parameters, $params);

        return $this;
    }

    /**
     * Set a single parameter
     */
    public function withParameter(string $key, string $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * Remove a parameter
     */
    public function without(string $key): self
    {
        unset($this->parameters[$key]);

        return $this;
    }

    /**
     * Remove multiple parameters
     */
    public function withoutParameters(array $keys): self
    {
        foreach ($keys as $key) {
            unset($this->parameters[$key]);
        }

        return $this;
    }

    /**
     * Get current parameters
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Clear all parameters
     */
    public function clearParameters(): self
    {
        $this->parameters = [];

        return $this;
    }

    /**
     * Build the final URL with parameters
     */
    public function build(): string
    {
        if (empty($this->parameters)) {
            return $this->baseUrl;
        }

        // Remove empty values
        $cleanParams = array_filter($this->parameters, function ($value) {
            return $value !== null && $value !== '';
        });

        if (empty($cleanParams)) {
            return $this->baseUrl;
        }

        $separator = parse_url($this->baseUrl, PHP_URL_QUERY) ? '&' : '?';
        $queryString = http_build_query($cleanParams);

        return $this->baseUrl.$separator.$queryString;
    }

    /**
     * Convert to string (alias for build())
     */
    public function __toString(): string
    {
        return $this->build();
    }

    /**
     * Static method to create Google Ads URL
     */
    public static function googleAds(string $url, ?string $campaign = null): self
    {
        $builder = self::make($url)->withUTM('google', 'cpc', $campaign);

        return $builder->withGoogleAds();
    }

    /**
     * Static method to create Meta Ads URL
     */
    public static function metaAds(string $url, ?string $campaign = null): self
    {
        $builder = self::make($url)->withUTM('facebook', 'paid', $campaign);

        return $builder->withMetaAds();
    }

    /**
     * Static method to create Microsoft Ads URL
     */
    public static function microsoftAds(string $url, ?string $campaign = null): self
    {
        $builder = self::make($url)->withUTM('bing', 'cpc', $campaign);

        return $builder->withMicrosoftAds();
    }

    /**
     * Static method to create LinkedIn Ads URL
     */
    public static function linkedInAds(string $url, ?string $campaign = null): self
    {
        $builder = self::make($url)->withUTM('linkedin', 'paid', $campaign);

        return $builder->withLinkedInAds();
    }

    /**
     * Static method to create UTM URL
     */
    public static function utm(
        string $url,
        string $source,
        string $medium,
        ?string $campaign = null,
        ?string $term = null,
        ?string $content = null
    ): string {
        return self::make($url)
            ->withUTM($source, $medium, $campaign, $term, $content)
            ->build();
    }

    /**
     * Create URL from tracking template
     */
    public static function fromTemplate(string $url, string $templateKey, array $replacements = []): self
    {
        $template = config("marketing-data-tracker.tracking_urls.{$templateKey}", '');

        if (!$template) {
            return self::make($url);
        }

        // Replace placeholders in template
        foreach ($replacements as $placeholder => $value) {
            $template = str_replace("{{$placeholder}}", $value, $template);
        }

        // Parse template parameters
        parse_str($template, $params);

        return self::make($url)->withCustomParams($params);
    }
}
