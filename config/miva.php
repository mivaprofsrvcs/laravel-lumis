<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Miva Store Connection
    |--------------------------------------------------------------------------
    */

    'default' => env('MM_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Miva Store Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the Miva store connections your application
    | uses. Each connection contains both the API credentials and
    | store-specific settings for a single Miva store instance.
    |
    | You can configure multiple connections if your application integrates
    | with more than one store. The "default" connection will be used when
    | a specific connection name is not provided.
    |
    */

    'connections' => [
        'default' => [
            'api' => [
                'key' => env('MM_API_KEY'),
                'token' => env('MM_API_TOKEN'),
                'url' => env('MM_API_URL'),
                'verify_ssl' => (bool) env('MM_API_VERIFY_SSL', false),
                // Optional API client settings:
                // 'hmac' => 'sha256',
                // 'timeout' => 30,
                // 'binary_encoding' => 'base64',
                // 'range' => '1-100',
                // 'ssh_auth' => [
                //     'username' => env('MM_API_SSH_USERNAME'),
                //     'private_key' => env('MM_API_SSH_PRIVATE_KEY'),
                //     'algorithm' => 'sha256',
                // ],
                // 'http_client' => [
                //     // List of Guzzle options:
                //     // https://docs.guzzlephp.org/en/stable/request-options.html
                // ],
            ],
            'store' => [
                'auth' => [
                    'username' => env('MM_STORE_AUTH_USERNAME', ''),
                    'password' => env('MM_STORE_AUTH_PASSWORD', ''),
                ],
                'code' => env('MM_STORE_CODE'),
                'graphics_path' => env('MM_STORE_GRAPHICS_PATH', 'graphics/00000001/'),
                'root_path' => env('MM_STORE_ROOT_PATH', '/mm5/'),
                'url' => env('MM_STORE_URL'),
            ],
        ],

        // Add more named connections as needed:
        // 'store02' => [ 'api' => [...], 'store' => [...] ],
        // 'store03' => [ 'api' => [...], 'store' => [...] ],
    ],

];
