<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Events\SubmissionEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubmissionProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected $data
    ){}

    /**
     * Get the data for the job.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
         try {
            // Save the data to the database
            $submission = Submission::create($this->data);

            // Dispatch the event
            event(new SubmissionEvent($submission));

        } catch (\Exception $e) {
            // Log the exception
            Log::error('Failed to process submission job: ' . $e->getMessage());
        }
    }
}
