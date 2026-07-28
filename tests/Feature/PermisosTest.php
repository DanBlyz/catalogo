<?php

use App\Livewire\Administracion\Permisos;
use App\Models\Permiso;
use App\Models\User;
use Database\Seeders\PermisoSeeder;
use Livewire\Livewire;

test('permisos component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('administracion.permisos'))
        ->assertSuccessful();
});

test('user can create a new permiso', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Permisos::class)
        ->call('openModal')
        ->set('nombre', 'Exportar Reportes')
        ->set('codigo', 'reportes.exportar')
        ->set('modulo', 'REPORTES')
        ->set('descripcion', 'Permite exportar reportes a PDF o Excel')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('swal:modal');

    $this->assertDatabaseHas('permisos', [
        'nombre' => 'Exportar Reportes',
        'codigo' => 'reportes.exportar',
        'modulo' => 'REPORTES',
    ]);
});

test('user can update an existing permiso', function () {
    $user = User::factory()->create();
    $permiso = Permiso::create([
        'nombre' => 'Ver Caja',
        'codigo' => 'cajas.ver_test',
        'modulo' => 'CAJAS',
        'descripcion' => 'Permiso de prueba',
    ]);

    Livewire::actingAs($user)
        ->test(Permisos::class)
        ->call('edit', $permiso->id)
        ->set('nombre', 'Ver Caja Detallada')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('permisos', [
        'id' => $permiso->id,
        'nombre' => 'Ver Caja Detallada',
    ]);
});

test('user can soft delete a permiso', function () {
    $this->seed(PermisoSeeder::class);
    $user = User::factory()->create();
    $permiso = Permiso::create([
        'nombre' => 'Permiso Temporal',
        'codigo' => 'temporal.eliminar',
        'modulo' => 'PRUEBA',
        'descripcion' => 'Permiso a eliminar',
    ]);

    Livewire::actingAs($user)
        ->test(Permisos::class)
        ->call('delete', $permiso->id)
        ->assertDispatched('swal:modal');

    $this->assertSoftDeleted('permisos', [
        'id' => $permiso->id,
    ]);
});
