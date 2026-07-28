<?php

use App\Livewire\Administracion\Sucursales;
use App\Models\Sucursal;
use App\Models\User;
use Database\Seeders\SucursalSeeder;
use Livewire\Livewire;

test('sucursales component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('administracion.sucursales'))
        ->assertSuccessful();
});

test('user can create a new sucursal', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Sucursales::class)
        ->call('openModal')
        ->set('nombre', 'Sucursal Norte')
        ->set('direccion', 'Av. Banzer 4to Anillo')
        ->set('telefono', '71111111')
        ->set('es_principal', false)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('sucursales', [
        'nombre' => 'Sucursal Norte',
        'telefono' => '71111111',
    ]);
});

test('user can update an existing sucursal', function () {
    $user = User::factory()->create();
    $sucursal = Sucursal::create([
        'nombre' => 'Sucursal Este',
        'direccion' => 'Zona Este #456',
        'telefono' => '72222222',
        'es_principal' => false,
    ]);

    Livewire::actingAs($user)
        ->test(Sucursales::class)
        ->call('edit', $sucursal->id)
        ->set('nombre', 'Sucursal Este Renovada')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('sucursales', [
        'id' => $sucursal->id,
        'nombre' => 'Sucursal Este Renovada',
    ]);
});

test('user can soft delete a secondary sucursal', function () {
    $this->seed(SucursalSeeder::class);
    $user = User::factory()->create();
    $sucursal = Sucursal::create([
        'nombre' => 'Sucursal Temporal',
        'direccion' => 'Calle Falsa 123',
        'telefono' => '73333333',
        'es_principal' => false,
    ]);

    Livewire::actingAs($user)
        ->test(Sucursales::class)
        ->call('delete', $sucursal->id)
        ->assertDispatched('swal:modal');

    $this->assertSoftDeleted('sucursales', [
        'id' => $sucursal->id,
    ]);
});
