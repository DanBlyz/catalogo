<?php

use App\Livewire\Catalogo\Marcas;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\User;
use Livewire\Livewire;

test('marcas component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('catalogo.marcas'))
        ->assertSuccessful();
});

test('user can create a new marca', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Marcas::class)
        ->call('openModal')
        ->set('nombre', 'Sony')
        ->set('descripcion', 'Fabricante de electrónica y tecnología')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('marcas', [
        'nombre' => 'Sony',
    ]);
});

test('user can update an existing marca', function () {
    $user = User::factory()->create();
    $marca = Marca::create([
        'nombre' => 'Samsun',
        'descripcion' => 'Electrónica',
    ]);

    Livewire::actingAs($user)
        ->test(Marcas::class)
        ->call('edit', $marca->id)
        ->set('nombre', 'Samsung')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('marcas', [
        'id' => $marca->id,
        'nombre' => 'Samsung',
    ]);
});

test('user can soft delete a marca without products', function () {
    $user = User::factory()->create();
    $marca = Marca::create([
        'nombre' => 'Marca Válida',
        'descripcion' => 'Sin productos',
    ]);

    Livewire::actingAs($user)
        ->test(Marcas::class)
        ->call('delete', $marca->id)
        ->assertDispatched('swal:modal');

    $this->assertSoftDeleted('marcas', [
        'id' => $marca->id,
    ]);
});

test('user cannot delete a marca with associated products', function () {
    $user = User::factory()->create();
    $marca = Marca::create([
        'nombre' => 'Nike',
        'descripcion' => 'Calzado e indumentaria',
    ]);

    Producto::create([
        'nombre' => 'Zapatillas Nike Air',
        'sku' => 'NIKE-001',
        'marca_id' => $marca->id,
    ]);

    Livewire::actingAs($user)
        ->test(Marcas::class)
        ->call('delete', $marca->id)
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('marcas', [
        'id' => $marca->id,
        'deleted_at' => null,
    ]);
});
