<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            // Productos
            ['nombre' => 'Ver Productos', 'codigo' => 'productos.ver', 'modulo' => 'PRODUCTOS'],
            ['nombre' => 'Crear Productos', 'codigo' => 'productos.crear', 'modulo' => 'PRODUCTOS'],
            ['nombre' => 'Editar Productos', 'codigo' => 'productos.editar', 'modulo' => 'PRODUCTOS'],
            ['nombre' => 'Eliminar Productos', 'codigo' => 'productos.eliminar', 'modulo' => 'PRODUCTOS'],

            // Ventas
            ['nombre' => 'Crear Ventas', 'codigo' => 'ventas.crear', 'modulo' => 'VENTAS'],
            ['nombre' => 'Ver Ventas', 'codigo' => 'ventas.ver', 'modulo' => 'VENTAS'],
            ['nombre' => 'Anular Ventas', 'codigo' => 'ventas.anular', 'modulo' => 'VENTAS'],

            // Cajas
            ['nombre' => 'Aperturar Caja', 'codigo' => 'cajas.aperturar', 'modulo' => 'CAJAS'],
            ['nombre' => 'Cerrar Caja', 'codigo' => 'cajas.cerrar', 'modulo' => 'CAJAS'],
            ['nombre' => 'Ver Cajas', 'codigo' => 'cajas.ver', 'modulo' => 'CAJAS'],

            // Clientes y Proveedores
            ['nombre' => 'Gestionar Clientes', 'codigo' => 'clientes.gestionar', 'modulo' => 'CLIENTES'],
            ['nombre' => 'Gestionar Proveedores', 'codigo' => 'proveedores.gestionar', 'modulo' => 'PROVEEDORES'],

            // Usuarios & Permisos
            ['nombre' => 'Gestionar Usuarios', 'codigo' => 'usuarios.gestionar', 'modulo' => 'USUARIOS'],
            ['nombre' => 'Gestionar Permisos', 'codigo' => 'permisos.gestionar', 'modulo' => 'PERMISOS'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::updateOrCreate(['codigo' => $permiso['codigo']], $permiso);
        }
    }
}
