<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\Data;

use App\Api\Modules\Auth\Data\RegisterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * @group auth
 */
class RegisterDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(): array
    {
        return [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
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
            'password_null' => [array_merge(self::validPayload(), ['password' => null]), 'password'],
            'password_empty' => [array_merge(self::validPayload(), ['password' => '']), 'password'],
            'password_too_short' => [
                array_merge(self::validPayload(), [
                    'password' => 'short',
                    'password_confirmation' => 'short',
                ]),
                'password',
            ],
            'password_not_confirmed' => [
                array_merge(self::validPayload(), ['password_confirmation' => 'different']),
                'password',
            ],
        ];
    }

    /**
     * @dataProvider validData
     */
    public function testValidDataValidation(array $validItem): void
    {
        // Arrange & Act
        $result = RegisterData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(RegisterData::class, $result);
    }

    /**
     * @dataProvider invalidData
     */
    public function testInvalidDataValidation(array $invalidItem, string $expectedField): void
    {
        // Arrange & Act & Assert
        $this->expectException(ValidationException::class);

        try {
            RegisterData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }

    public function testEmailUniqueValidation(): void
    {
        // Arrange
        User::factory()->create(['email' => 'existing@example.com']);
        $payload = array_merge(self::validPayload(), ['email' => 'existing@example.com']);

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            RegisterData::validateAndCreate($payload);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->errors());

            throw $e;
        }
    }
}
