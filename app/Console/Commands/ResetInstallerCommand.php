<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetInstallerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-installer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the installation lock to re-run the installer.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = storage_path('installed');

        if (file_exists($path)) {
            unlink($path);
            $this->info('Installation lock removed successfully.');
        } else {
            $this->warn('No installation lock file found.');
        }

        return self::SUCCESS;
    }
}
