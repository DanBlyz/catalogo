<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $meta['titulo'] }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-title {
            font-size: 16px;
            font-weight: bold;
            color: #0369a1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .report-sub {
            font-size: 9px;
            color: #64748b;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 8.5px;
        }
        .meta-box table {
            width: 100%;
        }
        .metrics-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .metrics-cell {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px;
            text-align: center;
        }
        .metrics-label {
            font-size: 7.5px;
            text-transform: uppercase;
            font-weight: bold;
            color: #475569;
        }
        .metrics-val {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #0f172a;
        }
        .data-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-mono { font-family: 'Courier', monospace; }
        .font-bold { font-weight: bold; }
        .badge-success { color: #15803d; font-weight: bold; }
        .badge-danger { color: #b91c1c; font-weight: bold; }
        .badge-info { color: #0369a1; font-weight: bold; }
        
        .footer-note {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="text-left" style="vertical-align: top;">
                <div class="company-title">CATÁLOGO WEB POS</div>
                <div class="report-title">{{ $meta['titulo'] }}</div>
                <div class="report-sub">{{ $meta['subtitulo'] }}</div>
            </td>
            <td class="text-right" style="vertical-align: top; width: 35%;">
                <div style="font-size: 8.5px; color: #64748b;">
                    <div><strong>Generado:</strong> {{ $meta['fechaGeneracion'] }}</div>
                    <div><strong>Sucursal:</strong> {{ $meta['sucursalNombre'] }}</div>
                    <div><strong>Usuario:</strong> {{ $meta['usuarioNombre'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Metrics Summary Cards -->
    @if (!empty($metrics))
        <table class="metrics-grid">
            <tr>
                @foreach ($metrics as $label => $val)
                    <td class="metrics-cell">
                        <div class="metrics-label">{{ $label }}</div>
                        <div class="metrics-val">{{ $val }}</div>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    <!-- Data Tables per Report Type -->
    @if ($meta['tipoReporte'] === 'stock_sucursal')
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 4%;">#</th>
                    <th class="text-left">PRODUCTO</th>
                    <th class="text-left">SKU</th>
                    <th class="text-left">CATEGORÍA / MARCA</th>
                    <th class="text-left">SUCURSAL</th>
                    <th class="text-center">STOCK INICIAL</th>
                    <th class="text-center">STOCK ACTUAL</th>
                    <th class="text-right">P. COMPRA</th>
                    <th class="text-right">P. VENTA</th>
                    <th class="text-right">VALOR COSTO</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reporteData as $idx => $row)
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="font-bold">{{ $row->producto?->nombre }}</td>
                        <td class="font-mono">{{ $row->producto?->sku }}</td>
                        <td>{{ $row->producto?->categoria?->nombre }} / {{ $row->producto?->marca?->nombre }}</td>
                        <td>{{ $row->sucursal?->nombre }}</td>
                        <td class="text-center font-mono font-bold" style="color: #64748b;">
                            {{ number_format($row->stock_inicial ?? 0, 0) }}
                        </td>
                        <td class="text-center font-mono font-bold {{ $row->stock_actual <= 0 ? 'badge-danger' : 'badge-success' }}">
                            {{ number_format($row->stock_actual, 0) }}
                        </td>
                        <td class="text-right font-mono">Bs {{ number_format($row->producto?->precio_compra, 2) }}</td>
                        <td class="text-right font-mono">Bs {{ number_format($row->producto?->precio_venta, 2) }}</td>
                        <td class="text-right font-mono font-bold">
                            Bs {{ number_format($row->stock_actual * ($row->producto?->precio_compra ?? 0), 2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center">No se encontraron registros de inventario.</td></tr>
                @endforelse
            </tbody>
        </table>


    @elseif ($meta['tipoReporte'] === 'ventas_sucursal')
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th class="text-left">FECHA / HORA</th>
                    <th class="text-left font-mono">N° RECIBO</th>
                    <th class="text-left">CLIENTE</th>
                    <th class="text-left">SUCURSAL</th>
                    <th class="text-left">CAJERO</th>
                    <th class="text-center">PAGO</th>
                    <th class="text-right">TOTAL</th>
                    <th class="text-center">ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reporteData as $idx => $v)
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td>{{ $v->fecha_venta?->format('d/m/Y H:i') }}</td>
                        <td class="font-mono font-bold">{{ $v->numero_recibo }}</td>
                        <td>{{ $v->cliente?->nombre_razon_social ?? 'Cliente S/N' }}</td>
                        <td>{{ $v->sucursal?->nombre }}</td>
                        <td>{{ $v->usuario?->nombre_completo }}</td>
                        <td class="text-center">{{ $v->metodo_pago_principal }}</td>
                        <td class="text-right font-mono font-bold">Bs {{ number_format($v->total, 2) }}</td>
                        <td class="text-center">
                            @if ($v->estado === 'COMPLETADA')
                                <span class="badge-success">COMPLETADA</span>
                            @else
                                <span class="badge-danger">ANULADA</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">No se encontraron ventas en el rango seleccionado.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($meta['tipoReporte'] === 'ingresos_egresos')
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th class="text-left">FECHA / HORA</th>
                    <th class="text-center">TIPO</th>
                    <th class="text-left">CONCEPTO / REFERENCIA</th>
                    <th class="text-left">REGISTRADO POR</th>
                    <th class="text-center">MÉTODO PAGO</th>
                    <th class="text-right">MONTO (BS)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reporteData as $idx => $p)
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td>{{ $p->fecha_pago?->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            @if ($p->venta_id)
                                <span class="badge-success">VENTA</span>
                            @elseif ($p->monto > 0)
                                <span class="badge-info">INGRESO EXTRA</span>
                            @else
                                <span class="badge-danger">EGRESO / GASTO</span>
                            @endif
                        </td>
                        <td>
                            @if ($p->venta)
                                Venta N° {{ $p->venta->numero_recibo }} ({{ $p->venta->cliente?->nombre_razon_social }})
                            @else
                                {{ $p->referencia_transaccion }}
                            @endif
                        </td>
                        <td>{{ $p->usuario?->nombre_completo ?? $p->usuario?->name }}</td>
                        <td class="text-center">{{ $p->metodo_pago }}</td>
                        <td class="text-right font-mono font-bold {{ $p->monto >= 0 ? 'badge-success' : 'badge-danger' }}">
                            {{ $p->monto >= 0 ? '+Bs '.number_format($p->monto, 2) : '-Bs '.number_format(abs($p->monto), 2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No se encontraron movimientos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($meta['tipoReporte'] === 'utilidades')
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th class="text-left font-mono">N° RECIBO</th>
                    <th class="text-left">FECHA</th>
                    <th class="text-left">SUCURSAL</th>
                    <th class="text-right">VENTA BRUTA</th>
                    <th class="text-right">COSTO HISTÓRICO</th>
                    <th class="text-right">DESCUENTOS</th>
                    <th class="text-right">UTILIDAD NETA REAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reporteData as $idx => $v)
                    @php
                        $costoProductos = 0.00;
                        foreach ($v->detalles as $d) {
                            $mov = $v->movimientos->firstWhere('producto_id', $d->producto_id);
                            $costoUnitarioHistorico = $mov ? (float) $mov->precio_compra : (float) ($d->producto->precio_compra ?? 0);
                            $costoProductos += ($costoUnitarioHistorico * $d->cantidad);
                        }
                        $utilidadFila = $v->total - $costoProductos;
                    @endphp
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="font-mono font-bold">{{ $v->numero_recibo }}</td>
                        <td>{{ $v->fecha_venta?->format('d/m/Y H:i') }}</td>
                        <td>{{ $v->sucursal?->nombre }}</td>
                        <td class="text-right font-mono">Bs {{ number_format($v->subtotal, 2) }}</td>
                        <td class="text-right font-mono">Bs {{ number_format($costoProductos, 2) }}</td>
                        <td class="text-right font-mono badge-danger">-Bs {{ number_format($v->descuento_general, 2) }}</td>
                        <td class="text-right font-mono font-bold {{ $utilidadFila >= 0 ? 'badge-success' : 'badge-danger' }}">
                            Bs {{ number_format($utilidadFila, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No se registraron ventas completadas.</td></tr>
                @endforelse
            </tbody>
        </table>


    @elseif ($meta['tipoReporte'] === 'productos_top')
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">POS</th>
                    <th class="text-left">PRODUCTO</th>
                    <th class="text-left font-mono">SKU</th>
                    <th class="text-left">CATEGORÍA</th>
                    <th class="text-center">UNIDADES VENDIDAS</th>
                    <th class="text-right">MONTO GENERADO (BS)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reporteData as $idx => $row)
                    <tr>
                        <td class="text-center font-mono font-bold">{{ $idx + 1 }}°</td>
                        <td class="font-bold">{{ $row->producto?->nombre }}</td>
                        <td class="font-mono">{{ $row->producto?->sku }}</td>
                        <td>{{ $row->producto?->categoria?->nombre }}</td>
                        <td class="text-center font-mono font-bold text-lg" style="color: #0284c7;">
                            {{ number_format($row->total_cant, 0) }}
                        </td>
                        <td class="text-right font-mono font-bold badge-success">
                            Bs {{ number_format($row->total_monto, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No hay registros de ventas.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($meta['tipoReporte'] === 'arqueo_cajas')
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 4%;">#</th>
                    <th class="text-left">FECHA APERTURA</th>
                    <th class="text-left">SUCURSAL</th>
                    <th class="text-left">USUARIO / CAJERO</th>
                    <th class="text-right">APERTURA</th>
                    <th class="text-right">VENTAS EFECTIVO</th>
                    <th class="text-right">VENTAS QR / TRANSF.</th>
                    <th class="text-right">GASTOS</th>
                    <th class="text-right">MONTO FINAL</th>
                    <th class="text-center">ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reporteData as $idx => $c)
                    @php
                        $ventasEfectivo = $c->pagos->where('metodo_pago', 'EFECTIVO')->where('monto', '>', 0)->sum('monto');
                        $ventasDigital = $c->pagos->whereIn('metodo_pago', ['QR', 'TRANSFERENCIA'])->where('monto', '>', 0)->sum('monto');
                        $egresosGastos = abs($c->pagos->where('monto', '<', 0)->sum('monto'));
                    @endphp
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td>{{ $c->fecha_apertura?->format('d/m/Y H:i') }}</td>
                        <td>{{ $c->sucursal?->nombre }}</td>
                        <td>{{ $c->usuario?->nombre_completo }}</td>
                        <td class="text-right font-mono">Bs {{ number_format($c->monto_apertura, 2) }}</td>
                        <td class="text-right font-mono badge-success">Bs {{ number_format($ventasEfectivo, 2) }}</td>
                        <td class="text-right font-mono badge-info">Bs {{ number_format($ventasDigital, 2) }}</td>
                        <td class="text-right font-mono badge-danger">Bs {{ number_format($egresosGastos, 2) }}</td>
                        <td class="text-right font-mono font-bold">
                            Bs {{ number_format($c->monto_cierre ?? ($c->monto_apertura + $ventasEfectivo - $egresosGastos), 2) }}
                        </td>
                        <td class="text-center">
                            @if ($c->estado === 'ABIERTA')
                                <span class="badge-info">ABIERTA</span>
                            @else
                                <span class="badge-success">CERRADA</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center">No se registraron sesiones de caja.</td></tr>
                @endforelse
            </tbody>
        </table>

    @endif

    <div class="footer-note">
        Catálogo Web POS - Reporte Oficial Generado Automáticamente | Documento Confidencial de Control Interno
    </div>

</body>
</html>
