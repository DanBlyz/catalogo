<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo {{ $venta->numero_recibo }}</title>
    <style>
        @page {
            margin: 4px 6px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 2px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .font-bold {
            font-weight: bold;
        }
        .font-mono {
            font-family: 'Courier', monospace;
        }
        .uppercase {
            text-transform: uppercase;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .divider-double {
            border-top: 2px dashed #000;
            margin: 6px 0;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .table-items th {
            border-bottom: 1px dashed #000;
            padding: 3px 0;
            font-size: 8px;
            text-transform: uppercase;
        }
        .table-items td {
            padding: 3px 0;
            vertical-align: top;
        }
        .header-title {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header-sub {
            font-size: 9px;
            color: #333;
        }
        .total-box {
            font-size: 12px;
            font-weight: bold;
            margin-top: 4px;
        }
        .footer-note {
            font-size: 8px;
            margin-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Header / Sucursal Info -->
    <div class="text-center">
        <div class="header-title uppercase">MI CATALOGO</div>
        <div class="header-sub uppercase">{{ $venta->sucursal?->nombre ?? 'SUCURSAL PRINCIPAL' }}</div>
        @if ($venta->sucursal?->direccion)
            <div class="header-sub">{{ $venta->sucursal->direccion }}</div>
        @endif
        @if ($venta->sucursal?->telefono)
            <div class="header-sub">Tel: {{ $venta->sucursal->telefono }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Ticket Info -->
    <div class="text-center font-bold font-mono" style="font-size: 10px;">
        COMPROBANTE DE VENTA
    </div>
    <div class="text-center font-mono font-bold" style="font-size: 11px;">
        N° {{ $venta->numero_recibo }}
    </div>

    <div class="divider"></div>

    <!-- Customer & Sale Meta -->
    <table style="width: 100%; font-size: 8.5px;">
        <tr>
            <td class="font-bold">FECHA:</td>
            <td class="text-right font-mono">{{ $venta->fecha_venta?->format('d/m/Y H:i') ?? date('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="font-bold">ATENDIDO POR:</td>
            <td class="text-right">{{ $venta->usuario?->nombre_completo ?? 'Cajero' }}</td>
        </tr>
        <tr>
            <td class="font-bold">CLIENTE:</td>
            <td class="text-right">{{ $venta->cliente?->nombre_razon_social ?? 'Cliente S/N' }}</td>
        </tr>
        @if ($venta->cliente?->cedula_nit_ruc && $venta->cliente->cedula_nit_ruc !== '0')
            <tr>
                <td class="font-bold">NIT / CI:</td>
                <td class="text-right font-mono">{{ $venta->cliente->cedula_nit_ruc }}</td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    <!-- Items Table -->
    <table class="table-items">
        <thead>
            <tr>
                <th class="text-left" style="width: 45%;">PRODUCTO</th>
                <th class="text-center" style="width: 15%;">CANT</th>
                <th class="text-right" style="width: 20%;">P.UNIT</th>
                <th class="text-right" style="width: 20%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->detalles as $det)
                <tr>
                    <td class="text-left font-bold" colspan="4">
                        {{ $det->producto?->nombre ?? 'Producto' }}
                    </td>
                </tr>
                <tr>
                    <td class="text-left font-mono" style="color: #444; font-size: 8px;">
                        SKU: {{ $det->producto?->sku }}
                    </td>
                    <td class="text-center font-mono font-bold">
                        {{ number_format($det->cantidad, 0) }}
                    </td>
                    <td class="text-right font-mono">
                        {{ number_format($det->precio_unitario, 2) }}
                    </td>
                    <td class="text-right font-mono font-bold">
                        {{ number_format($det->subtotal, 2) }}
                    </td>
                </tr>
                @if ($det->descuento_unitario > 0)
                    <tr>
                        <td colspan="4" class="text-right font-mono" style="font-size: 7.5px; color: #555;">
                            (Desc. item: -Bs {{ number_format($det->descuento_unitario, 2) }})
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Totals Breakdown -->
    <table style="width: 100%; font-size: 9px;">
        <tr>
            <td>SUBTOTAL BRUTO:</td>
            <td class="text-right font-mono">Bs {{ number_format($venta->subtotal, 2) }}</td>
        </tr>
        @if ($venta->descuento_general > 0)
            <tr>
                <td>DESCUENTO GENERAL:</td>
                <td class="text-right font-mono">-Bs {{ number_format($venta->descuento_general, 2) }}</td>
            </tr>
        @endif
    </table>

    <div class="divider-double"></div>

    <table style="width: 100%;" class="total-box">
        <tr>
            <td class="font-bold uppercase">TOTAL A PAGAR:</td>
            <td class="text-right font-mono font-bold" style="font-size: 13px;">
                Bs {{ number_format($venta->total, 2) }}
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Payment Method Details -->
    <table style="width: 100%; font-size: 8.5px;">
        <tr>
            <td class="font-bold">FORMA DE PAGO:</td>
            <td class="text-right font-bold uppercase">{{ $venta->metodo_pago_principal }}</td>
        </tr>
        @if ($venta->metodo_pago_principal === 'EFECTIVO')
            <tr>
                <td>EFECTIVO RECIBIDO:</td>
                <td class="text-right font-mono">Bs {{ number_format($venta->monto_pagado, 2) }}</td>
            </tr>
            <tr>
                <td class="font-bold">CAMBIO ENTREGADO:</td>
                <td class="text-right font-mono font-bold">Bs {{ number_format($venta->cambio, 2) }}</td>
            </tr>
        @else
            @php $firstPago = $venta->pagos->first(); @endphp
            @if ($firstPago && $firstPago->referencia_transaccion)
                <tr>
                    <td>REF. COMPROBANTE:</td>
                    <td class="text-right font-mono">{{ $firstPago->referencia_transaccion }}</td>
                </tr>
            @endif
        @endif
    </table>

    <div class="divider"></div>

    <!-- Footer Note -->
    <div class="text-center footer-note">
        <div>¡GRACIAS POR SU COMPRA!</div>
        <div>CONSERVE ESTE TICKET PARA CUALQUIER RECLAMO</div>
    </div>

</body>
</html>
