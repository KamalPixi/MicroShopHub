<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FinishInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:finish-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually mark the installation as completed.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = storage_path('installed');

        if (!file_exists($path)) {
            touch($path);
            $this->info('Installation marked as completed.');
        } else {
            $this->warn('Installation is already marked as completed.');
        }

        return self::SUCCESS;
    }
}
