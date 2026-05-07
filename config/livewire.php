<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Livewire supports native file uploads via standard HTML form inputs.
    | By default, Livewire will use the default storage disk to store
    | temporary uploads. You can specify a different disk here.
    |
    */

    'temporary_file_upload' => [
        'disk' => 'local',        // Force local storage for temporary uploads to avoid CORS issues with S3/R2
        'middleware' => null,     // Middleware to apply to the temporary upload endpoint
        'directory' => null,      // Directory to store temporary uploads
        'rules' => null,          // Validation rules for temporary uploads
        'cleanup' => true,        // Automatically cleanup temporary uploads
    ],

];
