<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRawEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $email,
        protected string $subject,
        protected string $body,
        protected bool $isHtml = false
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->isHtml) {
            Mail::html($this->body, function ($message) {
                $message->to($this->email);
                $message->subject($this->subject);
            });
        } else {
            Mail::raw($this->body, function ($message) {
                $message->to($this->email);
                $message->subject($this->subject);
            });
        }
    }
}
