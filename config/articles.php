<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as NewsApi, GuardianApi, New York Times Api and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'newsapi' => [
        'key' => env('NEWS_API_KEY'),
    ],

    'nytimes' => [
        'key' => env('NYTIMES_API_KEY'),
    ],


    'guardians' => [
        'key' => env('GUARDIANS_API_KEY'),
    ],

    'rapidnewapi' => [
        'key' => env('RAPID_API_KEY'),
        'proxy' => env('RAPID_API_PROXY'),
        ],

    'unsplash' => [
        'key' => env('UNSPLASH_API_KEY'),
        'secret' => env('UNSPLASH_API_SECRET')
        ],
];
