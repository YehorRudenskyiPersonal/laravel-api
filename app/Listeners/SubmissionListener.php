<?php

namespace App\Listeners;

use App\Events\SubmissionEvent;
use Illuminate\Support\Facades\Log;

class SubmissionListener
{

    /**
     * Handle the event.
     */
    public function handle(SubmissionEvent $event): void
    {
        $submission = $event->submission;
        Log::info('Submission saved', ['name' => $submission->name, 'email' => $submission->email]);
    }
}
