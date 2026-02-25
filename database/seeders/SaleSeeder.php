<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
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
