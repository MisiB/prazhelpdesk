<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WorkOS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WorkOS authentication and SSO integration.
    | Get your credentials from: https://dashboard.workos.com/
    |
    */

    'api_key' => env('WORKOS_API_KEY', ''),
    'client_id' => env('WORKOS_CLIENT_ID', ''),
    
    /*
    |--------------------------------------------------------------------------
    | Redirect URI
    |--------------------------------------------------------------------------
    |
    | The callback URL where WorkOS will redirect after authentication.
    | Must be registered in your WorkOS dashboard.
    |
    */
    
    'redirect_uri' => env('WORKOS_REDIRECT_URI', env('APP_URL') . '/auth/workos/callback'),
    
    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    */
    
    'connection' => env('WORKOS_CONNECTION', null),
    'organization' => env('WORKOS_ORGANIZATION', null),
    'provider' => env('WORKOS_PROVIDER', null),
    
    /*
    |--------------------------------------------------------------------------
    | Session Settings
    |--------------------------------------------------------------------------
    */
    
    'session_lifetime' => env('WORKOS_SESSION_LIFETIME', 1440), // minutes
    
    /*
    |--------------------------------------------------------------------------
    | User Provisioning
    |--------------------------------------------------------------------------
    */
    
    'auto_create_users' => env('WORKOS_AUTO_CREATE_USERS', true),
    'auto_update_users' => env('WORKOS_AUTO_UPDATE_USERS', true),
    
    /*
    |--------------------------------------------------------------------------
    | Directory Sync
    |--------------------------------------------------------------------------
    */
    
    'directory_sync' => [
        'enabled' => env('WORKOS_DIRECTORY_SYNC_ENABLED', false),
        'webhook_secret' => env('WORKOS_DIRECTORY_WEBHOOK_SECRET', ''),
    ],

];






