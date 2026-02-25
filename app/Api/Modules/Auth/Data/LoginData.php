<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * DTO de validação para autenticação de usuário.
 */
#[MapName(SnakeCaseMapper::class)]
class LoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    /**
     * Define as regras de validação para o login.
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Converte os dados para o formato esperado pelo guard de autenticação.
     */
    public function toCredentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
