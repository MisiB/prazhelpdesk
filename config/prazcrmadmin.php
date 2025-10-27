<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PrazCRM Admin API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the PrazCRM Admin API integration.
    | Make sure to set the appropriate values in your .env file.
    |
    */

    'url' => env('PRAZCRMADMIN_API_URL', 'https://prazcrmadmin.example.com'),
    'api_key' => env('PRAZCRMADMIN_API_KEY', ''),
    'timeout' => env('PRAZCRMADMIN_API_TIMEOUT', 30),
    
    /*
    |--------------------------------------------------------------------------
    | Sync Settings
    |--------------------------------------------------------------------------
    */
    
    'sync' => [
        'enabled' => env('PRAZCRMADMIN_SYNC_ENABLED', true),
        'auto_sync_tickets' => env('PRAZCRMADMIN_AUTO_SYNC_TICKETS', true),
        'sync_interval' => env('PRAZCRMADMIN_SYNC_INTERVAL', 300), // seconds
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    */
    
    'webhook' => [
        'enabled' => env('PRAZCRMADMIN_WEBHOOK_ENABLED', false),
        'secret' => env('PRAZCRMADMIN_WEBHOOK_SECRET', ''),
    ],

];

