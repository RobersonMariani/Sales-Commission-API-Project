<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreateSaleData extends Data
{
    public function __construct(
        public int $sellerId,
        public float $value,
        public string $saleDate,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'seller_id' => ['required', 'integer', Rule::exists('sellers', 'id')],
            'value' => ['required', 'numeric', 'min:0.01'],
            'sale_date' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, mixed>
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
