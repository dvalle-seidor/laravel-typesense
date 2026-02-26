<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Pro 15"',
                'description' => 'High-performance laptop with 16GB RAM and 512GB SSD',
                'price' => 1299.99,
                'category' => 'Electronics',
                'in_stock' => true,
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with long battery life',
                'price' => 29.99,
                'category' => 'Electronics',
                'in_stock' => true,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB mechanical keyboard with blue switches',
                'price' => 89.99,
                'category' => 'Electronics',
                'in_stock' => true,
            ],
            [
                'name' => 'USB-C Hub',
                'description' => '7-in-1 USB-C hub with HDMI and SD card reader',
                'price' => 49.99,
                'category' => 'Electronics',
                'in_stock' => false,
            ],
            [
                'name' => 'Monitor 27"',
                'description' => '4K IPS monitor with 60Hz refresh rate',
                'price' => 399.99,
                'category' => 'Electronics',
                'in_stock' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
