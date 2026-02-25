<?php

namespace App\Api\Modules\Sale\Tests\UseCases;

use App\Api\Modules\Sale\Data\CreateSaleData;
use App\Api\Modules\Sale\Repositories\SaleRepository;
use App\Api\Modules\Sale\UseCases\CreateSaleUseCase;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @group sale
 */
class CreateSaleUseCaseTest extends TestCase
{
    public function test_execute_should_return_sale_with_calculated_commission_when_data_is_valid(): void
    {
        // Arrange
        $data = new CreateSaleData(sellerId: 1, value: 100.00, saleDate: '2025-01-15');
        $expectedSale = new Sale([
            'id' => 1,
            'seller_id' => 1,
            'value' => 100.00,
            'commission' => 8.50,
            'sale_date' => '2025-01-15',
        ]);

        $this->instance(
            SaleRepository::class,
            Mockery::mock(SaleRepository::class, function (MockInterface $mock) use ($expectedSale) {
                $mock->shouldReceive('create')
                    ->once()
                    ->with(Mockery::on(function (array $data) {
                        return $data['seller_id'] === 1
                            && $data['value'] === 100.00
                            && $data['commission'] === 8.50
                            && $data['sale_date'] === '2025-01-15';
                    }))
                    ->andReturn($expectedSale);
            })
        );

        DB::shouldReceive('transaction')
            ->once()
            ->with(Mockery::type(\Closure::class))
            ->andReturnUsing(fn ($callback) => $callback());

        // Act
        $useCase = app()->make(CreateSaleUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals(8.50, $result->commission);
    }

    public function test_execute_should_calculate_commission_correctly_for_different_values(): void
    {
        // Arrange
        $data = new CreateSaleData(sellerId: 1, value: 1000.00, saleDate: '2025-01-15');
        $expectedSale = new Sale([
            'id' => 1,
            'seller_id' => 1,
            'value' => 1000.00,
            'commission' => 85.00,
            'sale_date' => '2025-01-15',
        ]);

        $this->instance(
            SaleRepository::class,
            Mockery::mock(SaleRepository::class, function (MockInterface $mock) use ($expectedSale) {
                $mock->shouldReceive('create')
                    ->once()
                    ->with(Mockery::on(function (array $data) {
                        return $data['commission'] === 85.00;
                    }))
                    ->andReturn($expectedSale);
            })
        );

        DB::shouldReceive('transaction')
            ->once()
            ->with(Mockery::type(\Closure::class))
            ->andReturnUsing(fn ($callback) => $callback());

        // Act
        $useCase = app()->make(CreateSaleUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertEquals(85.00, $result->commission);
    }
}
