<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Daily Database Backups
Schedule::command('db:backup')->dailyAt('00:00');

// Automatically upload backups to S3 shortly after creation
Schedule::command('db:upload-s3')->dailyAt('00:15');
