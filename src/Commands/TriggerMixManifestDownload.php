<?php

namespace StaticAssets\Commands;

use Illuminate\Console\Command;
use StaticAssets\DownloadManifest;

class TriggerMixManifestDownload extends Command
{
    protected $signature = 'static-assets:download-mix-manifest';

    protected $description = 'Download and save Laravel Mix manifest to disk';

    public function handle(): int
    {
        (new DownloadManifest)('mix');

        return Command::SUCCESS;
    }
}
