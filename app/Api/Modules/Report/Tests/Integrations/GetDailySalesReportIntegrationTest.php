<?php

namespace App\Api\Modules\Report\Tests\Integrations;

use App\Api\Modules\Report\Tests\Assertables\DailySalesReportAssertableJson;
use App\Models\Sale;
use App\Models\Seller;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('report')]
class GetDailySalesReportIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/reports/sales/daily';

    public function testShouldReturnUnauthorizedWhenNotAuthenticated(): void
    {
        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->get(self::ENDPOINT)
            ->assertUnauthorized();
    }

    public function testShouldReturnDataGroupedByDayWhenNoFiltersProvided(): void
    {
        // Arrange — vendas nos últimos 30 dias (range padrão do endpoint)
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $seller = Seller::factory()->create();
        $today = CarbonImmutable::today()->format('Y-m-d');
        $yesterday = CarbonImmutable::yesterday()->format('Y-m-d');
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 100,
            'commission' => 8.50,
            'sale_date' => $today,
        ]);
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 200,
            'commission' => 17.00,
            'sale_date' => $today,
        ]);
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 50,
            'commission' => 4.25,
            'sale_date' => $yesterday,
        ]);

        // Act & Assert — sem filtros, usa últimos 30 dias
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT);

        $response->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data')
                    ->has('data', function (AssertableJson $json) {
                        $json->each(function (AssertableJson $json) {
                            DailySalesReportAssertableJson::schema($json);
                        });
                    })->etc();
            });

        $data = $response->json('data');
        $this->assertCount(2, $data);
        $todayData = collect($data)->firstWhere('date', $today);
        $this->assertNotNull($todayData);
        $this->assertEquals(2, $todayData['total_sales']);
        $this->assertEquals(300.0, $todayData['total_value']);
        $this->assertEquals(25.5, $todayData['total_commission']);
        $yesterdayData = collect($data)->firstWhere('date', $yesterday);
        $this->assertNotNull($yesterdayData);
        $this->assertEquals(1, $yesterdayData['total_sales']);
        $this->assertEquals(50.0, $yesterdayData['total_value']);
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
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT.'?start_date=2025-01-01&end_date=2025-01-31');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('2025-01-15', $data[0]['date']);
        $this->assertEquals(1, $data[0]['total_sales']);
        $this->assertEquals(100.0, $data[0]['total_value']);
    }

    public function testShouldReturnDataOrderedByDateAsc(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $seller = Seller::factory()->create();
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 100,
            'sale_date' => '2025-01-20',
        ]);
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 200,
            'sale_date' => '2025-01-15',
        ]);
        Sale::factory()->create([
            'seller_id' => $seller->id,
            'value' => 50,
            'sale_date' => '2025-01-18',
        ]);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT.'?start_date=2025-01-01&end_date=2025-01-31');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(3, $data);
        $this->assertEquals('2025-01-15', $data[0]['date']);
        $this->assertEquals('2025-01-18', $data[1]['date']);
        $this->assertEquals('2025-01-20', $data[2]['date']);
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
            ->get(self::ENDPOINT.'?start_date=2025-01-01&end_date=2025-01-31');

        $response->assertOk();
        $this->assertEmpty($response->json('data'));
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
            ->get(self::ENDPOINT.'?start_date=invalid&end_date=not-a-date')
            ->assertUnprocessable();
    }
}
