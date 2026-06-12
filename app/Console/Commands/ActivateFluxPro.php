<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:activate-flux-pro {--dry-run : Validate the Flux Pro credentials without writing auth.json}')]
#[Description('Activate Flux Pro using FLUX_USERNAME and FLUX_LICENSE_KEY from the environment')]
class ActivateFluxPro extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $username = (string) config('services.flux.username', '');
        $licenseKey = (string) config('services.flux.license_key', '');

        if ($username === '' || $licenseKey === '') {
            $this->error('Set FLUX_USERNAME and FLUX_LICENSE_KEY before activating Flux Pro.');

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->info('Flux Pro credentials are configured.');

            return self::SUCCESS;
        }

        return $this->call('flux:activate', [
            'email' => $username,
            'key' => $licenseKey,
            '--no-interaction' => true,
        ]);
    }
}
