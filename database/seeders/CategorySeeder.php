<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Cargadores y Baterías'],
            ['name' => 'Cables y Adaptadores'],
            ['name' => 'Componentes de Cómputo'],
            ['name' => 'Audio y Video'],
            ['name' => 'Celulares'],
            ['name' => 'Accesorios para Celulares'],
            ['name' => 'Repuestos de Electrodomésticos'],
            ['name' => 'Iluminación'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
