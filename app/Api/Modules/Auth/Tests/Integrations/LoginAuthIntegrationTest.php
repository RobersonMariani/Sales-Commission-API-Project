<?php

namespace App\Api\Modules\Auth\Tests\Integrations;

use App\Api\Modules\Auth\Tests\Assertables\AuthAssertableJson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * @group auth
 */
class LoginAuthIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/auth/login';

    public function test_should_return_token_when_credentials_are_valid(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $payload = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, $payload)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    AuthAssertableJson::schema($json);
                })->etc();
            });
    }

    public function test_should_return_unauthorized_when_credentials_are_invalid(): void
    {
        // Arrange
        $payload = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, $payload)
            ->assertUnauthorized();
    }

    public function test_should_return_unprocessable_when_required_fields_missing(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, [])
            ->assertUnprocessable();
    }
}
