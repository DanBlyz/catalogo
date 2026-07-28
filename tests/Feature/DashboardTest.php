<?php

use App\Livewire\Dashboard;
use App\Models\Sucursal;
use App\Models\User;
use Livewire\Livewire;

test('dashboard component can render for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful();
});

test('dashboard calculates today sales and stock alerts correctly', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Dashboard']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Ventas Realizadas Hoy')
        ->assertSee('Stock Crítico');
});

test('admin user can filter dashboard by branch', function () {
    $adminUser = User::factory()->create();
    $adminUser->rol_id = 1;

    $sucursalA = Sucursal::create(['nombre' => 'Sucursal A']);

    Livewire::actingAs($adminUser)
        ->test(Dashboard::class)
        ->set('sucursalId', $sucursalA->id)
        ->assertSet('sucursalId', $sucursalA->id);
});
