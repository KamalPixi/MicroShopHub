<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to the configured backup disk.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = config('database.default');
        $this->info("Starting backup for connection: {$connection}");

        if ($connection !== 'sqlite') {
            $this->error("Backup only supports SQLite connection for now.");
            return self::FAILURE;
        }

        $dbPath = config('database.connections.sqlite.database');
        
        // If it's relative, resolve it
        if (!file_exists($dbPath)) {
            $dbPath = database_path($dbPath);
        }

        if (!file_exists($dbPath)) {
            $this->error("Database file not found at: {$dbPath}");
            return self::FAILURE;
        }

        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sqlite';
        $diskName = env('BACKUP_DISK', 'local');
        $backupPath = rtrim(env('BACKUP_PATH', 'backups'), '/') . '/';
        $backupBucket = env('BACKUP_BUCKET');

        // If we are using S3 and a specific backup bucket is defined, override the config
        if ($diskName === 's3' && !empty($backupBucket)) {
            config(['filesystems.disks.s3.bucket' => $backupBucket]);
        }

        $disk = \Illuminate\Support\Facades\Storage::disk($diskName);

        try {
            $this->info("Uploading backup to disk: {$diskName} at path: {$backupPath}...");
            $disk->put($backupPath . $filename, file_get_contents($dbPath));
            
            $this->info("Backup completed successfully: {$backupPath}{$filename}");
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
