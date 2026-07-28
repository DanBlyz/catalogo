<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VentaController;
use App\Livewire\Administracion\Permisos;
use App\Livewire\Administracion\Roles;
use App\Livewire\Administracion\Sucursales;
use App\Livewire\Administracion\Usuarios;
use App\Livewire\Catalogo\Categorias;
use App\Livewire\Catalogo\Marcas;
use App\Livewire\Catalogo\Productos;
use App\Livewire\Operacion\Cajas;
use App\Livewire\Operacion\Movimientos;
use App\Livewire\Operacion\Ventas;
use App\Livewire\Persona\Clientes;
use App\Livewire\Persona\Proveedores;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('administracion')->group(function () {
        Route::get('/roles', Roles::class)->name('administracion.roles');
        Route::get('/sucursales', Sucursales::class)->name('administracion.sucursales');
        Route::get('/permisos', Permisos::class)->name('administracion.permisos');
        Route::get('/usuarios', Usuarios::class)->name('administracion.usuarios');
    });

    Route::prefix('catalogo')->group(function () {
        Route::get('/categorias', Categorias::class)->name('catalogo.categorias');
        Route::get('/marcas', Marcas::class)->name('catalogo.marcas');
        Route::get('/productos', Productos::class)->name('catalogo.productos');
    });

    Route::prefix('persona')->group(function () {
        Route::get('/clientes', Clientes::class)->name('persona.clientes');
        Route::get('/proveedores', Proveedores::class)->name('persona.proveedores');
    });

    Route::prefix('operacion')->group(function () {
        Route::get('/cajas', Cajas::class)->name('operacion.cajas');
        Route::get('/movimientos', Movimientos::class)->name('operacion.movimientos');
        Route::get('/ventas', Ventas::class)->name('operacion.ventas');
        Route::get('/ventas/{venta}/recibo', [VentaController::class, 'generarRecibo'])->name('operacion.ventas.recibo');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
