<?php

namespace App\Http\Controllers;

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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReporteController extends Controller
{
    public function exportarPdf(Request $request): Response
    {
        $tipoReporte = $request->input('tipoReporte', 'stock_sucursal');
        $sucursalId = $request->input('sucursalId');
        $usuarioId = $request->input('usuarioId');
        $categoriaId = $request->input('categoriaId');
        $marcaId = $request->input('marcaId');
        $fechaInicio = $request->input('fechaInicio', date('Y-m-01'));
        $fechaFin = $request->input('fechaFin', date('Y-m-d'));

        $sucursal = $sucursalId ? Sucursal::find($sucursalId) : null;
        $usuario = $usuarioId ? User::find($usuarioId) : null;
        $categoria = $categoriaId ? Categoria::find($categoriaId) : null;
        $marca = $marcaId ? Marca::find($marcaId) : null;

        $meta = [
            'tipoReporte' => $tipoReporte,
            'sucursalNombre' => $sucursal?->nombre ?? 'Todas las Sucursales',
            'usuarioNombre' => $usuario?->nombre_completo ?? ($usuario?->name ?? 'Todos los Usuarios'),
            'categoriaNombre' => $categoria?->nombre ?? 'Todas las Categorías',
            'marcaNombre' => $marca?->nombre ?? 'Todas las Marcas',
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
        ];

        $reporteData = [];
        $metrics = [];

        switch ($tipoReporte) {
            case 'stock_sucursal':
                $query = ProductoSucursal::with(['producto.categoria', 'producto.marca', 'sucursal']);
                if ($sucursalId) {
                    $query->where('sucursal_id', $sucursalId);
                }
                if ($categoriaId) {
                    $query->whereHas('producto', fn ($q) => $q->where('categoria_id', $categoriaId));
                }
                if ($marcaId) {
                    $query->whereHas('producto', fn ($q) => $q->where('marca_id', $marcaId));
                }

                $items = $query->get();

                foreach ($items as $item) {
                    $stockActual = (float) $item->stock_actual;

                    $inflowsAfterStart = (float) MovimientoInventario::where('producto_id', $item->producto_id)
                        ->where('sucursal_id', $item->sucursal_id)
                        ->whereDate('fecha_movimiento', '>=', $fechaInicio)
                        ->whereIn('tipo_movimiento', ['INGRESO_COMPRA', 'INGRESO_STOCK', 'AJUSTE_ENTRADA', 'DEVOLUCION_VENTA'])
                        ->sum('cantidad');

                    $outflowsAfterStart = (float) MovimientoInventario::where('producto_id', $item->producto_id)
                        ->where('sucursal_id', $item->sucursal_id)
                        ->whereDate('fecha_movimiento', '>=', $fechaInicio)
                        ->whereIn('tipo_movimiento', ['VENTA', 'SALIDA_PERDIDA', 'SALIDA_VENCIDO', 'SALIDA_DANADO', 'AJUSTE_SALIDA'])
                        ->sum('cantidad');

                    $netChange = $inflowsAfterStart - $outflowsAfterStart;
                    $item->stock_inicial = max(0, $stockActual - $netChange);
                }

                $meta['titulo'] = 'REPORTE DE STOCK DE PRODUCTOS POR SUCURSAL';
                $meta['subtitulo'] = "Stock Inicial al {$fechaInicio} y Stock Actual al {$fechaFin}";

                $metrics = [
                    'Total Ítems' => $items->count(),
                    'Stock Inicial Total' => number_format($items->sum('stock_inicial'), 0),
                    'Stock Actual Total' => number_format($items->sum('stock_actual'), 0),
                    'Valor al Costo (Bs)' => 'Bs '.number_format($items->sum(fn ($i) => (float) $i->stock_actual * (float) ($i->producto->precio_compra ?? 0)), 2),
                ];
                $reporteData = $items;
                break;

            case 'ventas_sucursal':
                $query = Venta::with(['sucursal', 'usuario', 'cliente', 'detalles']);
                if ($sucursalId) {
                    $query->where('sucursal_id', $sucursalId);
                }
                if ($usuarioId) {
                    $query->where('usuario_id', $usuarioId);
                }
                if ($fechaInicio) {
                    $query->whereDate('fecha_venta', '>=', $fechaInicio);
                }
                if ($fechaFin) {
                    $query->whereDate('fecha_venta', '<=', $fechaFin);
                }

                $ventas = $query->latest('fecha_venta')->get();
                $meta['titulo'] = 'REPORTE DE VENTAS POR SUCURSAL Y USUARIO';
                $meta['subtitulo'] = "Rango de fechas: {$fechaInicio} al {$fechaFin}";

                $totalVentasMonto = $ventas->where('estado', 'COMPLETADA')->sum('total');

                $metrics = [
                    'Comprobantes Emitidos' => $ventas->count(),
                    'Ventas Completadas' => $ventas->where('estado', 'COMPLETADA')->count(),
                    'Ventas Anuladas' => $ventas->where('estado', 'ANULADA')->count(),
                    'Monto Total Recaudado (Bs)' => 'Bs '.number_format($totalVentasMonto, 2),
                ];
                $reporteData = $ventas;
                break;

            case 'ingresos_egresos':
                $query = Pago::with(['caja.sucursal', 'usuario', 'venta']);
                if ($usuarioId) {
                    $query->where('usuario_creador_id', $usuarioId);
                } elseif ($sucursalId) {
                    $query->whereHas('caja', fn ($q) => $q->where('sucursal_id', $sucursalId));
                }
                if ($fechaInicio) {
                    $query->whereDate('fecha_pago', '>=', $fechaInicio);
                }
                if ($fechaFin) {
                    $query->whereDate('fecha_pago', '<=', $fechaFin);
                }

                $pagos = $query->latest('fecha_pago')->get();
                $meta['titulo'] = 'REPORTE DE INGRESOS Y EGRESOS / GASTOS DE CAJA';
                $meta['subtitulo'] = "Movimientos de caja registrados del {$fechaInicio} al {$fechaFin}";

                $totalVentasMonto = $pagos->whereNotNull('venta_id')->sum('monto');
                $totalExtras = $pagos->whereNull('venta_id')->where('monto', '>', 0)->sum('monto');
                $totalEgresos = abs($pagos->whereNull('venta_id')->where('monto', '<', 0)->sum('monto'));
                $balanceNeto = ($totalVentasMonto + $totalExtras) - $totalEgresos;

                $metrics = [
                    'Ingresos por Ventas' => 'Bs '.number_format($totalVentasMonto, 2),
                    'Ingresos Extras' => 'Bs '.number_format($totalExtras, 2),
                    'Egresos / Gastos' => 'Bs '.number_format($totalEgresos, 2),
                    'Balance Neto' => 'Bs '.number_format($balanceNeto, 2),
                ];
                $reporteData = $pagos;
                break;

            case 'utilidades':
                $query = Venta::with(['detalles.producto', 'sucursal', 'movimientos'])
                    ->where('estado', 'COMPLETADA');

                if ($sucursalId) {
                    $query->where('sucursal_id', $sucursalId);
                }
                if ($fechaInicio) {
                    $query->whereDate('fecha_venta', '>=', $fechaInicio);
                }
                if ($fechaFin) {
                    $query->whereDate('fecha_venta', '<=', $fechaFin);
                }

                $ventasComp = $query->get();
                $meta['titulo'] = 'REPORTE DE UTILIDADES Y GANANCIAS NETAS';
                $meta['subtitulo'] = "Análisis de rentabilidad real del {$fechaInicio} al {$fechaFin}";

                $totalVentaBruta = 0.00;
                $totalCostoProductos = 0.00;

                foreach ($ventasComp as $v) {
                    $totalVentaBruta += (float) $v->subtotal;
                    foreach ($v->detalles as $det) {
                        $mov = $v->movimientos->firstWhere('producto_id', $det->producto_id);
                        $costoUnitarioHistorico = $mov ? (float) $mov->precio_compra : (float) ($det->producto->precio_compra ?? 0);

                        $totalCostoProductos += ($costoUnitarioHistorico * (float) $det->cantidad);
                    }
                }

                $ingresoNetoVentas = $ventasComp->sum('total');
                $utilidadNeta = $ingresoNetoVentas - $totalCostoProductos;

                $metrics = [
                    'Ventas Completadas' => $ventasComp->count(),
                    'Ingreso Neto Ventas' => 'Bs '.number_format($ingresoNetoVentas, 2),
                    'Costo Histórico (COGS)' => 'Bs '.number_format($totalCostoProductos, 2),
                    'UTILIDAD NETA REAL' => 'Bs '.number_format($utilidadNeta, 2),
                ];
                $reporteData = $ventasComp;
                break;

            case 'productos_top':
                $query = DetalleVenta::with('producto.categoria')
                    ->whereHas('venta', function ($q) use ($sucursalId, $fechaInicio, $fechaFin) {
                        $q->where('estado', 'COMPLETADA');
                        if ($sucursalId) {
                            $q->where('sucursal_id', $sucursalId);
                        }
                        if ($fechaInicio) {
                            $q->whereDate('fecha_venta', '>=', $fechaInicio);
                        }
                        if ($fechaFin) {
                            $q->whereDate('fecha_venta', '<=', $fechaFin);
                        }
                    });

                $ranking = $query->select('producto_id')
                    ->selectRaw('SUM(cantidad) as total_cant, SUM(subtotal) as total_monto')
                    ->groupBy('producto_id')
                    ->orderByDesc('total_cant')
                    ->limit(30)
                    ->get();

                $meta['titulo'] = 'RANKING DE PRODUCTOS MÁS VENDIDOS';
                $meta['subtitulo'] = "Top productos por cantidad y volumen del {$fechaInicio} al {$fechaFin}";

                $metrics = [
                    'Productos Distintos' => $ranking->count(),
                    'Unidades Vendidas' => number_format($ranking->sum('total_cant'), 0),
                    'Monto Producido' => 'Bs '.number_format($ranking->sum('total_monto'), 2),
                ];
                $reporteData = $ranking;
                break;

            case 'arqueo_cajas':
                $query = Caja::with(['sucursal', 'usuario', 'pagos']);
                if ($sucursalId) {
                    $query->where('sucursal_id', $sucursalId);
                }
                if ($usuarioId) {
                    $query->where('usuario_id', $usuarioId);
                }
                if ($fechaInicio) {
                    $query->whereDate('fecha_apertura', '>=', $fechaInicio);
                }
                if ($fechaFin) {
                    $query->whereDate('fecha_apertura', '<=', $fechaFin);
                }

                $cajas = $query->latest('fecha_apertura')->get();
                $meta['titulo'] = 'REPORTE DE ARQUEO Y CIERRE DE CAJAS';
                $meta['subtitulo'] = "Sesiones de caja aperturadas del {$fechaInicio} al {$fechaFin}";

                $metrics = [
                    'Sesiones Registradas' => $cajas->count(),
                    'Cajas Abiertas' => $cajas->where('estado', 'ABIERTA')->count(),
                    'Cajas Cerradas' => $cajas->where('estado', 'CERRADA')->count(),
                    'Monto Aperturas Total' => 'Bs '.number_format($cajas->sum('monto_apertura'), 2),
                ];
                $reporteData = $cajas;
                break;
        }

        $pdf = Pdf::loadView('pdf.reporte_general', [
            'meta' => $meta,
            'metrics' => $metrics,
            'reporteData' => $reporteData,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("Reporte_{$tipoReporte}_".date('Ymd_His').'.pdf');
    }
}
