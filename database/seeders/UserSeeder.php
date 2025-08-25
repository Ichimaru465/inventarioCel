<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear Administrador
    User::create([
        'name' => 'Administrador',
        'email' => 'Admin@inventaCelAdmin.com',
        'password' => Hash::make('InventaCel2024!'),
        'role' => 'admin', // Asignación directa del rol
    ]);

    // Crear Empleado
    User::create([
        'name' => 'Empleado Uno',
        'email' => 'Empleado@inventaCelEmpleado.com',
        'password' => Hash::make('#Empleado2024'),
        'role' => 'employee', // Asignación directa del rol
    ]);
    }
}
