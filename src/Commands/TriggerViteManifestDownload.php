<?php

namespace StaticAssets\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use StaticAssets\DownloadManifest;

class TriggerViteManifestDownload extends Command
{
    protected $signature = 'static-assets:download-vite-manifest';

    protected $description = 'Download and save Vite manifest to disk';

    public function handle(): int
    {
        try {
            DownloadManifest::make()
                ->forVite()
                ->viaCli()
                ->save();
        } catch (RuntimeException $e) {
            $this->output->error($e->getMessage());
        }

        $this->output->success('Static Assets: Vite manifest downloaded');

        return Command::SUCCESS;
    }
}
