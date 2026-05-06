<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Daily Database Backups (Only if enabled in settings)
if (env('BACKUP_ENABLED', true)) {
    Schedule::command('db:backup')->dailyAt('00:00');

    // Automatically upload backups to S3 shortly after creation
    Schedule::command('db:upload-s3')->dailyAt('00:15');
}

// If in "Cron Mode", we can also schedule the queue to run if no persistent worker is available
if (env('BACKGROUND_MODE') === 'cron') {
    Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();
}
