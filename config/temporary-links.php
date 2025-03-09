<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Link Expiration Time
    |--------------------------------------------------------------------------
    |
    | Default time in minutes after which links will expire.
    |
    */
    'default_expiration' => 1440, // 24 hours

    /*
    |--------------------------------------------------------------------------
    | Default Single-Use Setting
    |--------------------------------------------------------------------------
    |
    | Default setting for whether links should be single-use.
    |
    */
    'default_single_use' => false,

    /*
    |--------------------------------------------------------------------------
    | IP Validation
    |--------------------------------------------------------------------------
    |
    | Enable IP validation by default
    |
    */
    'validate_ip' => false,

    /*
    |--------------------------------------------------------------------------
    | Device Validation
    |--------------------------------------------------------------------------
    |
    | Enable device validation by default
    |
    */
    'validate_device' => false,

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    |
    | Configure webhook settings for link access notifications
    |
    */
    'webhooks' => [
        'enabled' => false,
        'url' => null,
        'secret' => null,
        'events' => ['accessed', 'expired']
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes used for accessing temporary links
    |
    */
    'routes' => [
        'prefix' => 'temp-links',
        'middleware' => ['web'],
    ]
];