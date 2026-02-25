<?php

namespace App\Api\Modules\Seller\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreateSellerData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('sellers', 'email')],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayModel(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
