<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmissionRequest;
use App\Jobs\SubmissionProcessJob;

class SubmissionController extends Controller
{
    /**
     * Handle the incoming submission request.
     *
     * @param  \App\Http\Requests\SubmissionRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(SubmissionRequest $request)
    {
        // Dispatch the job to process the submission
        SubmissionProcessJob::dispatch($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Your submission is being processed.',
        ], 200);
    }
}
