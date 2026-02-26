<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para geração de dados fictícios de vendas.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Define o estado padrão do model Sale com comissão calculada automaticamente.
     */
    public function definition(): array
    {
        $value = fake()->randomFloat(2, 10, 10000);

        $commissionRate = 8.50;

        return [
            'seller_id' => Seller::factory(),
            'value' => $value,
            'commission' => round($value * ($commissionRate / 100), 2),
            'commission_rate' => $commissionRate,
            'sale_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }
}
