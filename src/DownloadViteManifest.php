<?php

namespace StaticAssets;

use Illuminate\Support\Facades\Http;

class DownloadViteManifest
{
    public function __invoke(): void
    {
        // ensure there is the build directory
        if (! is_dir(public_path(config('static-assets.vite.manifest_directory')))) {
            mkdir(public_path(config('static-assets.vite.manifest_directory')));
        }

        Http::sink(public_path(config('static-assets.vite.manifest_directory')).'/manifest.json')
            ->get(sprintf(
                '%s/%s/manifest.json',
                config('static-assets.cdn'),
                config('static-assets.release')
            ));
    }
}
