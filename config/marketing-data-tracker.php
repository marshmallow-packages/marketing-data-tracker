<?php

// config for Marshmallow/MarketingData
return [
    /*
     * The fully qualified class name of the marketing data model.
     */
    'marketing_data_model' => Marshmallow\MarketingData\Models\MarketingData::class,

    /*
     * The table name of the marketing data model.
     */
    'marketing_data_table_name' => 'mm_marketing_data',

    /*
     * The connection name of the marketing data model.
     */
    'marketing_data_db_connection' => null,

    /*
     * The fully qualified class name of the marketing data cast.
     */
    'marketing_data_cast' => Marshmallow\MarketingData\Casts\MarketingDataCast::class,

    'tracking_urls' => [
        'google_ads' => 'utm_source=google&utm_medium=cpc&utm_term={keyword}&utm_content={creative}&mm_campaignid={campaignid}&mm_adgroupid={adgroupid}&mm_feedid={feeditemid}&mm_position={adposition}&mm_linterest={loc_interest_ms}&mm_lphys={loc_physical_ms}&mm_matchtype={matchtype}&mm_network={network}&mm_device={device}&mm_devicemodel={devicemodel}&mm_creative={creative}&mm_keyword={keyword}&mm_placement={placement}&mm_targetid={targetid}&mm_random={random}&mm_aceid={aceid}&mm_version=G3&gclid={gclid}&utm_campaign={_campaign}&gad_source=1&gad_campaignid={campaignid}&gbraid={gbraid}&wbraid={wbraid}',
        'meta_ads' => 'utm_source=facebook_ads&utm_medium={{site_source_name}}&utm_campaign={{campaign.name}}&utm_content={{ad.name}}',
    ],

    /*
     * The parameters that should be stored.
     */
    'store_marketing_parameters' => [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'utm_id',
        'mm_campaignid',
        'mm_adgroupid',
        'mm_feedid',
        'mm_position',
        'mm_linterest',
        'mm_lphys',
        'mm_matchtype',
        'mm_network',
        'mm_device',
        'mm_devicemodel',
        'mm_creative',
        'mm_keyword',
        'mm_placement',
        'mm_targetid',
        'mm_version',
        'mm_random',
        'mm_aceid',
        'tradetracker',
        'gclid',
        'gbraid',
        'wbraid',
        'gad_source',
        'gad_campaignid',
        'gad_medium',
        'landing_url',
        'landing_full_url',
        'landing_path',
        'source_url',
        'source_path',
        'previous_url',
        'request_url',
        'referer_url',
        'gad_*', // Catch all for Google Ads parameters
    ],

    'store_marketing_cookies' => [
        '_fbp', // Facebook
        'fbp', // Facebook
        'fbc', // Facebook
        '_ga', // Google Analytics
        '_gcl_au',
        '_gcl_aw', // Google Ads click identifier
        '_gcl_gs',
        '_gcl_ag', // Google Ads gbraid
        '_gcl_gb', // Google Ads wbraid
        '_uetsid',
        '_uetvid',
        'ajs_anonymous_id', // Segment
        '_epik', // Pinterest
        '_ttp', // TikTok
        'ttclid', // TikTok click
        '_ga*', // Google Analytics session ID
        '_gcl*', // Google Ads IDs
    ],

    /*
     * The parameters that should are stored, but should be hidden.
     */
    'hidden_marketing_parameters' => [
        'mm_campaignid',
        'mm_adgroupid',
        'mm_feedid',
        'mm_linterest',
        'mm_lphys',
        'mm_creative',
        'mm_targetid',
        'mm_version',
        'gclid',
        'gbraid',
        'wbraid',
        'gad',
        'landing_full_url',
        'landing_path',
        'source_path',
        'source_url',
        'previous_url',
        'request_url',
    ],

    // The paths of app that should be ignored.
    'ignore_paths' => [
        'mm-store-marketing-cookies', // The route for storing marketing cookies
        'horizon', // Laravel Horizon requests
        'telescope', // Laravel Telescope requests
        '_tt', // Laravel Telescope toolbar
        '_debugbar', // Laravel DebugBar requests
        'media', // Media requests
        'nova-api', // Nova API requests
        'storage', // Nova styles requests
        'livewire', // Livewire requests
        'boost', // Laravel Boost requests
        '__clockwork', // Clockwork requests
        '_boost', // Laravel Boost requests
        'pulse', // Laravel Pulse requests
        'flux', // Flux requests
        'herd', // Herd profile requests
        'storage', // Storage requests
        'nova-vendor', // Nova vendor requests
        'sanctum', // Laravel Sanctum requests
        'oauth', // Laravel Passport requests
        'health', // Health check endpoint
        'ping', // Ping endpoint
        'status', // Status endpoint
        'up', // Up endpoint
        'broadcasting', // Broadcasting requests
        'pusher', // Pusher requests
        'password', // Password reset requests
        'dusk', // Laravel Dusk requests
        'octane', // Laravel Octane requests
    ],
];
