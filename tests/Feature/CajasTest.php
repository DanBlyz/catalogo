<?php

use App\Livewire\Operacion\Cajas;
use App\Models\Caja;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Livewire\Livewire;

test('cajas component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('operacion.cajas'))
        ->assertSuccessful();
});

test('user can open a new caja assigned to their sucursal', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Test']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);

    Livewire::actingAs($user)
        ->test(Cajas::class)
        ->call('openAperturaModal')
        ->set('monto_apertura', 150.00)
        ->set('observaciones_apertura', 'Apertura inicial turno mañana')
        ->call('saveApertura')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('cajas', [
        'usuario_id' => $user->id,
        'sucursal_id' => $sucursal->id,
        'monto_apertura' => 150.00,
        'estado' => 'ABIERTA',
    ]);
});

test('user cannot open a second caja if they already have an active open box', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Central']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);

    Caja::create([
        'usuario_id' => $user->id,
        'sucursal_id' => $sucursal->id,
        'monto_apertura' => 100.00,
        'estado' => 'ABIERTA',
        'fecha_apertura' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(Cajas::class)
        ->call('openAperturaModal')
        ->assertDispatched('swal:modal');
});

test('user can close their open caja', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Central 2']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);

    $caja = Caja::create([
        'usuario_id' => $user->id,
        'sucursal_id' => $sucursal->id,
        'monto_apertura' => 200.00,
        'estado' => 'ABIERTA',
        'fecha_apertura' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(Cajas::class)
        ->call('openCierreModal', $caja->id)
        ->set('monto_cierre', 200.00)
        ->call('saveCierre')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('cajas', [
        'id' => $caja->id,
        'monto_cierre' => 200.00,
        'estado' => 'CERRADA',
    ]);
});

test('admin can view all cajas in branch while regular user sees only their own', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Norte']);
    $adminRol = Rol::create(['nombre' => 'Administrador', 'codigo' => 'ADMIN']);
    $cajeroRol = Rol::create(['nombre' => 'Cajero', 'codigo' => 'CAJERO']);

    $admin = User::factory()->create(['rol_id' => $adminRol->id, 'sucursal_id' => $sucursal->id]);
    $cajero1 = User::factory()->create(['rol_id' => $cajeroRol->id, 'sucursal_id' => $sucursal->id]);
    $cajero2 = User::factory()->create(['rol_id' => $cajeroRol->id, 'sucursal_id' => $sucursal->id]);

    $caja1 = Caja::create(['usuario_id' => $cajero1->id, 'sucursal_id' => $sucursal->id, 'monto_apertura' => 100, 'estado' => 'ABIERTA', 'fecha_apertura' => now()]);
    $caja2 = Caja::create(['usuario_id' => $cajero2->id, 'sucursal_id' => $sucursal->id, 'monto_apertura' => 150, 'estado' => 'ABIERTA', 'fecha_apertura' => now()]);

    // Admin sees both cajas
    Livewire::actingAs($admin)
        ->test(Cajas::class)
        ->assertSee($cajero1->name)
        ->assertSee($cajero2->name);

    // Regular cajero sees only their own caja
    Livewire::actingAs($cajero1)
        ->test(Cajas::class)
        ->assertSee($cajero1->name)
        ->assertDontSee($cajero2->name);
});
