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
class GetSellersIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/sellers';

    public function testShouldReturnPaginatedListWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        Seller::factory()->count(2)->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data')
                    ->has('meta')
                    ->has('links')
                    ->has('data', function (AssertableJson $json) {
                        $json->each(function (AssertableJson $json) {
                            SellerAssertableJson::schema($json);
                        });
                    })->etc();
            });
    }

    public function testShouldReturnValidStructureWhenNoAdditionalSellers(): void
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
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->get(self::ENDPOINT)
            ->assertUnauthorized();
    }
}
