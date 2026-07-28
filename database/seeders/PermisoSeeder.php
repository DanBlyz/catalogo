<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            // Administración
            ['nombre' => 'Gestionar Roles', 'codigo' => 'roles.gestionar', 'modulo' => 'ADMINISTRACION', 'descripcion' => 'Crear, editar y eliminar roles del sistema'],
            ['nombre' => 'Gestionar Sucursales', 'codigo' => 'sucursales.gestionar', 'modulo' => 'ADMINISTRACION', 'descripcion' => 'Crear y administrar sucursales de la empresa'],
            ['nombre' => 'Gestionar Permisos', 'codigo' => 'permisos.gestionar', 'modulo' => 'ADMINISTRACION', 'descripcion' => 'Asignar y revocar permisos a usuarios'],
            ['nombre' => 'Gestionar Usuarios', 'codigo' => 'usuarios.gestionar', 'modulo' => 'ADMINISTRACION', 'descripcion' => 'Crear, editar, activar o desactivar usuarios'],
            ['nombre' => 'Ver Reportes', 'codigo' => 'reportes.ver', 'modulo' => 'ADMINISTRACION', 'descripcion' => 'Acceso al módulo de reportes y estadísticas'],
            ['nombre' => 'Exportar Reportes PDF', 'codigo' => 'reportes.exportar', 'modulo' => 'ADMINISTRACION', 'descripcion' => 'Generar e imprimir informes ejecutivos en PDF'],

            // Catálogo
            ['nombre' => 'Gestionar Categorías', 'codigo' => 'categorias.gestionar', 'modulo' => 'CATALOGO', 'descripcion' => 'Administrar categorías de productos'],
            ['nombre' => 'Gestionar Marcas', 'codigo' => 'marcas.gestionar', 'modulo' => 'CATALOGO', 'descripcion' => 'Administrar marcas de productos'],
            ['nombre' => 'Ver Productos', 'codigo' => 'productos.ver', 'modulo' => 'CATALOGO', 'descripcion' => 'Visualizar el catálogo de productos y stock'],
            ['nombre' => 'Crear Productos', 'codigo' => 'productos.crear', 'modulo' => 'CATALOGO', 'descripcion' => 'Registrar nuevos productos en el catálogo'],
            ['nombre' => 'Editar Productos', 'codigo' => 'productos.editar', 'modulo' => 'CATALOGO', 'descripcion' => 'Modificar datos, precios o imágenes de productos'],
            ['nombre' => 'Eliminar Productos', 'codigo' => 'productos.eliminar', 'modulo' => 'CATALOGO', 'descripcion' => 'Eliminar o desactivar productos'],

            // Personas
            ['nombre' => 'Gestionar Clientes', 'codigo' => 'clientes.gestionar', 'modulo' => 'PERSONAS', 'descripcion' => 'Crear, editar y listar clientes'],
            ['nombre' => 'Gestionar Proveedores', 'codigo' => 'proveedores.gestionar', 'modulo' => 'PERSONAS', 'descripcion' => 'Crear, editar y listar proveedores'],

            // Operación
            ['nombre' => 'Ver Cajas', 'codigo' => 'cajas.ver', 'modulo' => 'OPERACION', 'descripcion' => 'Ver sesiones y estado de cajas'],
            ['nombre' => 'Aperturar Caja', 'codigo' => 'cajas.aperturar', 'modulo' => 'OPERACION', 'descripcion' => 'Realizar la apertura inicial de caja del día'],
            ['nombre' => 'Cerrar Caja', 'codigo' => 'cajas.cerrar', 'modulo' => 'OPERACION', 'descripcion' => 'Realizar el arqueo y cierre definitivo de caja'],

            ['nombre' => 'Ver Movimientos', 'codigo' => 'movimientos.ver', 'modulo' => 'OPERACION', 'descripcion' => 'Ver historial de movimientos e ingresos/salidas de inventario'],
            ['nombre' => 'Realizar Ingreso Stock', 'codigo' => 'movimientos.ingreso', 'modulo' => 'OPERACION', 'descripcion' => 'Agregar nuevo stock y actualizar precios de compra/venta'],
            ['nombre' => 'Realizar Salida Stock', 'codigo' => 'movimientos.salida', 'modulo' => 'OPERACION', 'descripcion' => 'Dar de baja stock por pérdida, daño o vencimiento'],

            ['nombre' => 'Realizar Ventas POS', 'codigo' => 'ventas.crear', 'modulo' => 'OPERACION', 'descripcion' => 'Acceso al módulo Punto de Venta (POS) y emitir cobros'],
            ['nombre' => 'Ver Ventas del Día', 'codigo' => 'ventas.ver', 'modulo' => 'OPERACION', 'descripcion' => 'Consultar historial de ventas y reimprimir recibos'],
            ['nombre' => 'Anular Ventas', 'codigo' => 'ventas.anular', 'modulo' => 'OPERACION', 'descripcion' => 'Anular ventas emitidas y devolver stock a inventario'],
            ['nombre' => 'Procesar Devoluciones', 'codigo' => 'ventas.devolucion', 'modulo' => 'OPERACION', 'descripcion' => 'Aceptar devolución de ítems vendidos'],

            ['nombre' => 'Ver Pagos y Gastos', 'codigo' => 'pagos.ver', 'modulo' => 'OPERACION', 'descripcion' => 'Consultar listado de ingresos y gastos de caja'],
            ['nombre' => 'Registrar Pagos / Gastos', 'codigo' => 'pagos.crear', 'modulo' => 'OPERACION', 'descripcion' => 'Ingresar pagos extras o egresos de caja por compras/almuerzos'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::updateOrCreate(['codigo' => $permiso['codigo']], $permiso);
        }
    }
}
