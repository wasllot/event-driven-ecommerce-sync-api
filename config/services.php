<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'prestashop' => [
        'source' => [
            'url' => env('PRESTASHOP_SOURCE_URL'),
            'key' => env('PRESTASHOP_SOURCE_KEY'),
        ],
        'client' => [
            'url' => env('PRESTASHOP_CLIENT_URL'),
            'key' => env('PRESTASHOP_CLIENT_KEY'),
        ],
    ],

    'api' => [
        'token' => env('API_ACCESS_TOKEN'),
    ],

];
