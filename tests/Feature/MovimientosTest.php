<?php

use App\Livewire\Operacion\Movimientos;
use App\Models\Producto;
use App\Models\ProductoSucursal;
use App\Models\Sucursal;
use App\Models\User;
use Livewire\Livewire;

test('movimientos component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('operacion.movimientos'))
        ->assertSuccessful();
});

test('user can process multi product stock inflow', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Inflow']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);
    $producto1 = Producto::create(['nombre' => 'Prod 1', 'sku' => 'P1', 'precio_compra' => 10, 'precio_venta' => 20]);
    $producto2 = Producto::create(['nombre' => 'Prod 2', 'sku' => 'P2', 'precio_compra' => 15, 'precio_venta' => 30]);

    Livewire::actingAs($user)
        ->test(Movimientos::class)
        ->call('openIngresoModal')
        ->set('selectedProductoIdForIngreso', $producto1->id)
        ->set('cantidadIngreso', 5)
        ->set('precioCompraIngreso', 16)
        ->set('precioVentaIngreso', 22)
        ->call('addItemToIngresoList')
        ->set('selectedProductoIdForIngreso', $producto2->id)
        ->set('cantidadIngreso', 10)
        ->set('precioCompraIngreso', 15)
        ->set('precioVentaIngreso', 35)
        ->call('addItemToIngresoList')
        ->call('saveIngresoLote')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('producto_sucursal', [
        'producto_id' => $producto1->id,
        'sucursal_id' => $sucursal->id,
        'stock_actual' => 5,
    ]);

    $this->assertDatabaseHas('productos', [
        'id' => $producto1->id,
        'precio_compra' => 16,
        'precio_venta' => 22,
    ]);
});

test('user can process single product stock outflow', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Outflow']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);
    $producto = Producto::create(['nombre' => 'Prod Out', 'sku' => 'POUT', 'precio_compra' => 10, 'precio_venta' => 20]);

    ProductoSucursal::create([
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursal->id,
        'stock_actual' => 25,
    ]);

    Livewire::actingAs($user)
        ->test(Movimientos::class)
        ->call('openSalidaModal')
        ->set('selectedProductoIdForSalida', $producto->id)
        ->set('cantidadSalida', 5)
        ->set('motivoSalida', 'PRODUCTO VENCIDO')
        ->call('saveSalida')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('producto_sucursal', [
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursal->id,
        'stock_actual' => 20,
    ]);

    $this->assertDatabaseHas('movimientos_inventario', [
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursal->id,
        'tipo_movimiento' => 'SALIDA',
        'cantidad' => 5,
    ]);
});

test('user can process inter branch transfer', function () {
    $sucursalOrigen = Sucursal::create(['nombre' => 'Origen']);
    $sucursalDestino = Sucursal::create(['nombre' => 'Destino']);
    $user = User::factory()->create(['sucursal_id' => $sucursalOrigen->id]);
    $producto = Producto::create(['nombre' => 'Prod Transfer', 'sku' => 'PTR', 'precio_compra' => 10, 'precio_venta' => 20]);

    ProductoSucursal::create([
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursalOrigen->id,
        'stock_actual' => 50,
    ]);

    Livewire::actingAs($user)
        ->test(Movimientos::class)
        ->call('openTransferenciaModal')
        ->set('selectedProductoIdForTransferencia', $producto->id)
        ->set('sucursalDestinoId', $sucursalDestino->id)
        ->set('cantidadTransferencia', 15)
        ->call('saveTransferencia')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('producto_sucursal', [
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursalOrigen->id,
        'stock_actual' => 35,
    ]);

    $this->assertDatabaseHas('producto_sucursal', [
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursalDestino->id,
        'stock_actual' => 15,
    ]);
});
