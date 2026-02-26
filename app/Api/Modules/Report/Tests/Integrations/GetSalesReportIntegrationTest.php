<?php

namespace App\Api\Modules\Report\Tests\Integrations;

use App\Api\Modules\Report\Tests\Assertables\SalesReportAssertableJson;
use App\Models\Sale;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('report')]
class GetSalesReportIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/reports/sales';

    public function testShouldReturnCorrectMetricsWhenSalesExist(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $seller = Seller::factory()->create();
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 100,
            'commission' => 8.50,
            'sale_date' => '2025-01-15',
        ]);
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 200,
            'commission' => 17.00,
            'sale_date' => '2025-01-16',
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    SalesReportAssertableJson::schema($json);
                })->etc();
            })
            ->assertJsonPath('data.total_sales', 2)
            ->assertJsonPath('data.total_value', 300)
            ->assertJsonPath('data.total_commission', 25.5)
            ->assertJsonPath('data.average_value', 150)
            ->assertJsonPath('data.average_commission', 12.75);
    }

    public function testShouldReturnZerosWhenNoSales(): void
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
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', function (AssertableJson $json) {
                    SalesReportAssertableJson::schema($json);
                })->etc();
            })
            ->assertJsonPath('data.total_sales', 0)
            ->assertJsonPath('data.total_value', 0)
            ->assertJsonPath('data.total_commission', 0)
            ->assertJsonPath('data.average_value', 0)
            ->assertJsonPath('data.average_commission', 0);
    }

    public function testShouldFilterByPeriodWhenStartDateAndEndDateProvided(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $seller = Seller::factory()->create();
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 100,
            'commission' => 8.50,
            'sale_date' => '2025-01-15',
        ]);
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 200,
            'commission' => 17.00,
            'sale_date' => '2025-02-20',
        ]);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT.'?start_date=2025-01-01&end_date=2025-01-31')
            ->assertOk()
            ->assertJsonPath('data.total_sales', 1)
            ->assertJsonPath('data.total_value', 100);
    }

    public function testShouldReturnUnprocessableWhenInvalidQueryParams(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT.'?start_date=invalid&seller_id=abc')
            ->assertUnprocessable();
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
