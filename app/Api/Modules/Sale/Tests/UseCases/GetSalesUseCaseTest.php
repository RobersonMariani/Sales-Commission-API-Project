<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Tests\UseCases;

use App\Api\Modules\Sale\Data\SaleQueryData;
use App\Api\Modules\Sale\Repositories\SaleRepository;
use App\Api\Modules\Sale\UseCases\GetSalesUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorImpl;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('sale')]
class GetSalesUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnPaginatedSales(): void
    {
        // Arrange
        $query = new SaleQueryData(page: 1, perPage: 15);
        $expectedPaginator = new LengthAwarePaginatorImpl([], 0, 15, 1);

        $this->instance(
            SaleRepository::class,
            Mockery::mock(SaleRepository::class, function (MockInterface $mock) use ($expectedPaginator) {
                $mock->shouldReceive('getAllPaginated')
                    ->once()
                    ->andReturn($expectedPaginator);
            }),
        );

        // Act
        $useCase = app()->make(GetSalesUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function testExecuteShouldPassSellerIdFilterToRepository(): void
    {
        // Arrange
        $query = new SaleQueryData(sellerId: 5, page: 1, perPage: 10);
        $expectedPaginator = new LengthAwarePaginatorImpl([], 0, 10, 1);

        $this->instance(
            SaleRepository::class,
            Mockery::mock(SaleRepository::class, function (MockInterface $mock) use ($expectedPaginator) {
                $mock->shouldReceive('getAllPaginated')
                    ->once()
                    ->with(Mockery::on(function (SaleQueryData $q) {
                        return $q->sellerId === 5 && $q->perPage === 10;
                    }))
                    ->andReturn($expectedPaginator);
            }),
        );

        // Act
        $useCase = app()->make(GetSalesUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }
}
