<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Tests\UseCases;

use App\Api\Modules\Seller\Data\CreateSellerData;
use App\Api\Modules\Seller\Repositories\SellerRepository;
use App\Api\Modules\Seller\UseCases\CreateSellerUseCase;
use App\Models\Seller;
use Closure;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @group seller
 */
class CreateSellerUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnSellerWhenDataIsValid(): void
    {
        // Arrange
        $data = new CreateSellerData(name: 'John Seller', email: 'john@example.com');
        $expectedSeller = new Seller(['id' => 1, 'name' => 'John Seller', 'email' => 'john@example.com']);

        $this->instance(
            SellerRepository::class,
            Mockery::mock(SellerRepository::class, function (MockInterface $mock) use ($expectedSeller) {
                $mock->shouldReceive('create')
                    ->once()
                    ->with(['name' => 'John Seller', 'email' => 'john@example.com'])
                    ->andReturn($expectedSeller);
            }),
        );

        DB::shouldReceive('transaction')
            ->once()
            ->with(Mockery::type(Closure::class))
            ->andReturnUsing(fn ($callback) => $callback());

        // Act
        $useCase = app()->make(CreateSellerUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Seller::class, $result);
        $this->assertEquals($expectedSeller, $result);
    }
}
