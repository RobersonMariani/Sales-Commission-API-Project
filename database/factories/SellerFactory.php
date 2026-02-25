<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para geração de dados fictícios de vendedores.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seller>
 */
class SellerFactory extends Factory
{
    protected $model = Seller::class;

    /**
     * Define o estado padrão do model Seller.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
