<?php

use App\Livewire\Catalogo\Productos;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\User;
use Livewire\Livewire;

test('productos component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('catalogo.productos'))
        ->assertSuccessful();
});

test('user can create a new producto', function () {
    $user = User::factory()->create();
    $categoria = Categoria::create(['nombre' => 'Tecnología']);
    $marca = Marca::create(['nombre' => 'Logitech']);

    Livewire::actingAs($user)
        ->test(Productos::class)
        ->call('openModal')
        ->set('nombre', 'Mouse MX Master 3S')
        ->set('sku', 'LOGI-MX3S')
        ->set('codigo_barras', '7790001112223')
        ->set('categoria_id', $categoria->id)
        ->set('marca_id', $marca->id)
        ->set('precio_compra', 450.00)
        ->set('precio_venta', 650.00)
        ->set('stock_minimo', 3)
        ->set('unidad_medida', 'UNIDAD')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('productos', [
        'nombre' => 'Mouse MX Master 3S',
        'sku' => 'LOGI-MX3S',
        'precio_venta' => 650.00,
    ]);
});

test('user can update an existing producto', function () {
    $user = User::factory()->create();
    $producto = Producto::create([
        'nombre' => 'Monitor 24',
        'sku' => 'MON-24',
        'precio_venta' => 1200.00,
    ]);

    Livewire::actingAs($user)
        ->test(Productos::class)
        ->call('edit', $producto->id)
        ->set('nombre', 'Monitor 24 IPS FHD')
        ->set('precio_venta', 1250.00)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('productos', [
        'id' => $producto->id,
        'nombre' => 'Monitor 24 IPS FHD',
        'precio_venta' => 1250.00,
    ]);
});

test('user can filter productos by categoria and marca', function () {
    $user = User::factory()->create();
    $categoria1 = Categoria::create(['nombre' => 'Audio']);
    $categoria2 = Categoria::create(['nombre' => 'Video']);

    $prod1 = Producto::create(['nombre' => 'Audífonos Bluetooth', 'sku' => 'AUD-01', 'categoria_id' => $categoria1->id, 'precio_venta' => 100]);
    $prod2 = Producto::create(['nombre' => 'Cámara Web 4K', 'sku' => 'CAM-01', 'categoria_id' => $categoria2->id, 'precio_venta' => 200]);

    Livewire::actingAs($user)
        ->test(Productos::class)
        ->set('categoriaFilter', (string) $categoria1->id)
        ->assertSee('Audífonos Bluetooth')
        ->assertDontSee('Cámara Web 4K');
});

test('user can soft delete a producto', function () {
    $user = User::factory()->create();
    $producto = Producto::create([
        'nombre' => 'Producto Eliminable',
        'sku' => 'DEL-001',
        'precio_venta' => 50.00,
    ]);

    Livewire::actingAs($user)
        ->test(Productos::class)
        ->call('delete', $producto->id)
        ->assertDispatched('swal:modal');

    $this->assertSoftDeleted('productos', [
        'id' => $producto->id,
    ]);
});
