<?php

namespace App\Api\Modules\Report\Tests\UseCases;

use App\Api\Modules\Report\Data\SalesReportQueryData;
use App\Api\Modules\Report\Repositories\ReportRepository;
use App\Api\Modules\Report\UseCases\GetSalesReportUseCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('report')]
class GetSalesReportUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnSalesSummaryWhenQueryIsValid(): void
    {
        // Arrange
        $query = new SalesReportQueryData();
        $expectedResult = (object) [
            'total_sales' => 10,
            'total_value' => 1500.00,
            'total_commission' => 127.50,
            'average_value' => 150.00,
            'average_commission' => 12.75,
        ];

        $this->instance(
            ReportRepository::class,
            Mockery::mock(ReportRepository::class, function (MockInterface $mock) use ($query, $expectedResult) {
                $mock->shouldReceive('getSalesSummary')
                    ->once()
                    ->with(Mockery::on(fn ($q) => $q === $query))
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(GetSalesReportUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    public function testExecuteShouldPassQueryWithFiltersToRepository(): void
    {
        // Arrange
        $query = new SalesReportQueryData(
            startDate: '2025-01-01',
            endDate: '2025-01-31',
            sellerId: 5,
        );
        $expectedResult = (object) [
            'total_sales' => 0,
            'total_value' => 0.0,
            'total_commission' => 0.0,
            'average_value' => 0.0,
            'average_commission' => 0.0,
        ];

        $this->instance(
            ReportRepository::class,
            Mockery::mock(ReportRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('getSalesSummary')
                    ->once()
                    ->with(Mockery::on(function (SalesReportQueryData $q) {
                        return $q->startDate === '2025-01-01'
                            && $q->endDate === '2025-01-31'
                            && $q->sellerId === 5;
                    }))
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(GetSalesReportUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }
}
