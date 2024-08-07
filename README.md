# Laravel API Submission Task

## Overview

This Laravel API provides a single endpoint for submitting data. The API accepts a `POST` request at `/api/submit` with a JSON payload. Data is processed by a queued job, and after saving to the database, an event is triggered. This README includes instructions for setting up the project, running migrations, and testing the API.

## Prerequisites

- **Docker**: Ensure Docker and Docker Compose are installed.
- **PHP**: PHP 8.3 is required.
- **Composer**: Used for managing PHP dependencies.

## Setup

1. **Clone this Repository** - git clone
    ```bash
    git clone
    ```
2. **Use the command to decrypt env file**
    ```bash
    php artisan env:decrypt --key=3UVsEgGVK36XN82KKeyLFMhvosbZN1aF
    ```
    Key is the test one from documentation
3. **Build and run application** - docker-compose build && docker-compose up
    ```bash
    docker-compose build && docker-compose up
    ```
4. **Run Migrations in app container**
    ```bash
    docker-compose exec app php artisan migrate
    ```
5. **Start the queue worker to process jobs**
    ```bash
    docker-compose exec app php artisan queue:work
    ```

## API Endpoint

URL: `/api/submit`
Method: POST
Request Payload:
```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "message": "This is a test message."
}
```
Response:

    Success: 200 OK with a confirmation message.
    Error: Appropriate error messages for validation or processing issues.

## Testing the API

1. Open Postman and create a new request.

2. Set Method to POST and URL to http://localhost/api/submit.

3. Go to the Body tab, select raw and JSON from the dropdown.

4. Enter the JSON Payload:
    ```json
    {
    "name": "John Doe",
    "email": "john.doe@example.com",
    "message": "This is a test message."
    }
    ```

5. Click "Send" to make the request and observe the response. 

## Running Tests

To run tests using PHPUnit, use the following command:
```bash
docker-compose exec app php artisan test
```

Ensure that all tests pass.
