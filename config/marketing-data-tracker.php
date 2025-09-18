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

        'meta_ads' => 'utm_source=facebook&utm_medium=paid&utm_campaign={{campaign.name}}&utm_content={{ad.name}}&utm_term={{adset.name}}&mm_campaignid={{campaign.id}}&mm_adgroupid={{adset.id}}&mm_creative={{ad.id}}&mm_network={{site_source_name}}&mm_placement={{placement}}&mm_version=FB1&fbclid={{fbclid}}&fb_source=1',
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
        'fbclid',
        'fb_source',
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
        'mm_fb_*', // Catch all for Facebook Ads parameters
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
        'fbclid',
        'gad',
        'landing_full_url',
        'landing_path',
        'source_path',
        'source_url',
        'previous_url',
        'request_url',
    ],

    // The paths of app that should be ignored.
    // The paths of app that should be ignored.
    'ignore_paths' => [
        // Marketing tracking routes
        'mm-store-marketing-cookies', // The route for storing marketing cookies

        // Laravel development tools
        'horizon', // Laravel Horizon requests
        'telescope', // Laravel Telescope requests
        '_tt', // Laravel Telescope toolbar
        '_debugbar', // Laravel DebugBar requests
        'boost', // Laravel Boost requests
        '__clockwork', // Clockwork requests
        '_boost', // Laravel Boost requests
        'pulse', // Laravel Pulse requests
        'flux', // Flux requests
        'octane', // Laravel Octane requests
        'dusk', // Laravel Dusk requests

        // Laravel Nova
        'nova-api', // Nova API requests
        'nova-vendor', // Nova vendor requests
        'nova', // Nova admin panel

        // Media and assets
        'media', // Media requests
        'storage', // Storage requests
        'assets', // Asset requests
        'css', // CSS files
        'js', // JavaScript files
        'images', // Image files
        'fonts', // Font files
        'favicon.ico', // Favicon
        'robots.txt', // Robots file
        'sitemap.xml', // Sitemap

        // Laravel services
        'livewire', // Livewire requests
        'sanctum', // Laravel Sanctum requests
        'oauth', // Laravel Passport requests
        'broadcasting', // Broadcasting requests
        'pusher', // Pusher requests
        'password', // Password reset requests

        // Health and monitoring
        'health', // Health check endpoint
        'ping', // Ping endpoint
        'status', // Status endpoint
        'up', // Up endpoint
        'ready', // Ready endpoint
        'metrics', // Metrics endpoint

        // Development and hosting
        'herd', // Herd profile requests
        '.well-known', // Well-known directory
        'phpinfo', // PHP info
        'debug', // Debug endpoints
        'test', // Test endpoints

        // API endpoints that shouldn't track marketing
        'api/webhooks', // Webhook endpoints
        'api/health', // API health checks
        'webhooks', // Alternative webhook path
        'cron', // Cron job endpoints
        'queue', // Queue processing

        // Third-party integrations
        'stripe', // Stripe webhooks
        'paypal', // PayPal webhooks
        'mailchimp', // Mailchimp webhooks
        'analytics', // Analytics endpoints
        'tracking', // Tracking pixels
        'pixel', // Tracking pixels

        // Security and bots
        'login', // Login pages (usually not marketing)
        'logout', // Logout pages
        'admin', // Admin panel
        '.env', // Environment file
        'wp-admin', // WordPress admin (bot traffic)
        'wp-login', // WordPress login (bot traffic)
        'xmlrpc.php', // WordPress XML-RPC (bot traffic)
    ],
];
