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
        'landing_url',
        'landing_full_url',
        'landing_path',
        'source_url',
        'source_path',
        'previous_url',
        'request_url',
        'referer_url',

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
        'landing_full_url',
        'landing_path',
        'source_path',
        'source_url',
        'previous_url',
        'request_url',
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
