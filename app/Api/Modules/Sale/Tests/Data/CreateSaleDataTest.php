<?php

namespace App\Api\Modules\Sale\Tests\Data;

use App\Api\Modules\Sale\Data\CreateSaleData;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * @group sale
 */
class CreateSaleDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(): array
    {
        return [
            'seller_id' => 1,
            'value' => 100.50,
            'sale_date' => '2025-01-15',
        ];
    }

    public static function validData(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'value_min' => [array_merge(self::validPayload(), ['value' => 0.01])],
            'sale_date_valid' => [array_merge(self::validPayload(), ['sale_date' => '2024-12-31'])],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'seller_id_null' => [array_merge(self::validPayload(), ['seller_id' => null]), 'seller_id'],
            'seller_id_not_integer' => [array_merge(self::validPayload(), ['seller_id' => 'abc']), 'seller_id'],
            'value_null' => [array_merge(self::validPayload(), ['value' => null]), 'value'],
            'value_zero' => [array_merge(self::validPayload(), ['value' => 0]), 'value'],
            'value_negative' => [array_merge(self::validPayload(), ['value' => -10]), 'value'],
            'value_not_numeric' => [array_merge(self::validPayload(), ['value' => 'invalid']), 'value'],
            'sale_date_null' => [array_merge(self::validPayload(), ['sale_date' => null]), 'sale_date'],
            'sale_date_empty' => [array_merge(self::validPayload(), ['sale_date' => '']), 'sale_date'],
            'sale_date_invalid' => [array_merge(self::validPayload(), ['sale_date' => 'invalid']), 'sale_date'],
        ];
    }

    /**
     * @dataProvider validData
     */
    public function test_valid_data_validation(array $validItem): void
    {
        // Arrange
        $seller = Seller::factory()->create();
        $payload = array_merge($validItem, ['seller_id' => $seller->id]);

        // Act
        $result = CreateSaleData::validateAndCreate($payload);

        // Assert
        $this->assertInstanceOf(CreateSaleData::class, $result);
    }

    /**
     * @dataProvider invalidData
     */
    public function test_invalid_data_validation(array $invalidItem, string $expectedField): void
    {
        // Arrange
        $seller = Seller::factory()->create();
        $payload = $invalidItem;
        if ($expectedField !== 'seller_id') {
            $payload['seller_id'] = $seller->id;
        }

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateSaleData::validateAndCreate($payload);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());
            throw $e;
        }
    }

    public function test_seller_id_exists_validation(): void
    {
        // Arrange
        $payload = array_merge(self::validPayload(), ['seller_id' => 99999]);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateSaleData::validateAndCreate($payload);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('seller_id', $e->errors());
            throw $e;
        }
    }
}
