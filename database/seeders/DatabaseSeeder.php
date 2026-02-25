<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder principal que orquestra a população inicial do banco de dados.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Cria o usuário admin padrão e executa os seeders de vendedores e vendas.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@salescommission.com',
            'password' => 'password',
        ]);

        $this->call([
            SellerSeeder::class,
            SaleSeeder::class,
        ]);
    }
}
