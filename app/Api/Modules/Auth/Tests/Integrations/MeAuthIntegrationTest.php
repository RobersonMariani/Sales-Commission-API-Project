<?php

namespace App\Api\Modules\Auth\Tests\Integrations;

use App\Api\Modules\Auth\Tests\Assertables\UserAssertableJson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * @group auth
 */
class MeAuthIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/auth/me';

    public function test_should_return_user_when_authenticated(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($user) {
                $json->has('data', function (AssertableJson $json) use ($user) {
                    UserAssertableJson::schema($json)
                        ->where('id', $user->id)
                        ->where('email', $user->email)
                        ->where('name', $user->name);
                })->etc();
            });
    }

    public function test_should_return_unauthorized_when_not_authenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->get(self::ENDPOINT)
            ->assertUnauthorized();
    }
}
