<?php

namespace App\Api\Modules\Report\Tests\UseCases;

use App\Api\Modules\Report\Data\SalesReportQueryData;
use App\Api\Modules\Report\Repositories\ReportRepository;
use App\Api\Modules\Report\UseCases\GetDailySalesReportUseCase;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('report')]
class GetDailySalesReportUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnDailySalesWhenQueryIsValid(): void
    {
        // Arrange
        $query = new SalesReportQueryData();
        $expectedResult = collect([
            (object) [
                'date' => '2025-01-15',
                'total_sales' => 2,
                'total_value' => 300.00,
                'total_commission' => 25.50,
            ],
        ]);

        $this->instance(
            ReportRepository::class,
            Mockery::mock(ReportRepository::class, function (MockInterface $mock) use ($query, $expectedResult) {
                $mock->shouldReceive('getDailySales')
                    ->once()
                    ->with(Mockery::on(fn ($q) => $q === $query))
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(GetDailySalesReportUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
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
        $expectedResult = collect([
            (object) [
                'date' => '2025-01-15',
                'total_sales' => 1,
                'total_value' => 100.00,
                'total_commission' => 8.50,
            ],
        ]);

        $this->instance(
            ReportRepository::class,
            Mockery::mock(ReportRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('getDailySales')
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
        $useCase = app()->make(GetDailySalesReportUseCase::class);
        $result = $useCase->execute($query);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals($expectedResult, $result);
    }
}
