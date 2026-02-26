<?php

namespace App\Api\Modules\Report\Tests\Data;

use App\Api\Modules\Report\Data\SalesReportQueryData;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('report')]
class SalesReportQueryDataTest extends TestCase
{
    use RefreshDatabase;

    public static function validData(): array
    {
        return [
            'without_filters' => [[]],
            'with_start_date' => [['start_date' => '2025-01-01']],
            'with_end_date' => [['end_date' => '2025-01-31']],
            'with_both_dates' => [
                ['start_date' => '2025-01-01', 'end_date' => '2025-01-31'],
            ],
            'with_seller_id' => [['seller_id' => 1]],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'start_date_not_date' => [['start_date' => 'invalid'], 'start_date'],
            'end_date_not_date' => [['end_date' => 'not-a-date'], 'end_date'],
            'seller_id_not_integer' => [['seller_id' => 'abc'], 'seller_id'],
        ];
    }

    #[DataProvider('validData')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange
        $payload = $validItem;
        if (isset($validItem['seller_id'])) {
            $seller = Seller::factory()->create();
            $payload['seller_id'] = $seller->id;
        }

        // Act
        $result = SalesReportQueryData::validateAndCreate($payload);

        // Assert
        $this->assertInstanceOf(SalesReportQueryData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange & Act & Assert
        $this->expectException(ValidationException::class);

        try {
            SalesReportQueryData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());
            throw $e;
        }
    }

    public function testShouldFailValidationWhenSellerIdDoesNotExist(): void
    {
        // Arrange
        $payload = ['seller_id' => 99999];

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            SalesReportQueryData::validateAndCreate($payload);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('seller_id', $e->errors());
            throw $e;
        }
    }
}
