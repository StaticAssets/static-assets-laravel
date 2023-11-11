<?php

namespace StaticAssets;

use Illuminate\Support\Facades\Http;

class DownloadManifest
{
    public function __invoke(string $type): void
    {
        // ensure there is the build directory
        if (! is_dir(public_path(config('static-assets.manifest.directory')))) {
            mkdir(public_path(config('static-assets.manifest.directory')));
        }

        $fileName = $type === 'vite' ? 'manifest.json' : 'mix-manifest.json';

        Http::sink(public_path(config('static-assets.manifest.directory'))."/{$fileName}")
            ->get(sprintf(
                'https://staticassets.app/api/manifest/%s',
                config('static-assets.release'),
            ));
    }
}
