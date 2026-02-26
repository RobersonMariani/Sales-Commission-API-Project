<?php

namespace App\Api\Modules\Report\Tests\Integrations;

use App\Api\Modules\Report\Tests\Assertables\SellerSalesReportAssertableJson;
use App\Models\Sale;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('report')]
class GetSalesBySellerReportIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/reports/sales/by-seller';

    public function testShouldReturnListGroupedBySellerWhenSalesExist(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $seller1 = Seller::factory()->create(['name' => 'João', 'email' => 'joao@example.com']);
        $seller2 = Seller::factory()->create(['name' => 'Maria', 'email' => 'maria@example.com']);
        Sale::factory()->create([
            'seller_id' => $seller1->id,
            'value' => 100,
            'commission' => 8.50,
            'sale_date' => '2025-01-15',
        ]);
        Sale::factory()->create([
            'seller_id' => $seller1->id,
            'value' => 200,
            'commission' => 17.00,
            'sale_date' => '2025-01-16',
        ]);
        Sale::factory()->create([
            'seller_id' => $seller2->id,
            'value' => 50,
            'commission' => 4.25,
            'sale_date' => '2025-01-17',
        ]);

        // Act & Assert
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT);

        $response->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('data')
                    ->has('data', function (AssertableJson $json) {
                        $json->each(function (AssertableJson $json) {
                            SellerSalesReportAssertableJson::schema($json);
                        });
                    })->etc();
            });

        $data = $response->json('data');
        $this->assertCount(2, $data);
        $joao = collect($data)->firstWhere('seller_id', $seller1->id);
        $this->assertEquals(2, $joao['total_sales']);
        $this->assertEquals(300.0, $joao['total_value']);
        $this->assertEquals(25.5, $joao['total_commission']);
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
        $this->assertCount(1, $data);
        $this->assertEquals($seller1->id, $data[0]['seller_id']);
        $this->assertEquals(2, $data[0]['total_sales']);
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
        $this->assertEquals(1, $data[0]['total_sales']);
        $this->assertEquals(100.0, $data[0]['total_value']);
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

    public function testShouldReturnUnprocessableWhenInvalidQueryParams(): void
    {
        // Arrange
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        // Act & Assert
        $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer '.$token)
            ->get(self::ENDPOINT.'?end_date=not-a-date&seller_id=xyz')
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
