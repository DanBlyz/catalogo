<?php

use App\Livewire\Persona\Proveedores;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Livewire\Livewire;

test('proveedores component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('persona.proveedores'))
        ->assertSuccessful();
});

test('user can create a new proveedor', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Proveedores::class)
        ->call('openModal')
        ->set('nombre_empresa', 'Distribuidora Global SA')
        ->set('contacto_nombre', 'Ing. Roberto Gómez')
        ->set('nit_ruc', '102030405060')
        ->set('telefono', '4-4455667')
        ->set('email', 'ventas@global.com')
        ->set('direccion', 'Av. Circunvalación #100')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('proveedores', [
        'nombre_empresa' => 'Distribuidora Global SA',
        'nit_ruc' => '102030405060',
    ]);
});

test('user can update an existing proveedor', function () {
    $user = User::factory()->create();
    $proveedor = Proveedor::create([
        'nombre_empresa' => 'Importadora Sur',
        'telefono' => '72223344',
    ]);

    Livewire::actingAs($user)
        ->test(Proveedores::class)
        ->call('edit', $proveedor->id)
        ->set('nombre_empresa', 'Importadora Sur SRL')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('proveedores', [
        'id' => $proveedor->id,
        'nombre_empresa' => 'Importadora Sur SRL',
    ]);
});

test('user can soft delete a proveedor without products', function () {
    $user = User::factory()->create();
    $proveedor = Proveedor::create([
        'nombre_empresa' => 'Proveedor Sin Productos',
    ]);

    Livewire::actingAs($user)
        ->test(Proveedores::class)
        ->call('delete', $proveedor->id)
        ->assertDispatched('swal:modal');

    $this->assertSoftDeleted('proveedores', [
        'id' => $proveedor->id,
    ]);
});

test('user cannot delete a proveedor with associated products', function () {
    $user = User::factory()->create();
    $proveedor = Proveedor::create([
        'nombre_empresa' => 'Proveedor Con Productos',
    ]);

    Producto::create([
        'nombre' => 'Producto De Muestra',
        'sku' => 'MUESTRA-01',
        'proveedor_id' => $proveedor->id,
    ]);

    Livewire::actingAs($user)
        ->test(Proveedores::class)
        ->call('delete', $proveedor->id)
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('proveedores', [
        'id' => $proveedor->id,
        'deleted_at' => null,
    ]);
});
