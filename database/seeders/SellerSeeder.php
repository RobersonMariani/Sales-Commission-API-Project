<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;

/**
 * Seeder que popula a tabela de vendedores com dados fictícios.
 */
class SellerSeeder extends Seeder
{
    /**
     * Cria 10 vendedores fictícios via factory.
     */
    public function run(): void
    {
        Seller::factory(10)->create();
    }
}
