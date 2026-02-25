<?php

namespace App\Api\Modules\Auth\Tests\Integrations;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group auth
 */
class LogoutAuthIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/auth/logout';

    public function test_should_return_success_when_authenticated(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT)
            ->assertOk()
            ->assertJson(['message' => 'Successfully logged out']);
    }

    public function test_should_return_unauthorized_when_not_authenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT)
            ->assertUnauthorized();
    }
}
