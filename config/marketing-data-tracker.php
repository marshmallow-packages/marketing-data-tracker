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

    /*
     * Enhanced Platform Support System
     * Configure specific platforms with their parameters, cookies, and tracking templates
     */
    'platforms' => [
        'google_ads' => [
            'enabled' => true,
            'name' => 'Google Ads',
            'click_id_params' => ['gclid', 'gbraid', 'wbraid'],
            'click_id_cookies' => ['_gcl_aw', '_gcl_gb', '_gcl_ag'],
            'parameters' => [
                'gclid',
                'gbraid',
                'wbraid',
                'gad_source',
                'gad_campaignid',
                'gad_medium',
                'gclsrc',
                'dclid',
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
                'mm_aceid'
            ],
            'cookies' => [
                '_ga',
                '_gid',
                '_gat',
                '_gtag_UA_*',
                '_gtag_GA_*',
                '_gcl_au',
                '_gcl_aw',
                '_gcl_dc',
                '_gcl_gs',
                '_gcl_ag',
                '_gcl_gb',
                '_ga*',
                '_gcl*'
            ],
        ],
        'meta' => [
            'enabled' => true,
            'name' => 'Meta/Facebook',
            'click_id_params' => ['fbclid'],
            'click_id_cookies' => ['_fbp', 'fbc', '_fbc'],
            'parameters' => [
                'fbclid',
                'fb_source',
                'campaignid',
                'adsetid',
                'adid',
                'placement',
                'site_source',
                'version',
                'fb_action_ids',
                'fb_action_types',
                'fb_ref',
                'h_ad_id'
            ],
            'cookies' => ['_fbp', 'fbp', 'fbc', '_fbc', '_fbp*', '_fbc*'],
        ],
        'microsoft' => [
            'enabled' => true,
            'name' => 'Microsoft Ads',
            'click_id_params' => ['msclkid'],
            'click_id_cookies' => ['_uetmsclkid'],
            'parameters' => [
                'msclkid',
                'utm_mscampaign',
                'utm_msadgroup',
                'utm_msad',
                'utm_mskeyword',
                'utm_msnetwork',
                'utm_msdevice',
                'utm_msplacement',
                'ef_id'
            ],
            'cookies' => ['_uetsid', '_uetvid', '_uetmsclkid', '_uetvidtk', '_uet*'],
        ],
        'linkedin' => [
            'enabled' => true,
            'name' => 'LinkedIn',
            'click_id_params' => ['li_fat_id'],
            'click_id_cookies' => ['li_fat_id'],
            'parameters' => ['li_fat_id', 'utm_linkedin', 'utm_li_campaign', 'utm_li_creative', 'utm_li_format'],
            'cookies' => ['lidc', 'li_sugr', 'UserMatchHistory', 'AnalyticsSyncHistory', 'li_fat_id', 'li_*'],
        ],
        'twitter' => [
            'enabled' => true,
            'name' => 'Twitter/X',
            'click_id_params' => ['twclid'],
            'click_id_cookies' => [],
            'parameters' => ['twclid', 'tw_campaign', 'tw_creative', 'tw_placement', 'tw_adformat', 'utm_twitter'],
            'cookies' => ['twid', 'auth_token', 'guest_id', 'personalization_id'],
        ],
        'pinterest' => [
            'enabled' => true,
            'name' => 'Pinterest',
            'click_id_params' => ['epik'],
            'click_id_cookies' => ['_epik', '_derived_epik'],
            'parameters' => ['epik', 'utm_pinterest', 'utm_pin_campaign', 'utm_pin_group', 'utm_pin_ad', 'pp'],
            'cookies' => ['_epik', '_pin_unauth', '_pinterest_sess', '_pinterest_ct_ua', '_derived_epik', '_epik*'],
        ],
        'tiktok' => [
            'enabled' => true,
            'name' => 'TikTok',
            'click_id_params' => ['ttclid'],
            'click_id_cookies' => ['ttclid'],
            'parameters' => ['ttclid', 'tt_campaign', 'tt_content', 'tt_medium', 'tt_term'],
            'cookies' => ['_ttp', 'tt_appInfo', 'tt_sessionId', 'ttclid', 'tt_chain_token', '_ttp*'],
        ],
        'reddit' => [
            'enabled' => true,
            'name' => 'Reddit',
            'click_id_params' => ['rdt_cid'],
            'click_id_cookies' => [],
            'parameters' => ['rdt_cid', 'utm_reddit'],
            'cookies' => ['rdt_user_id', 'reddit_session'],
        ],
        'snapchat' => [
            'enabled' => true,
            'name' => 'Snapchat',
            'click_id_params' => ['sscid'],
            'click_id_cookies' => [],
            'parameters' => ['sscid', 'utm_snapchat'],
            'cookies' => ['_scid', '_sctr'],
        ],
        'amazon' => [
            'enabled' => true,
            'name' => 'Amazon DSP',
            'click_id_params' => [],
            'click_id_cookies' => [],
            'parameters' => ['maas', 'utm_amazon'],
            'cookies' => ['ad-id', 'ad-privacy'],
        ],
        'tradetracker' => [
            'enabled' => true,
            'name' => 'TradeTracker',
            'click_id_params' => ['ttid'],
            'click_id_cookies' => [],
            'parameters' => ['tradetracker', 'tt', 'ttid', 'tm_campaign', 'tm_source'],
            'cookies' => [],
        ],
        'email_marketing' => [
            'enabled' => true,
            'name' => 'Email Marketing',
            'click_id_params' => [],
            'click_id_cookies' => [],
            'parameters' => ['mc_cid', 'mc_eid', 'utm_email', 'email_source', 'list_id'],
            'cookies' => [],
        ],
    ],

    /*
     * Click ID Management System
     * Configure priority-based click ID detection and extraction
     */
    'click_id_management' => [
        'enabled' => true,
        'google_click_ids' => [
            'enabled' => true,
            'priority' => ['gclid', 'wbraid', 'gbraid'],
            'cookie_mapping' => [
                'gclid' => '_gcl_aw',
                'wbraid' => '_gcl_gb',
                'gbraid' => '_gcl_ag',
            ],
            'extract_gclid_value' => true, // Extract actual ID from gclid cookie format
        ],
        'platform_priority' => [
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
        ],
    ],

    /*
     * Wildcard Pattern Support
     * Support for matching parameter and cookie patterns like _ga*, utm_*, etc.
     */
    'wildcard_patterns' => [
        'enabled' => true,
        'parameter_patterns' => ['gad_*', 'mm_*', 'utm_*', 'tm_*', 'tw_*', 'tt_*'],
        'cookie_patterns' => ['_ga*', '_gcl*', '_fbp*', '_fbc*', '_ttp*', '_epik*', '_uet*', 'li_*'],
    ],

    'tracking_urls' => [
        'google_ads' => 'utm_source=google&utm_medium=cpc&utm_term={keyword}&utm_content={creative}&mm_campaignid={campaignid}&mm_adgroupid={adgroupid}&mm_feedid={feeditemid}&mm_position={adposition}&mm_linterest={loc_interest_ms}&mm_lphys={loc_physical_ms}&mm_matchtype={matchtype}&mm_network={network}&mm_device={device}&mm_devicemodel={devicemodel}&mm_creative={creative}&mm_keyword={keyword}&mm_placement={placement}&mm_targetid={targetid}&mm_random={random}&mm_aceid={aceid}&mm_version=G3&gclid={gclid}&utm_campaign={_campaign}&gad_source=1&gad_campaignid={campaignid}&gbraid={gbraid}&wbraid={wbraid}',

        'meta_ads' => 'utm_source=facebook&utm_medium=paid&utm_campaign={{campaign.name}}&utm_content={{ad.name}}&utm_term={{adset.name}}&mm_campaignid={{campaign.id}}&mm_adgroupid={{adset.id}}&mm_creative={{ad.id}}&mm_network={{site_source_name}}&mm_placement={{placement}}&mm_version=FB1&fbclid={{fbclid}}&fb_source=1',
    ],

    /*
     * The parameters that should be stored.
     */
    'store_marketing_parameters' => [
        // Standard UTM parameters
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'utm_id',

        // Google Ads parameters
        'gclid', // Google Click ID
        'gbraid', // iOS click ID for Google Ads
        'wbraid', // iOS web-to-app click ID
        'gad_source',
        'gad_campaignid',
        'gad_medium',
        'gclsrc', // Google Click Source
        'dclid', // DoubleClick Click ID
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

        // Facebook/Meta/Instagram parameters
        'fbclid', // Facebook Click ID
        'fb_source',
        'campaignid',
        'adsetid',
        'adid',
        'placement',
        'site_source',
        'version',
        'fb_action_ids',
        'fb_action_types',
        'fb_ref',
        'h_ad_id', // Meta Advantage+ Shopping

        // Microsoft/Bing Ads parameters
        'msclkid', // Microsoft Click ID
        'utm_mscampaign',
        'utm_msadgroup',
        'utm_msad',
        'utm_mskeyword',
        'utm_msnetwork',
        'utm_msdevice',
        'utm_msplacement',
        'ef_id', // Bing Ads tracking

        // LinkedIn parameters
        'li_fat_id', // LinkedIn First-party Ad Tracking
        'utm_linkedin',
        'utm_li_campaign',
        'utm_li_creative',
        'utm_li_format',

        // X/Twitter parameters
        'twclid', // Twitter Click ID
        'tw_campaign',
        'tw_creative',
        'tw_placement',
        'tw_adformat',
        'utm_twitter',

        // Pinterest parameters
        'epik', // Pinterest Click ID
        'utm_pinterest',
        'utm_pin_campaign',
        'utm_pin_group',
        'utm_pin_ad',
        'pp', // Pinterest parameter

        // TikTok parameters
        'ttclid', // TikTok Click ID
        'tt_campaign',
        'tt_content',
        'tt_medium',
        'tt_term',

        // Reddit parameters
        'rdt_cid', // Reddit Click ID
        'utm_reddit',

        // Snapchat parameters
        'sscid', // Snapchat Click ID
        'utm_snapchat',

        // Amazon parameters
        'maas', // Amazon Advertising
        'utm_amazon',

        // TradeTracker parameters
        'tradetracker',
        'tt', // TradeTracker ID
        'ttid',
        'tm_campaign',
        'tm_source',

        // Affiliate & referral parameters
        'ref',
        'referrer',
        'affiliate_id',
        'partner_id',
        'source_id',
        'campaign_id',

        // Email marketing parameters
        'mc_cid', // Mailchimp Campaign ID
        'mc_eid', // Mailchimp Email ID
        'utm_email',
        'email_source',
        'list_id',

        // General tracking parameters
        'landing_url',      // Landing page URL (without query parameters)
        'landing_full_url', // Landing page URL (with all query parameters)
        'landing_path',     // Landing page path (without domain)
        'source_url',       // Source URL (landing page with marketing parameters)
        'source_path',      // Source path (landing page path with marketing parameters)
        'previous_url',     // Previous page URL from Laravel session
        'request_url',      // Current request URL (without query parameters)
        'referer_url',      // HTTP referer (where user came from)

        // Catch-all patterns
        'gad_*', // Google Ads parameters
        'mm_fb_*', // Facebook Ads parameters
        'utm_*', // All UTM parameters
    ],

    'store_marketing_cookies' => [
        // Facebook/Meta/Instagram cookies
        '_fbp', // Facebook Browser ID
        'fbp', // Facebook Browser ID (alternative)
        'fbc', // Facebook Click ID cookie
        '_fbc', // Facebook Click ID cookie (alternative)

        // Google Analytics & Ads cookies
        '_ga', // Google Analytics Client ID
        '_gid', // Google Analytics Session ID
        '_gat', // Google Analytics throttling
        '_gtag_UA_*', // Google Analytics Universal
        '_gtag_GA_*', // Google Analytics 4
        '_gcl_au', // Google Ads User ID
        '_gcl_aw', // Google Ads click identifier
        '_gcl_dc', // DoubleClick Click ID
        '_gcl_gs', // Google Shopping
        '_gcl_ag', // Google Ads gbraid
        '_gcl_gb', // Google Ads wbraid

        // Microsoft/Bing cookies
        '_uetsid', // Microsoft UET Session ID
        '_uetvid', // Microsoft UET Visitor ID
        '_uetmsclkid', // Microsoft Click ID
        '_uetvidtk', // Microsoft UET tracking

        // LinkedIn cookies
        'lidc', // LinkedIn Data Center
        'li_sugr', // LinkedIn Sugar cookie
        'UserMatchHistory', // LinkedIn user match
        'AnalyticsSyncHistory', // LinkedIn analytics sync
        'li_fat_id', // LinkedIn First-party Ad Tracking

        // X/Twitter cookies
        'twid', // Twitter ID
        'auth_token', // Twitter auth
        'guest_id', // Twitter guest
        'personalization_id', // Twitter personalization

        // Pinterest cookies
        '_epik', // Pinterest Enhanced Match
        '_pin_unauth', // Pinterest unauthenticated
        '_pinterest_sess', // Pinterest session
        '_pinterest_ct_ua', // Pinterest user agent
        '_derived_epik', // Pinterest derived Enhanced Match

        // TikTok cookies
        '_ttp', // TikTok Pixel
        'tt_appInfo', // TikTok app info
        'tt_sessionId', // TikTok session
        'ttclid', // TikTok click ID
        'tt_chain_token', // TikTok chain token

        // Reddit cookies
        'rdt_user_id', // Reddit user ID
        'reddit_session', // Reddit session

        // Snapchat cookies
        '_scid', // Snapchat Pixel ID
        '_sctr', // Snapchat tracking

        // Amazon cookies
        'ad-id', // Amazon advertising ID
        'ad-privacy', // Amazon privacy

        // Segment analytics
        'ajs_anonymous_id', // Segment anonymous ID
        'ajs_user_id', // Segment user ID
        'ajs_group_id', // Segment group ID

        // General tracking cookies
        'referrer_url',
        'landing_page',
        'session_id',
        'visitor_id',
        'tracking_id',

        // Catch-all patterns
        '_ga*', // Google Analytics cookies
        '_gcl*', // Google Ads IDs
        '_fbp*', // Facebook pixel cookies
        '_fbc*', // Facebook click cookies
        '_ttp*', // TikTok pixel cookies
        '_epik*', // Pinterest cookies
        '_uet*', // Microsoft UET cookies
        'li_*', // LinkedIn cookies
    ],

    /*
     * The parameters that should are stored, but should be hidden.
     */
    'hidden_marketing_parameters' => [
        // Google Ads internal tracking
        'mm_campaignid',
        'mm_adgroupid',
        'mm_feedid',
        'mm_linterest',
        'mm_lphys',
        'mm_creative',
        'mm_targetid',
        'mm_version',
        'mm_random',
        'mm_aceid',
        'gclsrc',
        'dclid',

        // Facebook/Meta internal tracking
        'campaignid',
        'adsetid',
        'adid',
        'placement',
        'site_source',
        'version',
        'fb_action_ids',
        'fb_action_types',
        'h_ad_id',

        // Microsoft/Bing internal tracking
        'ef_id',
        'utm_mscampaign',
        'utm_msadgroup',
        'utm_msad',
        'utm_mskeyword',
        'utm_msnetwork',
        'utm_msdevice',
        'utm_msplacement',

        // LinkedIn internal tracking
        'li_fat_id',
        'utm_li_campaign',
        'utm_li_creative',
        'utm_li_format',

        // X/Twitter internal tracking
        'tw_campaign',
        'tw_creative',
        'tw_placement',
        'tw_adformat',

        // Pinterest internal tracking
        'utm_pin_campaign',
        'utm_pin_group',
        'utm_pin_ad',
        'pp',

        // TikTok internal tracking
        'tt_campaign',
        'tt_content',
        'tt_medium',
        'tt_term',

        // Reddit internal tracking
        'rdt_cid',

        // Snapchat internal tracking
        'sscid',

        // Amazon internal tracking
        'maas',

        // TradeTracker internal tracking
        'ttid',
        'tm_campaign',
        'tm_source',

        // Email marketing internal tracking
        'mc_cid',
        'mc_eid',
        'list_id',

        // General internal tracking (keep visible: gclid, fbclid, msclkid, twclid, epik, ttclid)
        'gad',
        'gclid',
        'gbraid',
        'wbraid',
        'fbclid',
        'msclkid',
        'twclid',
        'epik',
        'ttclid',

        // URL tracking (internal use)
        'landing_full_url', // Landing page URL (with all query parameters)
        'landing_path',     // Landing page path (without domain)
        'source_path',      // Source path (landing page path with marketing parameters)
        'source_url',       // Source URL (landing page with marketing parameters)
        'previous_url',     // Previous page URL from Laravel session
        'request_url',      // Current request URL (without query parameters)
        'affiliate_id',
        'partner_id',
        'source_id',
        'campaign_id',
    ],

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
        'api\/webhooks', // Webhook endpoints
        'api\/health', // API health checks
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

    /*
     * Enhanced Event System
     * Configure package events and listeners
     */
    'events' => [
        'enabled' => true,
        'listeners' => [
            // 'Marshmallow\\MarketingData\\Events\\MarketingDataCreated' => [],
            // 'Marshmallow\\MarketingData\\Events\\MarketingDataUpdated' => [],
            // 'Marshmallow\\MarketingData\\Events\\ConversionTracked' => [],
            // 'Marshmallow\\MarketingData\\Events\\ClickIdDetected' => [],
        ],
    ],

    /*
     * Observer System Configuration
     * Automatic model observation and UTM data setting
     */
    'observers' => [
        'enabled' => false,
        'models' => [
            // App\Models\Lead::class,
            // App\Models\Order::class,
        ],
        'auto_set_utm' => true,
        'auto_detect_click_ids' => true,
        'forget_after_save' => true,
    ],

    /*
     * Cookie Management Configuration
     * Advanced cookie tracking with consent management
     */
    'cookie_management' => [
        'enabled' => true,
        'groups' => [
            'analytics' => [
                'cookies' => ['_ga', '_gid', '_ga_*'],
                'required' => false,
                'description' => 'Analytics and performance tracking',
            ],
            'advertising' => [
                'cookies' => ['_gcl_*', '_fbp', 'fbc', 'ttclid', '_epik', '_uet*'],
                'required' => false,
                'description' => 'Advertising and marketing attribution',
            ],
            'functional' => [
                'cookies' => ['session_id'],
                'required' => true,
                'description' => 'Essential website functionality',
            ],
        ],
        'consent' => [
            'enabled' => false,
            'cookie_name' => 'cookie_consent',
            'respect_consent' => true,
            'default_consent' => [
                'functional' => true,
                'analytics' => false,
                'advertising' => false,
            ],
        ],
        'wildcard_support' => true,
        'auto_register_exceptions' => true, // Automatically add marketing cookies to EncryptCookies exceptions
    ],

    /*
     * Conversion Tracking Framework
     * Configure conversion types and tracking
     */
    'conversions' => [
        'enabled' => false,
        'types' => [
            'lead' => ['value' => null, 'priority' => 1, 'description' => 'Lead generation'],
            'qualified_lead' => ['value' => null, 'priority' => 2, 'description' => 'Qualified lead'],
            'converted_lead' => ['value' => null, 'priority' => 3, 'description' => 'Converted lead'],
            'purchase' => ['value' => null, 'priority' => 5, 'description' => 'Purchase conversion'],
        ],
        'auto_track' => false,
        'track_value' => true,
    ],

    /*
     * E-commerce Tracking Configuration
     * Product and transaction tracking
     */
    'ecommerce' => [
        'enabled' => false,
        'currency' => 'EUR',
        'events' => [
            'view_item' => true,
            'add_to_cart' => true,
            'remove_from_cart' => true,
            'begin_checkout' => true,
            'purchase' => true,
        ],
        'gtm_format' => true,
        'platform_formats' => [
            'google_ads' => true,
            'meta' => true,
        ],
    ],
];
