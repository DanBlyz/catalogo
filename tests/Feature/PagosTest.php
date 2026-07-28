<?php

use App\Livewire\Operacion\Pagos;
use App\Models\Caja;
use App\Models\Pago;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Livewire\Livewire;

test('pagos component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('operacion.pagos'))
        ->assertSuccessful();
});

test('user cannot open modal or create extra movement without active caja', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Pagos::class)
        ->call('openPagoModal', 'EGRESO_GASTO')
        ->assertDispatched('swal:modal');
});

test('user with active caja can register extra inflow and egreso gasto', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Pagos']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);

    $caja = Caja::create([
        'usuario_id' => $user->id,
        'sucursal_id' => $sucursal->id,
        'monto_apertura' => 100.00,
        'estado' => 'ABIERTA',
        'fecha_apertura' => now(),
    ]);

    // Create Egreso / Gasto (e.g. Almuerzo)
    Livewire::actingAs($user)
        ->test(Pagos::class)
        ->call('openPagoModal', 'EGRESO_GASTO')
        ->set('monto', 25.50)
        ->set('metodoPago', 'EFECTIVO')
        ->set('concepto', 'Compra de almuerzo del personal')
        ->call('savePago')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('pagos', [
        'caja_id' => $caja->id,
        'metodo_pago' => 'EFECTIVO',
        'monto' => -25.50,
        'usuario_creador_id' => $user->id,
    ]);

    // Create Ingreso Extra (e.g. Reposición)
    Livewire::actingAs($user)
        ->test(Pagos::class)
        ->call('openPagoModal', 'INGRESO_EXTRA')
        ->set('monto', 50.00)
        ->set('metodoPago', 'EFECTIVO')
        ->set('concepto', 'Aporte adicional caja chica')
        ->call('savePago')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('pagos', [
        'caja_id' => $caja->id,
        'metodo_pago' => 'EFECTIVO',
        'monto' => 50.00,
        'usuario_creador_id' => $user->id,
    ]);
});

test('admin can filter payments by cashier while regular user sees only their own', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Filter']);
    $adminRol = Rol::create(['nombre' => 'Administrador', 'codigo' => 'ADMIN']);
    $cajeroRol = Rol::create(['nombre' => 'Cajero', 'codigo' => 'CAJERO']);

    $admin = User::factory()->create(['rol_id' => $adminRol->id, 'sucursal_id' => $sucursal->id]);
    $cajero1 = User::factory()->create(['rol_id' => $cajeroRol->id, 'sucursal_id' => $sucursal->id]);
    $cajero2 = User::factory()->create(['rol_id' => $cajeroRol->id, 'sucursal_id' => $sucursal->id]);

    $caja = Caja::create([
        'usuario_id' => $admin->id,
        'sucursal_id' => $sucursal->id,
        'monto_apertura' => 100.00,
        'estado' => 'ABIERTA',
        'fecha_apertura' => now(),
    ]);

    Pago::create([
        'caja_id' => $caja->id,
        'metodo_pago' => 'EFECTIVO',
        'monto' => -20,
        'referencia_transaccion' => '[EGRESO_GASTO] Gasto Cajero 1',
        'fecha_pago' => now(),
        'usuario_creador_id' => $cajero1->id,
    ]);

    Pago::create([
        'caja_id' => $caja->id,
        'metodo_pago' => 'EFECTIVO',
        'monto' => -30,
        'referencia_transaccion' => '[EGRESO_GASTO] Gasto Cajero 2',
        'fecha_pago' => now(),
        'usuario_creador_id' => $cajero2->id,
    ]);

    // Admin sees both
    Livewire::actingAs($admin)
        ->test(Pagos::class)
        ->assertSee('Gasto Cajero 1')
        ->assertSee('Gasto Cajero 2');

    // Admin filters by Cajero 1
    Livewire::actingAs($admin)
        ->test(Pagos::class)
        ->set('usuarioFilter', $cajero1->id)
        ->assertSee('Gasto Cajero 1')
        ->assertDontSee('Gasto Cajero 2');

    // Cajero 1 sees only their own
    Livewire::actingAs($cajero1)
        ->test(Pagos::class)
        ->assertSee('Gasto Cajero 1')
        ->assertDontSee('Gasto Cajero 2');
});
