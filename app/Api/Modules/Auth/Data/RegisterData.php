<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * DTO de validação para registro de novo usuário.
 */
#[MapName(SnakeCaseMapper::class)]
class RegisterData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $password_confirmation,
    ) {}

    /**
     * Define as regras de validação para o registro.
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    /**
     * Converte os dados para o formato de criação do model User.
     */
    public function toArrayModel(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
