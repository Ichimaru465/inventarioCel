<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(UserSeeder::class); // La que ya tenías para tu usuario
        $this->call(CategorySeeder::class); // <-- AÑADE ESTA LÍNEA
        $this->call(ProductSeeder::class); // <-- Y ESTA LÍNEA
        $this->call(AttributeSeeder::class); // <-- Y ESTA LÍNEA
    }
}
