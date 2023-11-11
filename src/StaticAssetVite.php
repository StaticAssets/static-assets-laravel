<?php

namespace StaticAssets;

use Illuminate\Support\Str;
use Illuminate\Foundation\Vite;

class StaticAssetVite extends Vite
{
    protected function assetPath($path, $secure = null): string
    {
        return Str::of('https://cdn.staticassets.app')
            ->append('/')
            ->append($path)
            ->replace(
                config('static-assets.manifest.custom_directory') ?: 'build',
                config('static-assets.release')
            )
            ->toString();
    }

    protected function manifest($buildDirectory): array
    {
        $path = $this->manifestPath($buildDirectory);

        // save to the disk and then continue as normal
        if (! is_file($path) && config('static-assets.manifest.save_method') === 'disk') {
            (new DownloadManifest)('vite');

            return parent::manifest($buildDirectory);
        }

        if (! isset(static::$manifests[$path])) {
            $remotePath = sprintf(
                'https://staticassets.app/api/manifest/%s',
                config('static-assets.release')
            );

            static::$manifests[$path] = cache()->rememberForever($remotePath, function () use ($remotePath) {
                return json_decode(file_get_contents($remotePath), true);
            });
        }

        return parent::manifest($buildDirectory);
    }
}
