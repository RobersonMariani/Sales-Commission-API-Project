<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Tests\UseCases;

use App\Api\Modules\Seller\Repositories\SellerRepository;
use App\Api\Modules\Seller\UseCases\GetSellerUseCase;
use App\Models\Seller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @group seller
 */
class GetSellerUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnSellerWhenFound(): void
    {
        // Arrange
        $expectedSeller = new Seller(['id' => 1, 'name' => 'John Seller', 'email' => 'john@example.com']);

        $repositoryMock = $this->instance(
            SellerRepository::class,
            Mockery::mock(SellerRepository::class, function (MockInterface $mock) use ($expectedSeller) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(1)
                    ->andReturn($expectedSeller);
            }),
        );

        // Act
        $useCase = app()->make(GetSellerUseCase::class);
        $result = $useCase->execute(1);

        // Assert
        $this->assertInstanceOf(Seller::class, $result);
        $this->assertEquals($expectedSeller, $result);
    }

    public function testExecuteShouldThrowModelNotFoundExceptionWhenNotFound(): void
    {
        // Arrange
        $repositoryMock = $this->instance(
            SellerRepository::class,
            Mockery::mock(SellerRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('findById')
                    ->once()
                    ->with(999)
                    ->andReturn(null);
            }),
        );

        // Act & Assert
        $this->expectException(ModelNotFoundException::class);

        $useCase = app()->make(GetSellerUseCase::class);
        $useCase->execute(999);
    }
}
