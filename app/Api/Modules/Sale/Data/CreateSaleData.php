<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * DTO de validação para criação de uma nova venda.
 */
#[MapName(SnakeCaseMapper::class)]
class CreateSaleData extends Data
{
    public function __construct(
        public int $sellerId,
        public float $value,
        public string $saleDate,
    ) {}

    /**
     * Define as regras de validação para criação de venda.
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'seller_id' => ['required', 'integer', Rule::exists('sellers', 'id')],
            'value' => ['required', 'numeric', 'min:0.01'],
            'sale_date' => ['required', 'date'],
        ];
    }

    /**
     * Converte os dados para o formato de criação do model Sale.
     */
    public function toArrayModel(): array
    {
        return [
            'seller_id' => $this->sellerId,
            'value' => $this->value,
            'sale_date' => $this->saleDate,
        ];
    }
}
