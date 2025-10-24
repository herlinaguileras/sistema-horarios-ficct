<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role; // <-- Import the Role model

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin role
        Role::create([
            'name' => 'admin',
            'description' => 'Administrador del Sistema'
        ]);

        // Create Docente role
        Role::create([
            'name' => 'docente',
            'description' => 'Docente de la Facultad'
        ]);

        

        // You can add more roles here later (e.g., 'coordinador')
    }
}
