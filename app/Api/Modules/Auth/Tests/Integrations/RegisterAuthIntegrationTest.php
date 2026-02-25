<?php

namespace App\Api\Modules\Auth\Tests\Integrations;

use App\Api\Modules\Auth\Tests\Assertables\UserAssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * @group auth
 */
class RegisterAuthIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/auth/register';

    public function test_should_return_created_when_data_is_valid(): void
    {
        // Arrange
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    UserAssertableJson::schema($json);
                })->etc();
            });
    }

    public function test_should_return_unprocessable_when_required_fields_missing(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, [])
            ->assertUnprocessable();
    }

    public function test_should_return_unprocessable_when_email_already_exists(): void
    {
        // Arrange
        $payload = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        $this->post(self::ENDPOINT, $payload);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, $payload)
            ->assertUnprocessable();
    }
}
