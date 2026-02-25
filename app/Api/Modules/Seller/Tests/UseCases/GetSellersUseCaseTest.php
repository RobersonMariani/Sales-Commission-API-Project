<?php

namespace App\Api\Modules\Seller\Tests\UseCases;

use App\Api\Modules\Seller\Data\SellerQueryData;
use App\Api\Modules\Seller\Repositories\SellerRepository;
use App\Api\Modules\Seller\UseCases\GetSellersUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as LengthAwarePaginatorImpl;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @group seller
 */
class GetSellersUseCaseTest extends TestCase
{
    public function test_execute_should_return_paginated_sellers(): void
    {
        // Arrange
        $query = new SellerQueryData(page: 1, perPage: 15);
        $expectedPaginator = new LengthAwarePaginatorImpl([], 0, 15, 1);

        $repositoryMock = $this->instance(
            SellerRepository::class,
            Mockery::mock(SellerRepository::class, function (MockInterface $mock) use ($expectedPaginator) {
                $mock->shouldReceive('getAllPaginated')
                    ->once()
                    ->andReturn($expectedPaginator);
            })
        );

        // Act
        $useCase = app()->make(GetSellersUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }
}
