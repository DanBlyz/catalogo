<?php

namespace App\Livewire\Common;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ProductoSucursal;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HeaderTopbar extends Component
{
    public string $searchQuery = '';

    public bool $showSearchDropdown = false;

    public array $searchResultsProductos = [];

    public array $searchResultsClientes = [];

    public array $searchResultsVentas = [];

    public function updatedSearchQuery(): void
    {
        $term = trim($this->searchQuery);

        if (strlen($term) < 2) {
            $this->resetSearch();

            return;
        }

        $user = Auth::user();
        $sucursalId = $user->sucursal_id;

        // 1. Search Products
        $productos = Producto::with(['categoria', 'productoSucursales' => function ($q) use ($sucursalId) {
            if ($sucursalId && ! Auth::user()->esAdmin()) {
                $q->where('sucursal_id', $sucursalId);
            }
        }])
            ->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
                    ->orWhere('codigo_barras', 'like', "%{$term}%");
            })
            ->take(5)
            ->get();

        $this->searchResultsProductos = $productos->map(function ($p) use ($user) {
            $ps = $user->sucursal_id
                ? $p->productoSucursales->firstWhere('sucursal_id', $user->sucursal_id)
                : $p->productoSucursales->first();

            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'sku' => $p->sku,
                'categoria' => $p->categoria?->nombre ?? 'Sin Cat.',
                'precio_venta' => (float) $p->precio_venta,
                'stock' => (float) ($ps?->stock_actual ?? 0),
            ];
        })->toArray();

        // 2. Search Clients
        $this->searchResultsClientes = Cliente::where('nombre_razon_social', 'like', "%{$term}%")
            ->orWhere('cedula_nit_ruc', 'like', "%{$term}%")
            ->orWhere('telefono', 'like', "%{$term}%")
            ->take(4)
            ->get(['id', 'nombre_razon_social', 'cedula_nit_ruc', 'telefono'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'nombre_razon_social' => $c->nombre_razon_social,
                'ci_nit_documento' => $c->cedula_nit_ruc,
                'telefono' => $c->telefono,
            ])
            ->toArray();

        // 3. Search Sales Receipts
        $queryVentas = Venta::with(['cliente'])
            ->where('numero_recibo', 'like', "%{$term}%");
        if ($sucursalId && ! Auth::user()->esAdmin()) {
            $queryVentas->where('sucursal_id', $sucursalId);
        }
        $this->searchResultsVentas = $queryVentas->take(4)
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'numero_recibo' => $v->numero_recibo,
                'cliente' => $v->cliente?->nombre_razon_social ?? 'Cliente S/N',
                'total' => (float) $v->total,
                'estado' => $v->estado,
                'fecha' => $v->fecha_venta?->format('d/m/Y H:i'),
            ])->toArray();

        $this->showSearchDropdown = true;
    }

    public function resetSearch(): void
    {
        $this->searchQuery = '';
        $this->showSearchDropdown = false;
        $this->searchResultsProductos = [];
        $this->searchResultsClientes = [];
        $this->searchResultsVentas = [];
    }

    public function render()
    {
        $user = Auth::user();
        $sucursalId = $user->sucursal_id;

        // Dynamic System Notifications List
        $notificaciones = [];

        // 1. Stock Crítico Notification
        $qStockCritico = ProductoSucursal::with(['producto', 'sucursal'])
            ->where('stock_actual', '<=', 5);
        if ($sucursalId && ! $user->esAdmin()) {
            $qStockCritico->where('sucursal_id', $sucursalId);
        }
        $stockCriticoCount = $qStockCritico->count();

        if ($stockCriticoCount > 0) {
            $notificaciones[] = [
                'id' => 'notif_stock_critico',
                'tipo' => 'danger',
                'icono' => 'fa-solid fa-boxes-stacked',
                'titulo' => 'Alerta de Stock Crítico',
                'mensaje' => "Hay {$stockCriticoCount} productos con 5 o menos unidades en inventario.",
                'link' => route('operacion.movimientos'),
                'fecha' => 'Hace un momento',
            ];
        }

        // 2. Active Cash Register Notification
        $cajaAbierta = Caja::where('usuario_id', $user->id)
            ->where('estado', 'ABIERTA')
            ->exists();

        if (! $cajaAbierta) {
            $notificaciones[] = [
                'id' => 'notif_caja_cerrada',
                'tipo' => 'warning',
                'icono' => 'fa-solid fa-cash-register',
                'titulo' => 'Caja Cerrada',
                'mensaje' => 'Aún no has aperturado tu caja del día para realizar ventas.',
                'link' => route('operacion.cajas'),
                'fecha' => 'Requiere atención',
            ];
        } else {
            $notificaciones[] = [
                'id' => 'notif_caja_abierta',
                'tipo' => 'success',
                'icono' => 'fa-solid fa-circle-check',
                'titulo' => 'Caja Abierta Activa',
                'mensaje' => 'Tu sesión de caja está activa para emitir ventas y pagos.',
                'link' => route('operacion.cajas'),
                'fecha' => 'Sesión activa',
            ];
        }

        // 3. Today's Recent Sales Summary Notification
        $qVentasHoy = Venta::whereDate('fecha_venta', now())->where('estado', 'COMPLETADA');
        if ($sucursalId && ! $user->esAdmin()) {
            $qVentasHoy->where('sucursal_id', $sucursalId);
        }
        $ventasHoyCant = $qVentasHoy->count();
        $ventasHoyTotal = $qVentasHoy->sum('total');

        if ($ventasHoyCant > 0) {
            $notificaciones[] = [
                'id' => 'notif_ventas_hoy',
                'tipo' => 'info',
                'icono' => 'fa-solid fa-receipt',
                'titulo' => 'Resumen de Ventas Hoy',
                'mensaje' => "Se han registrado {$ventasHoyCant} ventas acumulando Bs ".number_format($ventasHoyTotal, 2).'.',
                'link' => route('operacion.ventas-dia'),
                'fecha' => 'Hoy',
            ];
        }

        $unreadCount = count($notificaciones);

        return view('livewire.common.header-topbar', [
            'notificaciones' => $notificaciones,
            'unreadCount' => $unreadCount,
            'user' => $user,
        ]);
    }
}
