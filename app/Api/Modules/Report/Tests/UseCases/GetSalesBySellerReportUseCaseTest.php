<?php

namespace App\Api\Modules\Report\Tests\UseCases;

use App\Api\Modules\Report\Data\SalesReportQueryData;
use App\Api\Modules\Report\Repositories\ReportRepository;
use App\Api\Modules\Report\UseCases\GetSalesBySellerReportUseCase;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('report')]
class GetSalesBySellerReportUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnCollectionWhenQueryIsValid(): void
    {
        // Arrange
        $query = new SalesReportQueryData();
        $expectedResult = collect([
            (object) [
                'seller_id' => 1,
                'seller_name' => 'João',
                'seller_email' => 'joao@example.com',
                'total_sales' => 5,
                'total_value' => 500.00,
                'total_commission' => 42.50,
            ],
        ]);

        $this->instance(
            ReportRepository::class,
            Mockery::mock(ReportRepository::class, function (MockInterface $mock) use ($query, $expectedResult) {
                $mock->shouldReceive('getSalesBySeller')
                    ->once()
                    ->with(Mockery::on(fn ($q) => $q === $query))
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(GetSalesBySellerReportUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
    }

    public function testExecuteShouldPassQueryWithFiltersToRepository(): void
    {
        // Arrange
        $query = new SalesReportQueryData(
            startDate: '2025-01-01',
            endDate: '2025-01-31',
            sellerId: 3,
        );
        $expectedResult = collect([]);

        $this->instance(
            ReportRepository::class,
            Mockery::mock(ReportRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('getSalesBySeller')
                    ->once()
                    ->with(Mockery::on(function (SalesReportQueryData $q) {
                        return $q->startDate === '2025-01-01'
                            && $q->endDate === '2025-01-31'
                            && $q->sellerId === 3;
                    }))
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(GetSalesBySellerReportUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }
}
