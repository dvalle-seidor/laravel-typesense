<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class RobotProductSeeder extends Seeder
{
    public function run()
    {
        $robots = [
            [
                'name' => 'Robot Aspirador Inteligente Roomba i7+',
                'description' => 'Robot aspirador con mapeo láser, vacío automático y control por app. Ideal para hogares con mascotas.',
                'price' => 599.99,
                'category' => 'Electronics',
                'in_stock' => true,
            ],
            [
                'name' => 'Robot de Cocina Thermomix TM6',
                'description' => 'Robot multifunción que cocina, tritura, mezcla y más. Con pantalla táctil y conectividad WiFi.',
                'price' => 1299.99,
                'category' => 'Electronics',
                'in_stock' => true,
            ],
            [
                'name' => 'Robot Educativo LEGO Mindstorms EV3',
                'description' => 'Kit de robótica educativa para programar y construir tus propios robots. Compatible con tablets y smartphones.',
                'price' => 349.99,
                'category' => 'Electronics',
                'in_stock' => false,
            ],
            [
                'name' => 'Robot de Seguridad Arlo Ultra',
                'description' => 'Sistema de seguridad con robot móvil, visión nocturna 4K y detección de movimiento inteligente.',
                'price' => 449.99,
                'category' => 'Electronics',
                'in_stock' => true,
            ],
            [
                'name' => 'Robot Mascota AIBO Sony',
                'description' => 'Robots mascota con IA avanzada, reconocimiento facial y comportamiento emocional. Compañero robótico interactivo.',
                'price' => 2899.99,
                'category' => 'Electronics',
                'in_stock' => true,
            ],
        ];

        foreach ($robots as $robot) {
            Product::create($robot);
        }

        $this->command->info('Robot products seeded successfully!');
    }
}
