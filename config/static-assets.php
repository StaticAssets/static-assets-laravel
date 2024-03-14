<?php

return [
    'is_enabled' => env('STATIC_ASSETS', env('APP_ENV', 'production') === 'production'),

    'release' => env('STATIC_ASSETS_RELEASE', trim(exec('git --git-dir '.base_path('.git').' rev-parse HEAD'))),

    // for both Vite & Laravel Mix
    'manifest' => [
        // custom directory where assets are normally stored
        // this is scoped to public_path()
        'custom_directory' => env('STATIC_ASSETS_DIRECTORY'),

        // options: disk or cache
        'save_method' => env('STATIC_ASSETS_STORAGE', 'disk'),

        // amount of days to save manifest in the cache
        'cache_days' => env('STATIC_ASSETS_CACHE_TIMEOUT', 30),
    ],
];
