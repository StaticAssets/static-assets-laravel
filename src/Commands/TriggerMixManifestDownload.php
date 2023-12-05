<?php

namespace StaticAssets\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use StaticAssets\DownloadManifest;

class TriggerMixManifestDownload extends Command
{
    protected $signature = 'static-assets:download-mix-manifest';

    protected $description = 'Download and save Laravel Mix manifest to disk';

    public function handle(): int
    {
        try {
            DownloadManifest::make()
                ->forMix()
                ->viaCli()
                ->save();
        } catch (RuntimeException $e) {
            $this->output->error($e->getMessage());
        }

        $this->output->success('Static Assets: Mix manifest downloaded');

        return Command::SUCCESS;
    }
}
