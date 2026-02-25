<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Tests\Data;

use App\Api\Modules\Seller\Data\CreateSellerData;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('seller')]
class CreateSellerDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(): array
    {
        return [
            'name' => 'John Seller',
            'email' => 'john@example.com',
        ];
    }

    public static function validData(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'name_max_length' => [array_merge(self::validPayload(), ['name' => str_repeat('a', 100)])],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'name_null' => [array_merge(self::validPayload(), ['name' => null]), 'name'],
            'name_empty' => [array_merge(self::validPayload(), ['name' => '']), 'name'],
            'name_too_long' => [array_merge(self::validPayload(), ['name' => str_repeat('a', 101)]), 'name'],
            'name_not_string' => [array_merge(self::validPayload(), ['name' => 123]), 'name'],
            'email_null' => [array_merge(self::validPayload(), ['email' => null]), 'email'],
            'email_empty' => [array_merge(self::validPayload(), ['email' => '']), 'email'],
            'email_invalid' => [array_merge(self::validPayload(), ['email' => 'invalid']), 'email'],
        ];
    }

    #[DataProvider('validData')]
    public function testValidDataValidation(array $validItem): void
    {
        // Arrange & Act
        $result = CreateSellerData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(CreateSellerData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testInvalidDataValidation(array $invalidItem, string $expectedField): void
    {
        // Arrange & Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateSellerData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    public function testEmailUniqueValidation(): void
    {
        // Arrange
        Seller::factory()->create(['email' => 'existing@example.com']);
        $payload = array_merge(self::validPayload(), ['email' => 'existing@example.com']);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CreateSellerData::validateAndCreate($payload);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->errors());

            throw $e;
        }
    }
}
