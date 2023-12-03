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
        DownloadManifest::make()
            ->forMix()
            ->save();

        return Command::SUCCESS;
    }
}
