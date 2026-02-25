<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * DTO de validação para criação de um novo vendedor.
 */
#[MapName(SnakeCaseMapper::class)]
class CreateSellerData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    /**
     * Define as regras de validação para criação de vendedor.
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('sellers', 'email')],
        ];
    }

    /**
     * Converte os dados para o formato de criação do model Seller.
     */
    public function toArrayModel(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
