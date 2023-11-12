<?php

namespace StaticAssets;

use Illuminate\Support\Facades\Http;

class DownloadManifest
{
    public function __invoke(string $type): void
    {
        $fileName = $type === 'vite' ? 'manifest.json' : 'mix-manifest.json';
        $defaultBuildDirectory = $type === 'vite' ? 'build' : '';
        $buildDirectory = public_path(config('static-assets.manifest.custom_directory') ?: $defaultBuildDirectory);

        // ensure there is the build directory
        if (! is_dir($buildDirectory)) {
            mkdir($buildDirectory);
        }

        Http::sink("{$buildDirectory}/{$fileName}")
            ->get('https://manifests.staticassets.app/'.config('static-assets.release'));
    }
}
