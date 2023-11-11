<?php

namespace StaticAssets;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class StaticAssetMix
{
    public static array $manifests = [];

    public function __invoke($path, $manifestDirectory = '')
    {
        if (! str_starts_with($path, '/')) {
            $path = "/{$path}";
        }

        if ($manifestDirectory && ! str_starts_with($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        $manifestPath = public_path($manifestDirectory.'/mix-manifest.json');

        if (! isset(static::$manifests[$manifestPath])) {
            if (! is_file($manifestPath) && config('static-assets.manifest.save_method') === 'disk') {
                (new DownloadManifest)('mix');

                static::$manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);

                return $this->__invoke($path, $manifestDirectory);
            }

            if (config('static-assets.manifest.save_method') === 'cache') {
                $remotePath = sprintf(
                    '%s/%s/mix-manifest.json',
                    'https://cdn.staticassets.app',
                    config('static-assets.release')
                );

                static::$manifests[$manifestPath] = cache()->rememberForever($remotePath, function () use ($remotePath) {
                    return json_decode(file_get_contents($remotePath), true);
                });

                return $this->__invoke($path, $manifestDirectory);
            }

            if (! isset(static::$manifests[$manifestPath])) {
                throw new Exception("Mix manifest not found at: {$manifestPath}");
            }
        }

        $manifest = static::$manifests[$manifestPath];

        $path = "/public{$path}";

        if (! isset($manifest[$path])) {
            $exception = new Exception("Unable to locate Mix file: {$path}.");

            if (! app('config')->get('app.debug')) {
                report($exception);

                return $path;
            } else {
                throw $exception;
            }
        }

        return Str::of('https://cdn.staticassets.app')
            ->append('/')
            ->append(config('static-assets.release'))
            ->append(new HtmlString(app('config')->get('app.mix_url').$manifestDirectory.$manifest[$path]))
            ->replace(
                (config('static-assets.manifest.custom_directory') ?: 'public').config('static-assets.release'),
                config('static-assets.release')
            )
            ->toString();
    }
}
