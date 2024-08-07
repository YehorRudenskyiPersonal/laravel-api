<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Jobs\SubmissionProcessJob;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the submit endpoint with valid data.
     *
     * @return void
     */
    public function testSubmitEndpointWithValidData()
    {
        // Mock the queue
        Queue::fake();

        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'message' => 'This is a test message.',
        ];

        $response = $this->postJson('/api/submit', $data);

        // Assert the response status and structure
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Your submission is being processed.',
                 ]);

        // Assert that the job was pushed to the queue
        Queue::assertPushed(SubmissionProcessJob::class, function ($job) use ($data) {
            return $job->getData() === $data;
        });
    }

    /**
     * Test the submit endpoint with missing fields.
     *
     * @return void
     */
    public function testSubmitEndpointWithMissingFields()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            // 'message' is missing
        ];

        $response = $this->postJson('/api/submit', $data);

        // Assert the response status and error structure
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['message']);
    }

    /**
     * Test the submit endpoint with invalid email.
     *
     * @return void
     */
    public function testSubmitEndpointWithInvalidEmail()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'message' => 'This is a test message.',
        ];

        $response = $this->postJson('/api/submit', $data);

        // Assert the response status and error structure
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test the submit endpoint with empty payload.
     *
     * @return void
     */
    public function testSubmitEndpointWithEmptyPayload()
    {
        $response = $this->postJson('/api/submit', []);

        // Assert the response status and error structure
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    /**
     * Test the submit endpoint with excessively long fields.
     *
     * @return void
     */
    public function testSubmitEndpointWithExcessiveFieldLengths()
    {
        $data = [
            'name' => str_repeat('A', 256),
            'email' => 'john.doe@example.com',
            'message' => str_repeat('B', 10001),
        ];

        $response = $this->postJson('/api/submit', $data);

        // Assert the response status and error structure
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'message']);
    }

    /**
     * Test the submit endpoint with duplicate email submissions.
     *
     * @return void
     */
    public function testSubmitEndpointWithDuplicateEmail()
    {
        // Mock the queue to prevent actual job execution
        Queue::fake();

        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'message' => 'This is a test message.',
        ];

        // First submission
        $this->postJson('/api/submit', $data);

        // Wait for the job to process and store the submission
        $this->artisan('queue:work', ['--once' => true]);

        // Second submission with the same email
        $response = $this->postJson('/api/submit', $data);

        // Assert the response status and structure
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Your submission is being processed.',
                 ]);

        // Ensure two jobs were pushed (meaning duplicates are allowed, based on your business logic)
        Queue::assertPushed(SubmissionProcessJob::class, 2);
    }

    /**
     * Test that the submission is correctly saved to the database.
     *
     * @return void
     */
    public function testSubmissionIsSavedToDatabase()
    {
        // Send a POST request to the /submit endpoint
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'message' => 'This is a test message.',
        ];

        $response = $this->postJson('/api/submit', $data);

        // Ensure the response is successful
        $response->assertStatus(200);

        // Process the queue manually to execute the job
        $this->artisan('queue:work', ['--once' => true]);

        // Assert that the data was saved in the database
        $this->assertDatabaseHas('submissions', $data);
    }

    /**
     * Test the submit endpoint with an incorrect HTTP method.
     *
     * @return void
     */
    public function testSubmitEndpointWithIncorrectHttpMethod()
    {
        $response = $this->getJson('/api/submit');

        // Assert the response status is Method Not Allowed (405)
        $response->assertStatus(405);
    }
}
