<?php

namespace StaticAssets\Commands;

use Illuminate\Console\Command;
use StaticAssets\DownloadManifest;

class TriggerViteManifestDownload extends Command
{
    protected $signature = 'static-assets:download-vite-manifest';

    protected $description = 'Download and save Vite manifest to disk';

    public function handle(): int
    {
        (new DownloadManifest)('vite');

        return Command::SUCCESS;
    }
}
