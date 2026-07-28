<?php

use App\Livewire\Operacion\Ventas;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ProductoSucursal;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use Livewire\Livewire;

test('ventas component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('operacion.ventas'))
        ->assertSuccessful();
});

test('user cannot process sale without active caja', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal POS']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);

    Livewire::actingAs($user)
        ->test(Ventas::class)
        ->call('saveVenta')
        ->assertDispatched('swal:modal');
});

test('user can process sale with discounts and active caja', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Main']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);
    $cliente = Cliente::create(['nombre_razon_social' => 'Cliente Test', 'cedula_nit_ruc' => '12345']);

    $caja = Caja::create([
        'usuario_id' => $user->id,
        'sucursal_id' => $sucursal->id,
        'monto_apertura' => 100.00,
        'estado' => 'ABIERTA',
        'fecha_apertura' => now(),
    ]);

    $producto = Producto::create(['nombre' => 'Monitor LG', 'sku' => 'MON-LG', 'precio_compra' => 100, 'precio_venta' => 150]);

    ProductoSucursal::create([
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursal->id,
        'stock_actual' => 10,
    ]);

    Livewire::actingAs($user)
        ->test(Ventas::class)
        ->call('addToCart', $producto->id, 2)
        ->call('updateCartItemDiscount', 0, 10.00)
        ->set('descuentoGeneral', 5.00)
        ->set('cliente_id', $cliente->id)
        ->set('metodoPago', 'EFECTIVO')
        ->set('montoPagado', 300.00)
        ->call('saveVenta')
        ->assertHasNoErrors()
        ->assertDispatched('swal:venta-success');

    $this->assertDatabaseHas('ventas', [
        'usuario_id' => $user->id,
        'cliente_id' => $cliente->id,
        'caja_id' => $caja->id,
        'total' => 285.00,
    ]);

    $this->assertDatabaseHas('producto_sucursal', [
        'producto_id' => $producto->id,
        'sucursal_id' => $sucursal->id,
        'stock_actual' => 8,
    ]);
});

test('user can generate pdf ticket receipt stream for a completed sale', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal PDF']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);
    $venta = Venta::create([
        'numero_recibo' => 'REC-TEST-001',
        'sucursal_id' => $sucursal->id,
        'usuario_id' => $user->id,
        'subtotal' => 100.00,
        'total' => 100.00,
        'metodo_pago_principal' => 'EFECTIVO',
        'fecha_venta' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('operacion.ventas.recibo', $venta->id))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});
