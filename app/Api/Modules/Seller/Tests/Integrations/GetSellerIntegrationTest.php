<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Tests\Integrations;

use App\Api\Modules\Seller\Tests\Assertables\SellerAssertableJson;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('seller')]
class GetSellerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/sellers';

    public function testShouldReturnSellerWhenAuthenticatedAndFound(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $seller = Seller::factory()->create(['name' => 'John Seller', 'email' => 'john@example.com']);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT.'/'.$seller->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($seller) {
                $json->has('data', function (AssertableJson $json) use ($seller) {
                    SellerAssertableJson::schema($json)
                        ->where('id', $seller->id)
                        ->where('name', $seller->name)
                        ->where('email', $seller->email);
                })->etc();
            });
    }

    public function testShouldReturnNotFoundWhenSellerDoesNotExist(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT.'/99999')
            ->assertNotFound();
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Arrange
        $seller = Seller::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->get(self::ENDPOINT.'/'.$seller->id)
            ->assertUnauthorized();
    }
}
