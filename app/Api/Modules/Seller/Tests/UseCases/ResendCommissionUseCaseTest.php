<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Tests\UseCases;

use App\Api\Modules\Sale\Jobs\SendDailySellerCommissionJob;
use App\Api\Modules\Seller\Repositories\SellerRepository;
use App\Api\Modules\Seller\UseCases\ResendCommissionUseCase;
use App\Models\Seller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('seller')]
class ResendCommissionUseCaseTest extends TestCase
{
    public function testExecuteShouldDispatchJobWhenSellerFound(): void
    {
        // Arrange
        Queue::fake();
        $seller = new Seller(['id' => 1, 'name' => 'John', 'email' => 'john@example.com']);

        $this->instance(
            SellerRepository::class,
            Mockery::mock(SellerRepository::class, function (MockInterface $mock) use ($seller) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($seller);
            }),
        );

        // Act
        $useCase = app()->make(ResendCommissionUseCase::class);
        $useCase->execute(1, '2026-02-24');

        // Assert
        Queue::assertPushed(SendDailySellerCommissionJob::class, function ($job) use ($seller) {
            return $job->seller->id === $seller->id && $job->date === '2026-02-24';
        });
    }

    public function testExecuteShouldUseTodayWhenDateIsNull(): void
    {
        // Arrange
        Queue::fake();
        $seller = new Seller(['id' => 1, 'name' => 'John', 'email' => 'john@example.com']);

        $this->instance(
            SellerRepository::class,
            Mockery::mock(SellerRepository::class, function (MockInterface $mock) use ($seller) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($seller);
            }),
        );

        // Act
        $useCase = app()->make(ResendCommissionUseCase::class);
        $useCase->execute(1);

        // Assert
        Queue::assertPushed(SendDailySellerCommissionJob::class, function ($job) {
            return $job->date === now()->toDateString();
        });
    }

    public function testExecuteShouldThrowHttpResponseExceptionWhenSellerNotFound(): void
    {
        // Arrange
        $this->instance(
            SellerRepository::class,
            Mockery::mock(SellerRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
            }),
        );

        // Act & Assert
        $this->expectException(HttpResponseException::class);

        $useCase = app()->make(ResendCommissionUseCase::class);
        $useCase->execute(999);
    }
}
