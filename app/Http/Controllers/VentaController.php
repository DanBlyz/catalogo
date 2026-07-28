<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class VentaController extends Controller
{
    public function generarRecibo(Venta $venta): Response
    {
        $venta->load(['sucursal', 'usuario', 'cliente', 'detalles.producto', 'pagos']);

        // 80mm width = 226.77pt (80mm / 25.4 * 72)
        $itemCount = $venta->detalles->count();
        $paperHeight = max(400, 320 + ($itemCount * 35));

        $pdf = Pdf::loadView('pdf.recibo_venta', [
            'venta' => $venta,
        ])->setPaper([0, 0, 226.77, $paperHeight], 'portrait');

        return $pdf->stream("Recibo_{$venta->numero_recibo}.pdf");
    }
}
