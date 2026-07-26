<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        Rol::updateOrCreate(['id' => 1], [
            'nombre' => 'Administrador',
            'codigo' => 'ADMIN',
            'descripcion' => 'Acceso total al sistema y gestión de permisos',
        ]);

        Rol::updateOrCreate(['id' => 2], [
            'nombre' => 'Vendedor',
            'codigo' => 'VENDEDOR',
            'descripcion' => 'Registro de ventas y consulta de productos',
        ]);

        Rol::updateOrCreate(['id' => 3], [
            'nombre' => 'Cajero',
            'codigo' => 'CAJERO',
            'descripcion' => 'Apertura/cierre de caja y cobro de ventas',
        ]);

        Rol::updateOrCreate(['id' => 4], [
            'nombre' => 'Almacenero',
            'codigo' => 'ALMACENERO',
            'descripcion' => 'Gestión de productos, stock e ingresos/salidas de inventario',
        ]);
    }
}
