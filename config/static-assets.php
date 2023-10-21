<?php

return [
    'cdn' => 'https://cdn.staticassets.app',

    'is_enabled' => env('APP_ENV', 'production') === 'production',

    'release' => trim(exec('git --git-dir '.base_path('.git').' rev-parse HEAD')),

    'vite' => [
        'manifest_directory' => 'build',

        // options: disk or cache
        'manifest_save_method' => 'disk',
    ],
];
