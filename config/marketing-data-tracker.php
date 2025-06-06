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
        'tradetracker',
        'gclid',
        'gbraid',
        'wbraid',
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
        'horizon', // Laravel Horizon requests
        'telescope', // Laravel Telescope requests
        '_tt', // Laravel Telescope toolbar
        '_debugbar', // Laravel DebugBar requests
        'media', // Spatie Media requests
        'nova-api', // Nova Scripts requests
        'storage', // Nova styles requests
        'livewire', // Livewire requests
    ],
];
