<?php

use App\Livewire\Administracion\Roles;
use App\Models\Rol;
use App\Models\User;
use Database\Seeders\RolSeeder;
use Livewire\Livewire;

test('roles component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('administracion.roles'))
        ->assertSuccessful();
});

test('user can create a new role', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Roles::class)
        ->call('openModal')
        ->set('nombre', 'Supervisor de Tienda')
        ->set('codigo', 'SUPERVISOR')
        ->set('descripcion', 'Supervisa ventas y stock')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('roles', [
        'nombre' => 'Supervisor de Tienda',
        'codigo' => 'SUPERVISOR',
    ]);
});

test('user can update an existing role', function () {
    $user = User::factory()->create();
    $rol = Rol::create([
        'nombre' => 'Vendedor Junior',
        'codigo' => 'VENDEDOR_JR',
        'descripcion' => 'Vendedor de prueba',
    ]);

    Livewire::actingAs($user)
        ->test(Roles::class)
        ->call('edit', $rol->id)
        ->set('nombre', 'Vendedor Senior')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('roles', [
        'id' => $rol->id,
        'nombre' => 'Vendedor Senior',
    ]);
});

test('user can soft delete a role', function () {
    $this->seed(RolSeeder::class);
    $user = User::factory()->create();
    $rol = Rol::create([
        'nombre' => 'Rol Temporal',
        'codigo' => 'TEMPORAL',
        'descripcion' => 'A eliminar',
    ]);

    Livewire::actingAs($user)
        ->test(Roles::class)
        ->call('delete', $rol->id)
        ->assertDispatched('swal:modal');

    $this->assertSoftDeleted('roles', [
        'id' => $rol->id,
    ]);
});
