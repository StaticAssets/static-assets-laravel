<?php

namespace StaticAssets;

use Exception;
use Illuminate\Support\HtmlString;

class Mix
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
            if (is_file($manifestPath)) {
                static::$manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
            }

            if (! isset(static::$manifests[$manifestPath]) && config('static-assets.manifest.save_method') === 'disk') {
                (new DownloadManifest)('mix');

                return $this->__invoke($path, $manifestDirectory);
            }

            if (config('static-assets.manifest.save_method') === 'cache') {
                $remotePath = 'https://manifests.staticassets.app/'.config('static-assets.release');

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

        return new HtmlString(app('config')->get('app.mix_url').$manifestDirectory.$manifest[$path]);
    }
}
