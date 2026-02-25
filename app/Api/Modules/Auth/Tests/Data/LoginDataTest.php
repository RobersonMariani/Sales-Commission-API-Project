<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\Data;

use App\Api\Modules\Auth\Data\LoginData;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * @group auth
 */
class LoginDataTest extends TestCase
{
    private static function validPayload(): array
    {
        return [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];
    }

    public static function validData(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'email_null' => [array_merge(self::validPayload(), ['email' => null]), 'email'],
            'email_empty' => [array_merge(self::validPayload(), ['email' => '']), 'email'],
            'email_invalid' => [array_merge(self::validPayload(), ['email' => 'invalid']), 'email'],
            'password_null' => [array_merge(self::validPayload(), ['password' => null]), 'password'],
            'password_empty' => [array_merge(self::validPayload(), ['password' => '']), 'password'],
        ];
    }

    /**
     * @dataProvider validData
     */
    public function testValidDataValidation(array $validItem): void
    {
        // Arrange & Act
        $result = LoginData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(LoginData::class, $result);
    }

    /**
     * @dataProvider invalidData
     */
    public function testInvalidDataValidation(array $invalidItem, string $expectedField): void
    {
        // Arrange & Act & Assert
        $this->expectException(ValidationException::class);

        try {
            LoginData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
