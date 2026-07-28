<?php

namespace App\Livewire;

use App\Models\Caja;
use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\ProductoSucursal;
use App\Models\Sucursal;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int $sucursalId = null;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user->esAdmin()) {
            $this->sucursalId = $user->sucursal_id;
        }
    }

    public function render()
    {
        $user = Auth::user();
        $sucursalFiltro = $user->esAdmin() ? $this->sucursalId : $user->sucursal_id;

        // 1. KPI Metrics
        // Ventas de Hoy
        $queryVentasHoy = Venta::whereDate('fecha_venta', Carbon::today())
            ->where('estado', 'COMPLETADA');
        if ($sucursalFiltro) {
            $queryVentasHoy->where('sucursal_id', $sucursalFiltro);
        }
        $ventasHoyMonto = (float) $queryVentasHoy->sum('total');
        $ventasHoyCantidad = $queryVentasHoy->count();

        // Ingresos Efectivo vs Digital Hoy
        $queryPagosHoy = Pago::whereDate('fecha_pago', Carbon::today())
            ->whereNotNull('venta_id');
        if ($sucursalFiltro) {
            $queryPagosHoy->whereHas('caja', fn ($q) => $q->where('sucursal_id', $sucursalFiltro));
        }
        $pagosHoy = $queryPagosHoy->get();
        $ingresosEfectivoHoy = (float) $pagosHoy->where('metodo_pago', 'EFECTIVO')->sum('monto');
        $ingresosDigitalHoy = (float) $pagosHoy->whereIn('metodo_pago', ['QR', 'TRANSFERENCIA'])->sum('monto');

        // Stock Crítico / Agotado
        $queryStockBajo = ProductoSucursal::with(['producto.categoria', 'sucursal'])
            ->where('stock_actual', '<=', 5);
        if ($sucursalFiltro) {
            $queryStockBajo->where('sucursal_id', $sucursalFiltro);
        }
        $productosStockBajo = $queryStockBajo->orderBy('stock_actual')->take(6)->get();
        $totalStockCriticoCount = $queryStockBajo->count();

        // Caja Activa del Usuario
        $cajaActiva = Caja::where('usuario_id', $user->id)
            ->where('estado', 'ABIERTA')
            ->latest('fecha_apertura')
            ->first();

        // 2. Gráfico de Ventas (Últimos 7 Días)
        $ventasUltimosDias = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $qVentaDia = Venta::whereDate('fecha_venta', $fecha)
                ->where('estado', 'COMPLETADA');
            if ($sucursalFiltro) {
                $qVentaDia->where('sucursal_id', $sucursalFiltro);
            }

            $ventasUltimosDias[] = [
                'dia' => $fecha->translatedFormat('D d/M'),
                'monto' => (float) $qVentaDia->sum('total'),
                'count' => $qVentaDia->count(),
            ];
        }

        // Max monto para calcular porcentaje de barra en CSS
        $maxMontoGrafica = max(array_column($ventasUltimosDias, 'monto'));
        if ($maxMontoGrafica <= 0) {
            $maxMontoGrafica = 1;
        }

        // 3. Top 5 Productos Más Vendidos del Mes
        $inicioMes = Carbon::now()->startOfMonth();
        $qTop = DetalleVenta::with('producto.categoria')
            ->whereHas('venta', function ($q) use ($sucursalFiltro, $inicioMes) {
                $q->where('estado', 'COMPLETADA')
                    ->whereDate('fecha_venta', '>=', $inicioMes);
                if ($sucursalFiltro) {
                    $q->where('sucursal_id', $sucursalFiltro);
                }
            });

        $topProductos = $qTop->select('producto_id')
            ->selectRaw('SUM(cantidad) as total_cant, SUM(subtotal) as total_monto')
            ->groupBy('producto_id')
            ->orderByDesc('total_cant')
            ->limit(5)
            ->get();

        $maxCantTop = $topProductos->max('total_cant') ?? 1;

        // 4. Últimas 5 Ventas Recientes
        $qVentasRecientes = Venta::with(['cliente', 'usuario', 'sucursal'])
            ->latest('fecha_venta');
        if ($sucursalFiltro) {
            $qVentasRecientes->where('sucursal_id', $sucursalFiltro);
        }
        $ventasRecientes = $qVentasRecientes->take(5)->get();

        // 5. Lista de Sucursales para el filtro de Admin
        $todasSucursales = $user->esAdmin() ? Sucursal::orderBy('nombre')->get() : [];

        return view('livewire.dashboard', [
            'ventasHoyMonto' => $ventasHoyMonto,
            'ventasHoyCantidad' => $ventasHoyCantidad,
            'ingresosEfectivoHoy' => $ingresosEfectivoHoy,
            'ingresosDigitalHoy' => $ingresosDigitalHoy,
            'productosStockBajo' => $productosStockBajo,
            'totalStockCriticoCount' => $totalStockCriticoCount,
            'cajaActiva' => $cajaActiva,
            'ventasUltimosDias' => $ventasUltimosDias,
            'maxMontoGrafica' => $maxMontoGrafica,
            'topProductos' => $topProductos,
            'maxCantTop' => $maxCantTop,
            'ventasRecientes' => $ventasRecientes,
            'todasSucursales' => $todasSucursales,
        ]);
    }
}
