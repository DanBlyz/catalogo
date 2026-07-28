<?php

use App\Livewire\Administracion\Reportes;
use App\Models\Sucursal;
use App\Models\User;
use Livewire\Livewire;

test('reportes component can render', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('administracion.reportes'))
        ->assertSuccessful();
});

test('user can switch report types and render preview', function () {
    $sucursal = Sucursal::create(['nombre' => 'Sucursal Reportes']);
    $user = User::factory()->create(['sucursal_id' => $sucursal->id]);

    Livewire::actingAs($user)
        ->test(Reportes::class)
        ->set('tipoReporte', 'stock_sucursal')
        ->assertSee('Stock por Sucursal')
        ->set('tipoReporte', 'ventas_sucursal')
        ->assertSee('Ventas por Sucursal')
        ->set('tipoReporte', 'ingresos_egresos')
        ->assertSee('Ingresos y Egresos')
        ->set('tipoReporte', 'utilidades')
        ->assertSee('Utilidades y Margen')
        ->set('tipoReporte', 'productos_top')
        ->assertSee('Ranking de Productos')
        ->set('tipoReporte', 'arqueo_cajas')
        ->assertSee('Arqueo de Cajas');
});

test('user can generate pdf stream for stock by branch report', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('administracion.reportes.pdf', ['tipoReporte' => 'stock_sucursal']))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

test('user can generate pdf stream for utilities net profit report', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('administracion.reportes.pdf', ['tipoReporte' => 'utilidades']))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});
