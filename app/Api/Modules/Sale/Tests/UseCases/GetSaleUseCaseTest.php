<?php

namespace App\Api\Modules\Sale\Tests\UseCases;

use App\Api\Modules\Sale\Repositories\SaleRepository;
use App\Api\Modules\Sale\UseCases\GetSaleUseCase;
use App\Models\Sale;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @group sale
 */
class GetSaleUseCaseTest extends TestCase
{
    public function test_execute_should_return_sale_when_found(): void
    {
        // Arrange
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
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedSale);
            })
        );

        // Act
        $useCase = app()->make(GetSaleUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Sale::class, $result);
        $this->assertEquals($expectedSale, $result);
    }

    public function test_execute_should_throw_model_not_found_exception_when_not_found(): void
    {
        // Arrange
        $this->instance(
            SaleRepository::class,
            Mockery::mock(SaleRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
            })
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(GetSaleUseCase::class);
        $useCase->execute(999);
    }
}
