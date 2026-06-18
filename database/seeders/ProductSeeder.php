<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's products.
     *
     * Prices/stock are the design prototype's placeholder values — confirmed
     * with the shop owners to use as-is for now until real numbers are given.
     */
    public function run(): void
    {
        $products = [
            ['name' => '11 KG LPG Cylinder', 'category' => 'Cylinder', 'sale_price' => 2800, 'refill_charge' => 800, 'return_deposit' => 500, 'unit' => 'pcs', 'qty' => 24, 'min_qty' => 5, 'max_qty' => 50],
            ['name' => '5 KG Small Cylinder', 'category' => 'Cylinder', 'sale_price' => 1400, 'refill_charge' => 500, 'return_deposit' => 300, 'unit' => 'pcs', 'qty' => 8, 'min_qty' => 3, 'max_qty' => 30],
            ['name' => '45 KG Commercial', 'category' => 'Cylinder', 'sale_price' => 9500, 'refill_charge' => 3500, 'return_deposit' => 2000, 'unit' => 'pcs', 'qty' => 6, 'min_qty' => 2, 'max_qty' => 20],
            ['name' => 'HOB Mini Cylinder', 'category' => 'Cylinder', 'sale_price' => 600, 'refill_charge' => 250, 'return_deposit' => 150, 'unit' => 'pcs', 'qty' => 3, 'min_qty' => 2, 'max_qty' => 20],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
