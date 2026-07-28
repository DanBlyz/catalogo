<?php

use App\Livewire\Operacion\VentasDia;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\ProductoSucursal;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use Livewire\Livewire;

test('ventas dia component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('operacion.ventas-dia'))
        ->assertSuccessful();
});

test('admin can view all sales in branch while regular user sees only their own', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Test Ventas']);
    $adminRol = Rol::create(['nombre' => 'Administrador', 'codigo' => 'ADMIN']);
    $cajeroRol = Rol::create(['nombre' => 'Cajero', 'codigo' => 'CAJERO']);

    $admin = User::factory()->create(['rol_id' => $adminRol->id, 'sucursal_id' => $sucursal->id]);
    $cajero1 = User::factory()->create(['rol_id' => $cajeroRol->id, 'sucursal_id' => $sucursal->id]);
    $cajero2 = User::factory()->create(['rol_id' => $cajeroRol->id, 'sucursal_id' => $sucursal->id]);

    $venta1 = Venta::create([
        'numero_recibo' => 'REC-1001',
        'sucursal_id' => $sucursal->id,
        'usuario_id' => $cajero1->id,
        'subtotal' => 100,
        'total' => 100,
        'estado' => 'COMPLETADA',
        'fecha_venta' => now(),
    ]);

    $venta2 = Venta::create([
        'numero_recibo' => 'REC-1002',
        'sucursal_id' => $sucursal->id,
        'usuario_id' => $cajero2->id,
        'subtotal' => 150,
        'total' => 150,
        'estado' => 'COMPLETADA',
        'fecha_venta' => now(),
    ]);

    // Admin sees both sales
    Livewire::actingAs($admin)
        ->test(VentasDia::class)
        ->assertSee('REC-1001')
        ->assertSee('REC-1002');

    // Cajero 1 sees only REC-1001
    Livewire::actingAs($cajero1)
        ->test(VentasDia::class)
        ->assertSee('REC-1001')
        ->assertDontSee('REC-1002');
});

test('user can cancel sale and restore stock to sucursal', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Return']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);
    $producto = Producto::create(['nombre' => 'Prod Return', 'sku' => 'PRET', 'precio_compra' => 10, 'precio_venta' => 20]);

    ProductoSucursal::create([
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursal->id,
        'stock_actual' => 5,
    ]);

    $venta = Venta::create([
        'numero_recibo' => 'REC-CANCEL-01',
        'sucursal_id' => $sucursal->id,
        'usuario_id' => $user->id,
        'subtotal' => 60,
        'total' => 60,
        'estado' => 'COMPLETADA',
        'fecha_venta' => now(),
    ]);

    DetalleVenta::create([
        'venta_id' => $venta->id,
        'producto_id' => $producto->id,
        'cantidad' => 3,
        'precio_unitario' => 20,
        'subtotal' => 60,
    ]);

    Livewire::actingAs($user)
        ->test(VentasDia::class)
        ->call('openAnularModal', $venta->id)
        ->set('motivoAnulacion', 'Cliente solicitó devolución de producto')
        ->call('anularVenta')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('ventas', [
        'id' => $venta->id,
        'estado' => 'ANULADA',
    ]);

    // Stock should be 5 + 3 = 8
    $this->assertDatabaseHas('producto_sucursal', [
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursal->id,
        'stock_actual' => 8,
    ]);

    $this->assertDatabaseHas('movimientos_inventario', [
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursal->id,
        'tipo_movimiento' => 'DEVOLUCION_VENTA',
        'cantidad' => 3,
    ]);
});
