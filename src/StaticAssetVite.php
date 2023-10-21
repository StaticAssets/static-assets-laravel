<?php

namespace StaticAssets;

use Illuminate\Foundation\Vite;
use Illuminate\Support\Str;

class StaticAssetVite extends Vite
{
    protected function assetPath($path, $secure = null): string
    {
        return Str::of(config('static-assets.cdn'))
            ->append('/')
            ->append($path)
            ->replace(
                config('static-assets.vite.manifest_directory'),
                config('static-assets.release')
            )
            ->toString();
    }

    protected function manifest($buildDirectory): array
    {
        // save to the disk and then continue as normal
        if (config('static-assets.vite.manifest_save_method') === 'disk') {
            (new DownloadViteManifest)();

            return parent::manifest($buildDirectory);
        }

        $path = $this->manifestPath($buildDirectory);

        if (! isset(static::$manifests[$path])) {
            $remotePath = sprintf(
                '%s/%s/manifest.json',
                config('static-assets.cdn'),
                config('static-assets.release')
            );

            static::$manifests[$path] = cache()->rememberForever($remotePath, function () use ($remotePath) {
                return json_decode(file_get_contents($remotePath), true);
            });
        }

        return parent::manifest($buildDirectory);
    }
}
