<?php

namespace StaticAssets;

use Illuminate\Support\Str;

class Vite extends \Illuminate\Foundation\Vite
{
    protected function assetPath($path, $secure = null): string
    {
        return Str::of("$path")
            ->replace($this->buildDirectory, '')
            ->trim('/')
            ->toString();
    }

    protected function manifest($buildDirectory): array
    {
        $buildDirectory = 'build';

        $path = $this->manifestPath($buildDirectory);

        // save to the disk and then continue as normal
        if (! is_file($path) && config('static-assets.manifest.save_method') === 'disk') {
            (new DownloadManifest)('vite');

            return parent::manifest($buildDirectory);
        }

        if (config('static-assets.manifest.save_method') === 'cache') {
            if (! isset(static::$manifests[$path])) {
                $remotePath = 'https://manifests.staticassets.app/'.config('static-assets.release');

                static::$manifests[$path] = cache()->rememberForever($remotePath, function () use ($remotePath) {
                    return json_decode(file_get_contents($remotePath), true);
                });
            }
        }

        return parent::manifest($buildDirectory);
    }
}
