<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ComplexProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Electronics
            ['name' => 'Gaming Laptop RTX 4070', 'description' => 'High-end gaming laptop with RTX 4070, 32GB RAM, 1TB SSD', 'price' => 2499.99, 'category' => 'Electronics', 'in_stock' => true],
            ['name' => 'MacBook Air M2', 'description' => 'Apple MacBook Air with M2 chip, 16GB RAM, 512GB SSD', 'price' => 1299.99, 'category' => 'Electronics', 'in_stock' => true],
            ['name' => 'Gaming Mouse RGB', 'description' => 'Wireless gaming mouse with RGB lighting and 16000 DPI', 'price' => 79.99, 'category' => 'Electronics', 'in_stock' => true],
            ['name' => 'Mechanical Keyboard Gaming', 'description' => 'RGB mechanical keyboard with Cherry MX switches', 'price' => 149.99, 'category' => 'Electronics', 'in_stock' => false],
            ['name' => '4K Gaming Monitor 144Hz', 'description' => '27-inch 4K monitor with 144Hz refresh rate and G-Sync', 'price' => 599.99, 'category' => 'Electronics', 'in_stock' => true],
            ['name' => 'Webcam 4K Pro', 'description' => 'Professional 4K webcam with auto-focus and noise cancellation', 'price' => 199.99, 'category' => 'Electronics', 'in_stock' => true],
            
            // Books
            ['name' => 'Clean Code', 'description' => 'A handbook of agile software craftsmanship by Robert C. Martin', 'price' => 39.99, 'category' => 'Books', 'in_stock' => true],
            ['name' => 'The Pragmatic Programmer', 'description' => 'From journeyman to master by David Thomas and Andrew Hunt', 'price' => 42.99, 'category' => 'Books', 'in_stock' => true],
            ['name' => 'Design Patterns', 'description' => 'Elements of Reusable Object-Oriented Software by Gang of Four', 'price' => 54.99, 'category' => 'Books', 'in_stock' => false],
            ['name' => 'Refactoring', 'description' => 'Improving the Design of Existing Code by Martin Fowler', 'price' => 49.99, 'category' => 'Books', 'in_stock' => true],
            ['name' => 'Domain-Driven Design', 'description' => 'Tackling Complexity in the Heart of Software by Eric Evans', 'price' => 59.99, 'category' => 'Books', 'in_stock' => true],
            
            // Clothing
            ['name' => 'Cotton T-Shirt Premium', 'description' => '100% organic cotton t-shirt, comfortable and durable', 'price' => 29.99, 'category' => 'Clothing', 'in_stock' => true],
            ['name' => 'Denim Jeans Slim Fit', 'description' => 'Classic slim fit denim jeans with stretch comfort', 'price' => 79.99, 'category' => 'Clothing', 'in_stock' => true],
            ['name' => 'Winter Jacket Waterproof', 'description' => 'Warm and waterproof winter jacket with hood', 'price' => 149.99, 'category' => 'Clothing', 'in_stock' => false],
            ['name' => 'Running Shoes Pro', 'description' => 'Professional running shoes with advanced cushioning', 'price' => 129.99, 'category' => 'Clothing', 'in_stock' => true],
            ['name' => 'Leather Belt Classic', 'description' => 'Genuine leather belt with classic buckle design', 'price' => 49.99, 'category' => 'Clothing', 'in_stock' => true],
            
            // Home & Garden
            ['name' => 'Smart LED Bulbs (4-pack)', 'description' => 'WiFi-enabled smart LED bulbs with color changing', 'price' => 89.99, 'category' => 'Home & Garden', 'in_stock' => true],
            ['name' => 'Indoor Plant Collection', 'description' => 'Set of 3 low-maintenance indoor plants with pots', 'price' => 59.99, 'category' => 'Home & Garden', 'in_stock' => true],
            ['name' => 'Robot Vacuum Cleaner', 'description' => 'Smart robot vacuum with mapping and mopping function', 'price' => 399.99, 'category' => 'Home & Garden', 'in_stock' => false],
            ['name' => 'Coffee Maker Deluxe', 'description' => 'Programmable coffee maker with thermal carafe', 'price' => 129.99, 'category' => 'Home & Garden', 'in_stock' => true],
            ['name' => 'Yoga Mat Premium', 'description' => 'Extra thick yoga mat with alignment markers', 'price' => 39.99, 'category' => 'Home & Garden', 'in_stock' => true],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
