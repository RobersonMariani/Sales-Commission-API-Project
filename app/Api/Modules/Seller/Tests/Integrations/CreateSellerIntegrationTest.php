<?php

namespace App\Api\Modules\Seller\Tests\Integrations;

use App\Api\Modules\Seller\Tests\Assertables\SellerAssertableJson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * @group seller
 */
class CreateSellerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/sellers';

    public function test_should_return_created_when_data_is_valid(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $payload = [
            'name' => 'John Seller',
            'email' => 'john@example.com',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    SellerAssertableJson::schema($json)
                        ->where('name', 'John Seller')
                        ->where('email', 'john@example.com');
                })->etc();
            });
    }

    public function test_should_return_unprocessable_when_required_fields_missing(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, [])
            ->assertUnprocessable();
    }

    public function test_should_return_unprocessable_when_email_already_exists(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $payload = [
            'name' => 'John Seller',
            'email' => 'existing@example.com',
        ];
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, $payload);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, $payload)
            ->assertUnprocessable();
    }

    public function test_should_return_unauthorized_when_not_authenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, ['name' => 'John', 'email' => 'john@example.com'])
            ->assertUnauthorized();
    }
}
