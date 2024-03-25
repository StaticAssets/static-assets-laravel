<?php

namespace StaticAssets;

use Illuminate\Support\Str;

class Vite extends \Illuminate\Foundation\Vite
{
    protected function assetPath($path, $secure = null): string
    {
        if (Str::contains($path, 'vendor')) {
            return parent::assetPath($path, $secure);
        }

        return Str::of($path)
            ->replace($this->buildDirectory, '')
            ->trim('/')
            ->toString();
    }

    protected function manifest($buildDirectory): array
    {
        if (Str::contains($buildDirectory, 'vendor')) {
            return parent::manifest($buildDirectory);
        }

        $path = $this->manifestPath($buildDirectory);

        if (is_file($path)) {
            $manifest = json_decode(file_get_contents($path), true);

            // if this is not a static-assets manifest then
            // rename it and get the manifest again
            if (str(json_encode($manifest))->contains('cdn.staticassets.app')) {
                return static::$manifests[$path] = $manifest;
            }

            rename($path, "{$path}.old");
        }

        // save to the disk and then continue as normal
        if (config('static-assets.manifest.save_method') === 'disk') {
            DownloadManifest::make()
                ->forVite()
                ->save();

            return parent::manifest($buildDirectory);
        }

        if (config('static-assets.manifest.save_method') === 'cache') {
            if (! isset(static::$manifests[$path])) {
                return static::$manifests[$path] = cache()->remember(config('static-assets.release'), now()->addDays(config('static-assets.manifest.cache_days')), function () {
                    return DownloadManifest::make()
                        ->forVite()
                        ->toArray();
                });
            }
        }

        return parent::manifest($buildDirectory);
    }
}
