<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegúrate de que las categorías ya existen antes de ejecutar este seeder
        // Puedes usar Category::firstOrCreate() si no estás seguro

        Product::create([
            'name' => 'Cargador Rápido 20W',
            'sku' => 'CR-20W-001',
            'price' => 30.00,
            'cost' => 15.00,
            'quantity' => 50,
            'category_id' => 1, // ID de 'Cargadores y Baterías'
            'attributes' => [
                'potencia' => '20W',
                'tipo_conector' => 'USB-C',
                'color' => 'blanco'
            ]
        ]);

        Product::create([
            'name' => 'Cable USB',
                'sku' => 'CB-USB-C-1M',
                'price' => 15.00,
                'cost' => 7.50,
                'quantity' => 100,
                'category_id' => 2, // ID de 'Cables y Adaptadores'
                'attributes' => [
                    'tipo_conector' => 'USB-C',
                    'longitud' => '1m',
                    'color' => 'negro'
                ]
            ]);

        Product::create([
            'name' => 'Protector para Samsung A54',
            'sku' => 'PRO-SA54-360',
            'price' => 25.00,
            'cost' => 12.00,
            'quantity' => 50,
            'category_id' => 6, // ID de 'Accesorios para Celulares'
            'attributes' => [
                'tipo' => 'Protector',
                'diseño' => '360',
                'material' => 'Acrílico'
            ]
        ]);
    }
}
