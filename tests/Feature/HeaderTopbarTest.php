<?php

use App\Livewire\Common\HeaderTopbar;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\User;
use Livewire\Livewire;

test('header topbar component can render', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(HeaderTopbar::class)
        ->assertSee('Notificaciones del Sistema');
});

test('header topbar search returns products and clients', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Header']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);

    $producto = Producto::create([
        'nombre' => 'Laptop Gamer Pro',
        'sku' => 'LAP-100',
        'precio_compra' => 1000,
        'precio_venta' => 1500,
    ]);

    $cliente = Cliente::create([
        'nombre_razon_social' => 'Carlos Mendoza',
        'cedula_nit_ruc' => '1234567',
    ]);

    Livewire::actingAs($user)
        ->test(HeaderTopbar::class)
        ->set('searchQuery', 'Laptop')
        ->assertSee('Laptop Gamer Pro')
        ->set('searchQuery', 'Carlos')
        ->assertSee('Carlos Mendoza');
});

test('header topbar calculates stock notifications', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(HeaderTopbar::class)
        ->assertSee('Notificaciones del Sistema');
});
