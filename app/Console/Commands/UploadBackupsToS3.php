<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UploadBackupsToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:upload-s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload local database backups to S3 storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for local backups...');

        $storagePath = storage_path('app/backups');
        
        if (!is_dir($storagePath)) {
            $this->warn('No backup directory found.');
            return Command::SUCCESS;
        }

        $files = File::files($storagePath);

        if (empty($files)) {
            $this->info('No backup files found to upload.');
            return Command::SUCCESS;
        }

        $s3Disk = Storage::disk('s3');

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $this->info("Uploading {$filename} to S3...");

            try {
                $s3Disk->put("backups/{$filename}", fopen($file->getRealPath(), 'r+'));
                $this->info("Successfully uploaded {$filename}");
                
                // Optional: Remove local file after successful upload
                File::delete($file->getRealPath());
                $this->info("Deleted local copy of {$filename}");
            } catch (\Exception $e) {
                $this->error("Failed to upload {$filename}: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
