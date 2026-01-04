<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        try {
            $response = $this->get('/');
            $response->assertStatus(200);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            if (
                str_contains($message, 'could not find driver') ||
                str_contains($message, 'no such table') ||
                str_contains($message, 'Base table or view not found')
            ) {
                $this->markTestSkipped('Test membutuhkan database siap: '.$message);
            }

            throw $e;
        }
    }
}
