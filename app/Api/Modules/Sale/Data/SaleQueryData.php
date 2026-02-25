<?php

declare(strict_types=1);

namespace App\Api\Modules\Sale\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class SaleQueryData extends Data
{
    public function __construct(
        public ?int $sellerId = null,
        public ?int $page = 1,
        public ?int $perPage = 15,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'seller_id' => ['nullable', 'integer', 'exists:sellers,id'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
