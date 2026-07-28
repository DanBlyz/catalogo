<?php

use App\Livewire\Administracion\Usuarios;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Livewire\Livewire;

test('usuarios component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('administracion.usuarios'))
        ->assertSuccessful();
});

test('user can create a new usuario', function () {
    $user = User::factory()->create();
    $rol = Rol::create(['nombre' => 'Cajero', 'codigo' => 'CAJERO']);
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Norte']);

    Livewire::actingAs($user)
        ->test(Usuarios::class)
        ->call('openModal')
        ->set('name', 'Pedro')
        ->set('apellido_paterno', 'Mendoza')
        ->set('apellido_materno', 'Ríos')
        ->set('cedula', '9876543 CB')
        ->set('email', 'pedro@tienda.com')
        ->set('password', 'password123')
        ->set('rol_id', $rol->id)
        ->set('sucursal_id', $sucursal->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('users', [
        'email' => 'pedro@tienda.com',
        'rol_id' => $rol->id,
        'sucursal_id' => $sucursal->id,
    ]);
});

test('user can update an existing usuario', function () {
    $user = User::factory()->create();
    $rol = Rol::create(['nombre' => 'Vendedor', 'codigo' => 'VENDEDOR']);
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Central']);

    $targetUser = User::factory()->create([
        'name' => 'Usuario Antiguo',
        'email' => 'antiguo@tienda.com',
        'rol_id' => $rol->id,
        'sucursal_id' => $sucursal->id,
    ]);

    Livewire::actingAs($user)
        ->test(Usuarios::class)
        ->call('edit', $targetUser->id)
        ->set('name', 'Usuario Actualizado')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'name' => 'Usuario Actualizado',
    ]);
});

test('user can assign special permissions to a user', function () {
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    $permiso1 = Permiso::create(['nombre' => 'Ver Reportes', 'codigo' => 'reportes.ver', 'modulo' => 'REPORTES']);
    $permiso2 = Permiso::create(['nombre' => 'Anular Ventas', 'codigo' => 'ventas.anular', 'modulo' => 'VENTAS']);

    Livewire::actingAs($user)
        ->test(Usuarios::class)
        ->call('openPermissionsModal', $targetUser->id)
        ->set('userPermisos', [$permiso1->id, $permiso2->id])
        ->call('savePermissions')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    expect($targetUser->fresh()->permisos->pluck('id')->toArray())->toContain($permiso1->id, $permiso2->id);
});

test('logged in user cannot delete themselves', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Usuarios::class)
        ->call('delete', $user->id)
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'deleted_at' => null,
    ]);
});
