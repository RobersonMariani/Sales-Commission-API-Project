<?php

namespace App\Api\Modules\Seller\Tests\Integrations;

use App\Api\Modules\Seller\Tests\Assertables\SellerAssertableJson;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * @group seller
 */
class GetSellersIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/sellers';

    public function test_should_return_paginated_list_when_authenticated(): void
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

    public function test_should_return_valid_structure_when_no_additional_sellers(): void
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

    public function test_should_return_unauthorized_when_not_authenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->get(self::ENDPOINT)
            ->assertUnauthorized();
    }
}
