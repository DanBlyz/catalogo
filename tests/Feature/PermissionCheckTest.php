<?php

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;

test('admin user has full access to all protected routes', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('administracion.roles'))
        ->assertSuccessful();

    $this->actingAs($admin)
        ->get(route('administracion.usuarios'))
        ->assertSuccessful();
});

test('regular user without permissions is denied access with 403 forbidden', function () {
    Rol::firstOrCreate(['id' => 1], ['nombre' => 'Administrador', 'codigo' => 'ADMIN']);
    $rolVendedor = Rol::create(['nombre' => 'Vendedor', 'codigo' => 'VENDEDOR']);

    $user = User::factory()->create(['rol_id' => $rolVendedor->id]);

    $this->actingAs($user)
        ->get(route('administracion.roles'))
        ->assertStatus(403);
});

test('regular user with specific assigned permission is granted access', function () {
    Rol::firstOrCreate(['id' => 1], ['nombre' => 'Administrador', 'codigo' => 'ADMIN']);
    $rolVendedor = Rol::create(['nombre' => 'Vendedor', 'codigo' => 'VENDEDOR']);

    $user = User::factory()->create(['rol_id' => $rolVendedor->id]);

    $permiso = Permiso::firstOrCreate(['codigo' => 'roles.gestionar'], [
        'nombre' => 'Gestionar Roles',
        'modulo' => 'ADMINISTRACION',
    ]);

    $user->permisos()->attach($permiso->id);

    $this->actingAs($user)
        ->get(route('administracion.roles'))
        ->assertSuccessful();
});
