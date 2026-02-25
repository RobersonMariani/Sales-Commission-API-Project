<?php

declare(strict_types=1);

namespace App\Api\Modules\Seller\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * DTO de validação para os parâmetros de consulta de vendedores.
 */
#[MapName(SnakeCaseMapper::class)]
class SellerQueryData extends Data
{
    public function __construct(
        public ?int $page = 1,
        public ?int $perPage = null,
    ) {
        $this->perPage ??= (int) config('pagination.sellers_per_page');
    }

    /**
     * Define as regras de validação para os filtros de listagem de vendedores.
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
