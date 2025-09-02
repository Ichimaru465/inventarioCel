<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\Category;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear todos los atributos posibles para todo el inventario
        $attributes = collect([
            'Tipo', 'Color', 'Material', 'Diseño', 'Acabado',
            'Tipo de Conector', 'Tipo de Conector A', 'Tipo de Conector B',
            'Longitud', 'Potencia (Watts)', 'Capacidad (mAh)',
            'Compatibilidad', 'Marca Compatible', 'Modelo Compatible',
            'Conectividad', 'Idioma',
            'Almacenamiento (GB)', 'RAM (GB)', 'Pantalla (pulgadas)',
            'Tipo de Repuesto', 'Tipo de Foco', 'Tipo de Rosca', 'Color de Luz', 'Genero'
        ])->map(function ($name) {
            return Attribute::firstOrCreate(['name' => $name]);
        });

        // Helper para buscar atributos por nombre
        $findAttribute = fn($name) => $attributes->where('name', $name)->first()->id;

        // 2. Definir el mapa de qué atributos pertenecen a cada categoría
        $categoryAttributesMap = [
            'Cargadores y Baterías' => [
                $findAttribute('Tipo'), // Ej: Cargador de Pared, Batería AA
                $findAttribute('Tipo de Conector'),
                $findAttribute('Potencia (Watts)'),
                $findAttribute('Capacidad (mAh)'),
                $findAttribute('Compatibilidad')
            ],
            'Cables y Adaptadores' => [
                $findAttribute('Tipo de Conector A'),
                $findAttribute('Tipo de Conector B'),
                $findAttribute('Longitud'),
                $findAttribute('Color'),
                $findAttribute('Material')
            ],
            'Componentes de Cómputo' => [
                $findAttribute('Tipo'), // Ej: Mouse, Teclado
                $findAttribute('Conectividad'), // Ej: Inalámbrico, USB
                $findAttribute('Color'),
                $findAttribute('Idioma')
            ],
            'Audio y Video' => [
                $findAttribute('Tipo'), // Ej: Parlante, Audífono
                $findAttribute('Conectividad'),
                $findAttribute('Potencia (Watts)'),
                $findAttribute('Color')
            ],
            'Celulares' => [
                $findAttribute('Color'),
                $findAttribute('Almacenamiento (GB)'),
                $findAttribute('RAM (GB)'),
                $findAttribute('Pantalla (pulgadas)')
            ],
            'Accesorios para Celulares' => [
                $findAttribute('Tipo'), // Ej: Protector, Vidrio Templado, Hidrogel
                $findAttribute('Material'),
                $findAttribute('Diseño'),
                $findAttribute('Acabado'), // Ej: Transparente, Matte, Antiespía
                $findAttribute('Compatibilidad'), // Ej: iPhone 15, Samsung S24
                $findAttribute('Genero') // Ej: Transpar
            ],
            'Repuestos de Electrodomésticos' => [
                $findAttribute('Tipo de Repuesto'), // Ej: Cuchilla de licuadora, Vaso
                $findAttribute('Marca Compatible'), // Ej: Oster, Philips
                $findAttribute('Modelo Compatible')
            ],
            'Iluminación' => [
                $findAttribute('Tipo de Foco'), // Ej: LED, Ahorrador
                $findAttribute('Potencia (Watts)'),
                $findAttribute('Tipo de Rosca'), // Ej: E27
                $findAttribute('Color de Luz') // Ej: Cálida, Fría
            ]
        ];

        // 3. Recorrer el mapa y asignar las relaciones
        foreach ($categoryAttributesMap as $categoryName => $attributeIds) {
            $category = Category::where('name', $categoryName)->first();
            if ($category) {
                // sync() es la forma ideal de adjuntar relaciones en un seeder
                $category->attributes()->sync($attributeIds);
            }
        }
    }
}
