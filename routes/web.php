<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VentaController;
use App\Livewire\Administracion\Permisos;
use App\Livewire\Administracion\Reportes;
use App\Livewire\Administracion\Roles;
use App\Livewire\Administracion\Sucursales;
use App\Livewire\Administracion\Usuarios;
use App\Livewire\Catalogo\Categorias;
use App\Livewire\Catalogo\Marcas;
use App\Livewire\Catalogo\Productos;
use App\Livewire\Dashboard;
use App\Livewire\Operacion\Cajas;
use App\Livewire\Operacion\Movimientos;
use App\Livewire\Operacion\Pagos;
use App\Livewire\Operacion\Ventas;
use App\Livewire\Operacion\VentasDia;
use App\Livewire\Persona\Clientes;
use App\Livewire\Persona\Proveedores;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', Dashboard::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('administracion')->group(function () {
        Route::get('/roles', Roles::class)->middleware('permission:roles.gestionar')->name('administracion.roles');
        Route::get('/sucursales', Sucursales::class)->middleware('permission:sucursales.gestionar')->name('administracion.sucursales');
        Route::get('/permisos', Permisos::class)->middleware('permission:permisos.gestionar')->name('administracion.permisos');
        Route::get('/usuarios', Usuarios::class)->middleware('permission:usuarios.gestionar')->name('administracion.usuarios');
        Route::get('/reportes', Reportes::class)->middleware('permission:reportes.ver')->name('administracion.reportes');
        Route::get('/reportes/pdf', [ReporteController::class, 'exportarPdf'])->middleware('permission:reportes.exportar')->name('administracion.reportes.pdf');
    });

    Route::prefix('catalogo')->group(function () {
        Route::get('/categorias', Categorias::class)->middleware('permission:categorias.gestionar')->name('catalogo.categorias');
        Route::get('/marcas', Marcas::class)->middleware('permission:marcas.gestionar')->name('catalogo.marcas');
        Route::get('/productos', Productos::class)->middleware('permission:productos.ver')->name('catalogo.productos');
    });

    Route::prefix('persona')->group(function () {
        Route::get('/clientes', Clientes::class)->middleware('permission:clientes.gestionar')->name('persona.clientes');
        Route::get('/proveedores', Proveedores::class)->middleware('permission:proveedores.gestionar')->name('persona.proveedores');
    });

    Route::prefix('operacion')->group(function () {
        Route::get('/cajas', Cajas::class)->middleware('permission:cajas.ver')->name('operacion.cajas');
        Route::get('/movimientos', Movimientos::class)->middleware('permission:movimientos.ver')->name('operacion.movimientos');
        Route::get('/ventas', Ventas::class)->middleware('permission:ventas.crear')->name('operacion.ventas');
        Route::get('/ventas-dia', VentasDia::class)->middleware('permission:ventas.ver')->name('operacion.ventas-dia');
        Route::get('/pagos', Pagos::class)->middleware('permission:pagos.ver')->name('operacion.pagos');
        Route::get('/ventas/{venta}/recibo', [VentaController::class, 'generarRecibo'])->middleware('permission:ventas.ver')->name('operacion.ventas.recibo');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
