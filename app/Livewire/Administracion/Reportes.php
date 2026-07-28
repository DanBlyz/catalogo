<?php

namespace App\Livewire\Administracion;

use App\Models\Caja;
use App\Models\Categoria;
use App\Models\DetalleVenta;
use App\Models\Marca;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\ProductoSucursal;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Reportes extends Component
{
    #[Url(history: true)]
    public string $tipoReporte = 'stock_sucursal'; // stock_sucursal, ventas_sucursal, ingresos_egresos, utilidades, productos_top, arqueo_cajas

    #[Url(history: true)]
    public string $sucursalId = '';

    #[Url(history: true)]
    public string $usuarioId = '';

    #[Url(history: true)]
    public string $categoriaId = '';

    #[Url(history: true)]
    public string $marcaId = '';

    #[Url(history: true)]
    public string $fechaInicio = '';

    #[Url(history: true)]
    public string $fechaFin = '';

    public function mount(): void
    {
        $user = Auth::user();

        if (empty($this->fechaInicio)) {
            $this->fechaInicio = date('Y-m-01'); // First day of current month
        }
        if (empty($this->fechaFin)) {
            $this->fechaFin = date('Y-m-d');
        }

        if (! $user->esAdmin() && ! empty($user->sucursal_id)) {
            $this->sucursalId = (string) $user->sucursal_id;
            $this->usuarioId = (string) $user->id;
        }
    }

    public function resetFilters(): void
    {
        $user = Auth::user();
        $this->fechaInicio = date('Y-m-01');
        $this->fechaFin = date('Y-m-d');
        $this->categoriaId = '';
        $this->marcaId = '';

        if ($user->esAdmin()) {
            $this->sucursalId = '';
            $this->usuarioId = '';
        } else {
            $this->sucursalId = (string) ($user->sucursal_id ?? '');
            $this->usuarioId = (string) $user->id;
        }
    }

    public function exportPdf(): void
    {
        $params = array_filter([
            'tipoReporte' => $this->tipoReporte,
            'sucursalId' => $this->sucursalId,
            'usuarioId' => $this->usuarioId,
            'categoriaId' => $this->categoriaId,
            'marcaId' => $this->marcaId,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin,
        ]);

        $url = route('administracion.reportes.pdf', $params);

        $this->dispatch('open-pdf-window', ['url' => $url]);
    }

    public function render()
    {
        $user = Auth::user();

        // General Collections for Dropdowns
        $todasSucursales = $user->esAdmin() ? Sucursal::orderBy('nombre')->get() : Sucursal::where('id', $user->sucursal_id)->get();
        $todosUsuarios = $user->esAdmin() ? User::orderBy('name')->get() : User::where('id', $user->id)->get();
        $todasCategorias = Categoria::orderBy('nombre')->get();
        $todasMarcas = Marca::orderBy('nombre')->get();

        // Data containers for Live Preview
        $reporteData = [];
        $resumenMetrics = [];

        switch ($this->tipoReporte) {
            case 'stock_sucursal':
                $query = ProductoSucursal::with(['producto.categoria', 'producto.marca', 'sucursal']);

                if ($this->sucursalId) {
                    $query->where('sucursal_id', $this->sucursalId);
                }
                if ($this->categoriaId) {
                    $query->whereHas('producto', fn ($q) => $q->where('categoria_id', $this->categoriaId));
                }
                if ($this->marcaId) {
                    $query->whereHas('producto', fn ($q) => $q->where('marca_id', $this->marcaId));
                }

                $items = $query->get();

                // Compute Stock Inicial for each product based on fechaInicio
                foreach ($items as $item) {
                    $stockActual = (float) $item->stock_actual;

                    $inflowsAfterStart = (float) MovimientoInventario::where('producto_id', $item->producto_id)
                        ->where('sucursal_id', $item->sucursal_id)
                        ->whereDate('fecha_movimiento', '>=', $this->fechaInicio)
                        ->whereIn('tipo_movimiento', ['INGRESO_COMPRA', 'INGRESO_STOCK', 'AJUSTE_ENTRADA', 'DEVOLUCION_VENTA'])
                        ->sum('cantidad');

                    $outflowsAfterStart = (float) MovimientoInventario::where('producto_id', $item->producto_id)
                        ->where('sucursal_id', $item->sucursal_id)
                        ->whereDate('fecha_movimiento', '>=', $this->fechaInicio)
                        ->whereIn('tipo_movimiento', ['VENTA', 'SALIDA_PERDIDA', 'SALIDA_VENCIDO', 'SALIDA_DANADO', 'AJUSTE_SALIDA'])
                        ->sum('cantidad');

                    $netChange = $inflowsAfterStart - $outflowsAfterStart;
                    $item->stock_inicial = max(0, $stockActual - $netChange);
                }

                $totalStockInicial = $items->sum('stock_inicial');
                $totalStockActual = $items->sum('stock_actual');
                $valorCompraTotal = $items->sum(fn ($i) => (float) $i->stock_actual * (float) ($i->producto->precio_compra ?? 0));
                $valorVentaTotal = $items->sum(fn ($i) => (float) $i->stock_actual * (float) ($i->producto->precio_venta ?? 0));

                $resumenMetrics = [
                    'Total Ítems' => $items->count(),
                    'Stock Inicial (Fecha Inicio)' => number_format($totalStockInicial, 0),
                    'Stock Actual (Fecha Fin)' => number_format($totalStockActual, 0),
                    'Valor al Costo (Bs)' => 'Bs '.number_format($valorCompraTotal, 2),
                    'Valor a la Venta (Bs)' => 'Bs '.number_format($valorVentaTotal, 2),
                ];
                $reporteData = $items;
                break;

            case 'ventas_sucursal':
                $query = Venta::with(['sucursal', 'usuario', 'cliente', 'detalles']);

                if ($this->sucursalId) {
                    $query->where('sucursal_id', $this->sucursalId);
                }
                if ($this->usuarioId) {
                    $query->where('usuario_id', $this->usuarioId);
                }
                if ($this->fechaInicio) {
                    $query->whereDate('fecha_venta', '>=', $this->fechaInicio);
                }
                if ($this->fechaFin) {
                    $query->whereDate('fecha_venta', '<=', $this->fechaFin);
                }

                $ventas = $query->latest('fecha_venta')->get();
                $totalVentas = $ventas->where('estado', 'COMPLETADA')->sum('total');
                $totalDescuentos = $ventas->where('estado', 'COMPLETADA')->sum('descuento_general');

                $resumenMetrics = [
                    'Total Ventas Registradas' => $ventas->count(),
                    'Ventas Completadas' => $ventas->where('estado', 'COMPLETADA')->count(),
                    'Ventas Anuladas' => $ventas->where('estado', 'ANULADA')->count(),
                    'Monto Total Recaudado' => 'Bs '.number_format($totalVentas, 2),
                ];
                $reporteData = $ventas;
                break;

            case 'ingresos_egresos':
                $query = Pago::with(['caja.sucursal', 'usuario', 'venta']);

                if ($this->usuarioId) {
                    $query->where('usuario_creador_id', $this->usuarioId);
                } elseif ($this->sucursalId) {
                    $query->whereHas('caja', fn ($q) => $q->where('sucursal_id', $this->sucursalId));
                }

                if ($this->fechaInicio) {
                    $query->whereDate('fecha_pago', '>=', $this->fechaInicio);
                }
                if ($this->fechaFin) {
                    $query->whereDate('fecha_pago', '<=', $this->fechaFin);
                }

                $pagos = $query->latest('fecha_pago')->get();

                $totalVentasMonto = $pagos->whereNotNull('venta_id')->sum('monto');
                $totalIngresosExtras = $pagos->whereNull('venta_id')->where('monto', '>', 0)->sum('monto');
                $totalEgresos = abs($pagos->whereNull('venta_id')->where('monto', '<', 0)->sum('monto'));
                $balanceNeto = ($totalVentasMonto + $totalIngresosExtras) - $totalEgresos;

                $resumenMetrics = [
                    'Ingresos por Ventas' => 'Bs '.number_format($totalVentasMonto, 2),
                    'Ingresos Extraordinarios' => 'Bs '.number_format($totalIngresosExtras, 2),
                    'Egresos / Gastos' => 'Bs '.number_format($totalEgresos, 2),
                    'Balance Neto de Caja' => 'Bs '.number_format($balanceNeto, 2),
                ];
                $reporteData = $pagos;
                break;

            case 'utilidades':
                $query = Venta::with(['detalles.producto', 'sucursal', 'movimientos'])
                    ->where('estado', 'COMPLETADA');

                if ($this->sucursalId) {
                    $query->where('sucursal_id', $this->sucursalId);
                }
                if ($this->fechaInicio) {
                    $query->whereDate('fecha_venta', '>=', $this->fechaInicio);
                }
                if ($this->fechaFin) {
                    $query->whereDate('fecha_venta', '<=', $this->fechaFin);
                }

                $ventasComp = $query->get();

                $totalVentaBruta = 0.00;
                $totalCostoProductos = 0.00;
                $totalDescuentos = 0.00;

                foreach ($ventasComp as $v) {
                    $totalVentaBruta += (float) $v->subtotal;
                    $totalDescuentos += (float) $v->descuento_general;

                    foreach ($v->detalles as $det) {
                        // Retrieve the historical buy price recorded at the exact moment of sale
                        $mov = $v->movimientos->firstWhere('producto_id', $det->producto_id);
                        $costoUnitarioHistorico = $mov ? (float) $mov->precio_compra : (float) ($det->producto->precio_compra ?? 0);

                        $totalCostoProductos += ($costoUnitarioHistorico * (float) $det->cantidad);
                        $totalDescuentos += ((float) $det->descuento_unitario * (float) $det->cantidad);
                    }
                }

                $ingresoNetoVentas = $ventasComp->sum('total');
                $utilidadNeta = $ingresoNetoVentas - $totalCostoProductos;

                $resumenMetrics = [
                    'Ventas Completadas' => $ventasComp->count(),
                    'Ingreso Neto Ventas' => 'Bs '.number_format($ingresoNetoVentas, 2),
                    'Costo Histórico Real (COGS)' => 'Bs '.number_format($totalCostoProductos, 2),
                    'Utilidad Neta Real' => 'Bs '.number_format($utilidadNeta, 2),
                ];
                $reporteData = $ventasComp;
                break;

            case 'productos_top':
                $query = DetalleVenta::with('producto.categoria')
                    ->whereHas('venta', function ($q) {
                        $q->where('estado', 'COMPLETADA');
                        if ($this->sucursalId) {
                            $q->where('sucursal_id', $this->sucursalId);
                        }
                        if ($this->fechaInicio) {
                            $q->whereDate('fecha_venta', '>=', $this->fechaInicio);
                        }
                        if ($this->fechaFin) {
                            $q->whereDate('fecha_venta', '<=', $this->fechaFin);
                        }
                    });

                $ranking = $query->select('producto_id')
                    ->selectRaw('SUM(cantidad) as total_cant, SUM(subtotal) as total_monto')
                    ->groupBy('producto_id')
                    ->orderByDesc('total_cant')
                    ->limit(20)
                    ->get();

                $resumenMetrics = [
                    'Productos Distintos Vendidos' => $ranking->count(),
                    'Unidades Totales Vendidas' => number_format($ranking->sum('total_cant'), 0),
                    'Monto Total Producido' => 'Bs '.number_format($ranking->sum('total_monto'), 2),
                ];
                $reporteData = $ranking;
                break;

            case 'arqueo_cajas':
                $query = Caja::with(['sucursal', 'usuario', 'pagos']);

                if ($this->sucursalId) {
                    $query->where('sucursal_id', $this->sucursalId);
                }
                if ($this->usuarioId) {
                    $query->where('usuario_id', $this->usuarioId);
                }
                if ($this->fechaInicio) {
                    $query->whereDate('fecha_apertura', '>=', $this->fechaInicio);
                }
                if ($this->fechaFin) {
                    $query->whereDate('fecha_apertura', '<=', $this->fechaFin);
                }

                $cajas = $query->latest('fecha_apertura')->get();

                $resumenMetrics = [
                    'Total Cajas Aperturadas' => $cajas->count(),
                    'Cajas Abiertas Activas' => $cajas->where('estado', 'ABIERTA')->count(),
                    'Cajas Cerradas' => $cajas->where('estado', 'CERRADA')->count(),
                    'Monto Inicial Acumulado' => 'Bs '.number_format($cajas->sum('monto_apertura'), 2),
                ];
                $reporteData = $cajas;
                break;
        }

        return view('livewire.administracion.reportes', [
            'todasSucursales' => $todasSucursales,
            'todosUsuarios' => $todosUsuarios,
            'todasCategorias' => $todasCategorias,
            'todasMarcas' => $todasMarcas,
            'reporteData' => $reporteData,
            'resumenMetrics' => $resumenMetrics,
        ]);
    }
}
