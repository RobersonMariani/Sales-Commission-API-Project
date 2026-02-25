<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Tests\Integrations;

use App\Api\Modules\Sale\Tests\Assertables\SaleAssertableJson;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * @group sale
 */
class GetSaleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/sales';

    public function testShouldReturnSaleWhenAuthenticatedAndFound(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $sale = Sale::factory()->create([
            'value' => 200.00,
            'commission' => 17.00,
            'sale_date' => '2025-01-20',
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT.'/'.$sale->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($sale) {
                $json->has('data', function (AssertableJson $json) use ($sale) {
                    SaleAssertableJson::schema($json)
                        ->where('id', $sale->id)
                        ->where('value', 200)
                        ->where('commission', 17)
                        ->where('sale_date', '2025-01-20')
                        ->has('seller');
                })->etc();
            });
    }

    public function testShouldReturnNotFoundWhenSaleDoesNotExist(): void
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
        $sale = Sale::factory()->create();

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->get(self::ENDPOINT.'/'.$sale->id)
            ->assertUnauthorized();
    }
}
