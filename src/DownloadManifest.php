<?php

namespace StaticAssets;

use Illuminate\Support\Facades\Http;

class DownloadManifest
{
    protected bool $mix = false;

    protected bool $vite = false;

    public static function make(): self
    {
        return new static();
    }

    public function forMix(): self
    {
        $this->vite = false;
        $this->mix = true;

        return $this;
    }

    public function forVite(): self
    {
        $this->vite = true;
        $this->mix = false;

        return $this;
    }

    protected function getManifest(): array
    {
        $response = Http::get('https://manifests.staticassets.app/'.config('static-assets.release'));

        if ($response->successful()) {
            return $response->json();
        }

        $response = Http::get('https://staticassets.app/api/manifest-fallback/'.config('static-assets.release'));

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    public function save(): void
    {
        $fileName = $this->vite ? 'manifest.json' : 'mix-manifest.json';
        $defaultBuildDirectory = $this->vite ? 'build' : '';
        $buildDirectory = public_path(config('static-assets.manifest.custom_directory') ?: $defaultBuildDirectory);

        // ensure there is the build directory
        if (! is_dir($buildDirectory)) {
            mkdir($buildDirectory);
        }

        file_put_contents("{$buildDirectory}/{$fileName}", json_encode($this->getManifest()));
    }

    public function toArray(): array
    {
        return $this->getManifest();
    }
}
