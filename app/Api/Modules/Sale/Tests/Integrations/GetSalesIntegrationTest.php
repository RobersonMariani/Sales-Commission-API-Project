<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Tests\Integrations;

use App\Api\Modules\Sale\Tests\Assertables\SaleAssertableJson;
use App\Models\Sale;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('sale')]
class GetSalesIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/sales';

    public function testShouldReturnPaginatedListWhenAuthenticated(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        Sale::factory()->count(2)->create();

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
                            SaleAssertableJson::schema($json);
                        });
                    })->etc();
            });
    }

    public function testShouldReturnEmptyListWhenNoSales(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT);

        $response->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    public function testShouldFilterBySellerIdWhenQueryParamProvided(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $seller1 = Seller::factory()->create();
        $seller2 = Seller::factory()->create();
        Sale::factory()->create(['seller_id' => $seller1->id]);
        Sale::factory()->create(['seller_id' => $seller1->id]);
        Sale::factory()->create(['seller_id' => $seller2->id]);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT.'?seller_id='.$seller1->id);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals($seller1->id, $data[0]['seller_id']);
        $this->assertEquals($seller1->id, $data[1]['seller_id']);
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
