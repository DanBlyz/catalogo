<?php

use App\Livewire\Persona\Clientes;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Venta;
use Livewire\Livewire;

test('clientes component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('persona.clientes'))
        ->assertSuccessful();
});

test('user can create a new cliente', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Clientes::class)
        ->call('openModal')
        ->set('nombre_razon_social', 'Empresa Ejemplo SRL')
        ->set('cedula_nit_ruc', '1020304050')
        ->set('telefono', '70011223')
        ->set('email', 'contacto@ejemplo.com')
        ->set('direccion', 'Av. Las Palmas #789')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('clientes', [
        'nombre_razon_social' => 'Empresa Ejemplo SRL',
        'cedula_nit_ruc' => '1020304050',
    ]);
});

test('user can update an existing cliente', function () {
    $user = User::factory()->create();
    $cliente = Cliente::create([
        'nombre_razon_social' => 'Carlos López',
        'telefono' => '71112233',
    ]);

    Livewire::actingAs($user)
        ->test(Clientes::class)
        ->call('edit', $cliente->id)
        ->set('nombre_razon_social', 'Carlos López Vargas')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('clientes', [
        'id' => $cliente->id,
        'nombre_razon_social' => 'Carlos López Vargas',
    ]);
});

test('user can soft delete a cliente without sales', function () {
    $user = User::factory()->create();
    $cliente = Cliente::create([
        'nombre_razon_social' => 'Cliente Sin Ventas',
    ]);

    Livewire::actingAs($user)
        ->test(Clientes::class)
        ->call('delete', $cliente->id)
        ->assertDispatched('swal:modal');

    $this->assertSoftDeleted('clientes', [
        'id' => $cliente->id,
    ]);
});

test('user cannot delete a cliente with associated sales', function () {
    $user = User::factory()->create();
    $cliente = Cliente::create([
        'nombre_razon_social' => 'Cliente Frecuente',
    ]);

    Venta::create([
        'cliente_id' => $cliente->id,
        'usuario_id' => $user->id,
        'numero_comprobante' => 'VNT-0001',
        'total' => 150.00,
    ]);

    Livewire::actingAs($user)
        ->test(Clientes::class)
        ->call('delete', $cliente->id)
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('clientes', [
        'id' => $cliente->id,
        'deleted_at' => null,
    ]);
});
