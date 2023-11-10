<?php

return [
    'cdn' => 'https://cdn.staticassets.app',

    'is_enabled' => env('APP_ENV', 'production') === 'production',

    'release' => trim(exec('git --git-dir '.base_path('.git').' rev-parse HEAD')),

    // for both Vite & Laravel Mix
    'manifest' => [
        // custom directory where assets are normally stored
        // this is scoped to public_path()
        'custom_directory' => null,

        // options: disk or cache
        'save_method' => 'disk',
    ],
];
