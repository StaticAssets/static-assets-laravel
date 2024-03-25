<?php

namespace StaticAssets;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class Mix extends \Illuminate\Foundation\Mix
{
    public static array $manifests = [];

    public function __invoke($path, $manifestDirectory = '')
    {
        if (Str::contains($manifestDirectory, 'vendor')) {
            return parent::__invoke($path, $manifestDirectory);
        }

        if ($manifestDirectory && ! str_starts_with($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        $manifestPath = public_path($manifestDirectory.'/mix-manifest.json');

        if (! isset(static::$manifests[$manifestPath])) {
            // file exists load into memory
            if (is_file($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);

                // if this is not a static-assets manifest then
                // rename it and get the manifest again
                if (str(json_encode(static::$manifests[$manifestPath]))->contains('cdn.staticassets.app')) {
                    static::$manifests[$manifestPath] = $manifest;
                } else {
                    rename($manifestPath, "{$manifestPath}.old");
                }
            }

            if (! isset(static::$manifests[$manifestPath]) && config('static-assets.manifest.save_method') === 'disk') {
                DownloadManifest::make()
                    ->forMix()
                    ->save();

                return $this->__invoke($path, $manifestDirectory);
            }

            if (config('static-assets.manifest.save_method') === 'cache') {
                static::$manifests[$manifestPath] = cache()->remember(config('static-assets.release'), now()->addDays(config('static-assets.manifest.cache_days')), function () {
                    return DownloadManifest::make()
                        ->forMix()
                        ->toArray();
                });

                return $this->__invoke($path, $manifestDirectory);
            }

            if (! isset(static::$manifests[$manifestPath])) {
                throw new Exception("Mix manifest not found at: {$manifestPath}");
            }
        }

        $manifest = static::$manifests[$manifestPath];

        $path = Str::of($path)->ltrim('/')->prepend('/')->toString();

        if (! isset($manifest[$path])) {
            $path = Str::of($path)->ltrim('/')->prepend('/public/')->toString();
        }

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
