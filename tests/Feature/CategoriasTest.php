<?php

use App\Livewire\Catalogo\Categorias;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Livewire\Livewire;

test('categorias component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('catalogo.categorias'))
        ->assertSuccessful();
});

test('user can create a new categoria', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Categorias::class)
        ->call('openModal')
        ->set('nombre', 'Bebidas')
        ->set('descripcion', 'Jugos, refrescos y aguas')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('categorias', [
        'nombre' => 'Bebidas',
    ]);
});

test('user can update an existing categoria', function () {
    $user = User::factory()->create();
    $categoria = Categoria::create([
        'nombre' => 'Lácteos',
        'descripcion' => 'Leches y yogures',
    ]);

    Livewire::actingAs($user)
        ->test(Categorias::class)
        ->call('edit', $categoria->id)
        ->set('nombre', 'Lácteos y Derivados')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('categorias', [
        'id' => $categoria->id,
        'nombre' => 'Lácteos y Derivados',
    ]);
});

test('user can soft delete a categoria without products', function () {
    $user = User::factory()->create();
    $categoria = Categoria::create([
        'nombre' => 'Categoría Válida',
        'descripcion' => 'Sin productos',
    ]);

    Livewire::actingAs($user)
        ->test(Categorias::class)
        ->call('delete', $categoria->id)
        ->assertDispatched('swal:modal');

    $this->assertSoftDeleted('categorias', [
        'id' => $categoria->id,
    ]);
});

test('user cannot delete a categoria with associated products', function () {
    $user = User::factory()->create();
    $categoria = Categoria::create([
        'nombre' => 'Snacks',
        'descripcion' => 'Papas y galletas',
    ]);

    Producto::create([
        'nombre' => 'Papas Fritas',
        'sku' => 'SNACK-001',
        'categoria_id' => $categoria->id,
    ]);

    Livewire::actingAs($user)
        ->test(Categorias::class)
        ->call('delete', $categoria->id)
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('categorias', [
        'id' => $categoria->id,
        'deleted_at' => null,
    ]);
});
