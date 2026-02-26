<?php

namespace App\Api\Modules\Report\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * DTO de validação para os parâmetros de consulta dos relatórios de vendas.
 */
#[MapName(SnakeCaseMapper::class)]
class SalesReportQueryData extends Data
{
    public function __construct(
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?int $sellerId = null,
    ) {}

    /**
     * Define as regras de validação para os filtros dos relatórios.
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'seller_id' => ['nullable', 'integer', 'exists:sellers,id'],
        ];
    }
}
