<?php

namespace App\Api\Modules\Sale\Tests\Integrations;

use App\Api\Modules\Sale\Tests\Assertables\SaleAssertableJson;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * @group sale
 */
class CreateSaleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/sales';

    public function test_should_return_created_with_calculated_commission_when_data_is_valid(): void
    {
        // Arrange
        $user = User::factory()->create();
        $seller = Seller::factory()->create();
        $token = auth('api')->login($user);
        $payload = [
            'seller_id' => $seller->id,
            'value' => 100.00,
            'sale_date' => '2025-01-15',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, $payload)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($seller) {
                $json->has('data', function (AssertableJson $json) use ($seller) {
                    SaleAssertableJson::schema($json)
                        ->where('seller_id', $seller->id)
                        ->where('value', 100)
                        ->where('commission', 8.5)
                        ->where('sale_date', '2025-01-15')
                        ->has('seller');
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

    public function test_should_return_unprocessable_when_value_is_zero(): void
    {
        // Arrange
        $user = User::factory()->create();
        $seller = Seller::factory()->create();
        $token = auth('api')->login($user);
        $payload = [
            'seller_id' => $seller->id,
            'value' => 0,
            'sale_date' => '2025-01-15',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->post(self::ENDPOINT, $payload)
            ->assertUnprocessable();
    }

    public function test_should_return_unauthorized_when_not_authenticated(): void
    {
        // Arrange
        $seller = Seller::factory()->create();
        $payload = [
            'seller_id' => $seller->id,
            'value' => 100.00,
            'sale_date' => '2025-01-15',
        ];

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->post(self::ENDPOINT, $payload)
            ->assertUnauthorized();
    }
}
