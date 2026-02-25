<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Database\Seeder;

/**
 * Seeder que popula a tabela de vendas com dados fictícios.
 */
class SaleSeeder extends Seeder
{
    /**
     * Cria entre 3 e 8 vendas para cada vendedor existente nos últimos 30 dias.
     */
    public function run(): void
    {
        $sellers = Seller::all();

        $sellers->each(function (Seller $seller) {
            $count = rand(3, 8);

            Sale::factory($count)->create([
                'seller_id' => $seller->id,
                'sale_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            ]);
        });
    }
}
