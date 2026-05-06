<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the PostgreSQL database to a local file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password');
        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');

        $filename = "backup-" . now()->format('Y-m-d-H-i-s') . ".sql";
        $storagePath = storage_path('app/backups');

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $path = $storagePath . '/' . $filename;

        // Use pg_dump for PostgreSQL
        $command = sprintf(
            'PGPASSWORD="%s" pg_dump -h %s -p %s -U %s %s > %s',
            $password,
            $host,
            $port,
            $username,
            $database,
            $path
        );

        $process = Process::fromShellCommandline($command);

        try {
            $process->mustRun();
            $this->info("Backup successfully created at: {$path}");
            return Command::SUCCESS;
        } catch (ProcessFailedException $exception) {
            $this->error("Backup failed: " . $exception->getMessage());
            return Command::FAILURE;
        }
    }
}
