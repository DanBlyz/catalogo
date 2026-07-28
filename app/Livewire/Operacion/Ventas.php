<?php

namespace App\Livewire\Operacion;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\ProductoSucursal;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Ventas extends Component
{
    // Client selection
    public ?int $cliente_id = null;

    public string $clienteSearch = '';

    // Product Live Search
    public string $searchProducto = '';

    // Sales Cart Array
    public array $cart = [];

    // General Discount & Payment
    public float $descuentoGeneral = 0.00;

    public string $metodoPago = 'EFECTIVO';

    public ?float $montoPagado = null;

    public string $referenciaTransaccion = '';

    public string $observaciones = '';

    // Quick Apertura Modal
    public bool $isQuickAperturaModalOpen = false;

    public float $montoAperturaRapida = 0.00;

    public string $observacionesAperturaRapida = 'Apertura desde Punto de Venta';

    public function mount(): void
    {
        $this->ensureDefaultClientExists();
    }

    private function ensureDefaultClientExists(): void
    {
        if (! Cliente::where('id', 1)->exists()) {
            Cliente::create([
                'id' => 1,
                'nombre_razon_social' => 'Cliente S/N (Ventas Mostrador)',
                'cedula_nit_ruc' => '0',
            ]);
        }
    }

    public function openQuickAperturaModal(): void
    {
        $this->montoAperturaRapida = 0.00;
        $this->observacionesAperturaRapida = 'Apertura desde Punto de Venta';
        $this->isQuickAperturaModalOpen = true;
    }

    public function closeQuickAperturaModal(): void
    {
        $this->isQuickAperturaModalOpen = false;
    }

    public function saveQuickApertura(): void
    {
        $this->validate([
            'montoAperturaRapida' => 'required|numeric|min:0',
        ], [
            'montoAperturaRapida.required' => 'El monto inicial es obligatorio.',
            'montoAperturaRapida.min' => 'El monto no puede ser negativo.',
        ]);

        try {
            $user = Auth::user();
            $sucursalId = $user->sucursal_id ?? 1;

            $hasOpenBox = Caja::where('usuario_id', $user->id)
                ->where('estado', 'ABIERTA')
                ->exists();

            if ($hasOpenBox) {
                $this->closeQuickAperturaModal();

                return;
            }

            Caja::create([
                'sucursal_id' => $sucursalId,
                'usuario_id' => $user->id,
                'monto_apertura' => $this->montoAperturaRapida,
                'monto_cierre' => 0.00,
                'ventas_efectivo' => 0.00,
                'ventas_digital' => 0.00,
                'total_esperado' => $this->montoAperturaRapida,
                'diferencia' => 0.00,
                'estado' => 'ABIERTA',
                'fecha_apertura' => now(),
                'observaciones' => trim($this->observacionesAperturaRapida),
            ]);

            $this->closeQuickAperturaModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => '¡Caja Aperturada!',
                'text' => 'Caja abierta con éxito. Ya puedes realizar ventas.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Error al aperturar caja',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function addToCart(int $productoId, float $cantidad = 1.0): void
    {
        $user = Auth::user();
        $sucursalId = $user->sucursal_id ?? 1;

        $producto = Producto::findOrFail($productoId);

        $ps = ProductoSucursal::where('producto_id', $producto->id)
            ->where('sucursal_id', $sucursalId)
            ->first();

        $stockDisponible = $ps ? (float) $ps->stock_actual : 0.0;

        // Check if item is already in cart
        $existingIndex = null;
        foreach ($this->cart as $idx => $item) {
            if ($item['producto_id'] === $producto->id) {
                $existingIndex = $idx;
                break;
            }
        }

        $nuevaCantidad = $cantidad;
        if ($existingIndex !== null) {
            $nuevaCantidad = $this->cart[$existingIndex]['cantidad'] + $cantidad;
        }

        if ($nuevaCantidad > $stockDisponible) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Stock Insuficiente',
                'text' => "El producto '{$producto->nombre}' sólo cuenta con {$stockDisponible} unidades en esta sucursal.",
            ]);

            return;
        }

        if ($existingIndex !== null) {
            $this->cart[$existingIndex]['cantidad'] = $nuevaCantidad;
            $this->cart[$existingIndex]['subtotal'] = ($this->cart[$existingIndex]['precio_unitario'] * $nuevaCantidad) - $this->cart[$existingIndex]['descuento_unitario'];
        } else {
            $precioUnitario = (float) $producto->precio_venta;
            $this->cart[] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'sku' => $producto->sku,
                'precio_unitario' => $precioUnitario,
                'cantidad' => $cantidad,
                'descuento_unitario' => 0.00,
                'stock_disponible' => $stockDisponible,
                'subtotal' => ($precioUnitario * $cantidad),
            ];
        }

        $this->searchProducto = '';
    }

    public function updateCartItemQuantity(int $index, float $quantity): void
    {
        if (! isset($this->cart[$index])) {
            return;
        }

        if ($quantity <= 0) {
            $this->removeFromCart($index);

            return;
        }

        $stockDisponible = $this->cart[$index]['stock_disponible'];
        if ($quantity > $stockDisponible) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Excede el Stock',
                'text' => "Stock máximo disponible: {$stockDisponible}.",
            ]);

            return;
        }

        $this->cart[$index]['cantidad'] = $quantity;
        $this->recalculateCartItemSubtotal($index);
    }

    public function updateCartItemDiscount(int $index, float $discount): void
    {
        if (! isset($this->cart[$index])) {
            return;
        }

        $discount = max(0, $discount);
        $totalBruto = $this->cart[$index]['precio_unitario'] * $this->cart[$index]['cantidad'];

        if ($discount > $totalBruto) {
            $discount = $totalBruto;
        }

        $this->cart[$index]['descuento_unitario'] = $discount;
        $this->recalculateCartItemSubtotal($index);
    }

    private function recalculateCartItemSubtotal(int $index): void
    {
        $bruto = $this->cart[$index]['precio_unitario'] * $this->cart[$index]['cantidad'];
        $desc = $this->cart[$index]['descuento_unitario'];
        $this->cart[$index]['subtotal'] = max(0, $bruto - $desc);
    }

    public function removeFromCart(int $index): void
    {
        if (isset($this->cart[$index])) {
            array_splice($this->cart, $index, 1);
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->descuentoGeneral = 0.00;
    }

    public function saveVenta(): void
    {
        $user = Auth::user();
        $sucursalId = $user->sucursal_id ?? 1;

        // 1. Verify active box
        $cajaActive = Caja::where('usuario_id', $user->id)
            ->where('estado', 'ABIERTA')
            ->first();

        if (! $cajaActive) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Caja no Aperturada',
                'text' => 'Debes aperturar una caja activa para procesar la venta.',
            ]);

            return;
        }

        // 2. Verify cart not empty
        if (empty($this->cart)) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Carrito Vacío',
                'text' => 'Debes agregar al menos un producto al carrito de compras.',
            ]);

            return;
        }

        // 3. Compute totals
        $brutoItemsSum = 0.00;
        $descuentosItemsSum = 0.00;
        foreach ($this->cart as $item) {
            $brutoItemsSum += ($item['precio_unitario'] * $item['cantidad']);
            $descuentosItemsSum += $item['descuento_unitario'];
        }

        $descuentoGen = max(0, (float) $this->descuentoGeneral);
        $totalFinal = max(0, ($brutoItemsSum - $descuentosItemsSum - $descuentoGen));

        $montoRecibidoFinal = $this->metodoPago === 'EFECTIVO' ? ($this->montoPagado ?? $totalFinal) : $totalFinal;
        $cambioCalculado = max(0, $montoRecibidoFinal - $totalFinal);

        if ($this->metodoPago === 'EFECTIVO' && $this->montoPagado !== null && $this->montoPagado < $totalFinal) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Monto Insuficiente',
                'text' => 'El monto recibido en efectivo es menor al total a pagar.',
            ]);

            return;
        }

        try {
            $createdVenta = null;

            DB::transaction(function () use ($user, $sucursalId, $cajaActive, $brutoItemsSum, $descuentoGen, $totalFinal, $montoRecibidoFinal, $cambioCalculado, &$createdVenta) {
                // Generate Invoice Number
                $receiptNumber = 'REC-'.date('Ymd').'-'.str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                $targetClienteId = $this->cliente_id ?: 1;

                // 1. Create Venta
                $venta = Venta::create([
                    'numero_recibo' => $receiptNumber,
                    'sucursal_id' => $sucursalId,
                    'caja_id' => $cajaActive->id,
                    'usuario_id' => $user->id,
                    'cliente_id' => $targetClienteId,
                    'subtotal' => $brutoItemsSum,
                    'descuento_general' => $descuentoGen,
                    'total' => $totalFinal,
                    'monto_pagado' => $montoRecibidoFinal,
                    'cambio' => $cambioCalculado,
                    'metodo_pago_principal' => $this->metodoPago,
                    'estado' => 'COMPLETADA',
                    'fecha_venta' => now(),
                ]);

                $createdVenta = $venta;

                // 2. Loop Cart: Create DetalleVenta & update Stock & Movimientos
                foreach ($this->cart as $item) {
                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'descuento_unitario' => $item['descuento_unitario'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    $ps = ProductoSucursal::where('producto_id', $item['producto_id'])
                        ->where('sucursal_id', $sucursalId)
                        ->first();

                    $stockAnterior = $ps ? (float) $ps->stock_actual : 0.0;
                    $stockNuevo = max(0, $stockAnterior - (float) $item['cantidad']);

                    if ($ps) {
                        $ps->update(['stock_actual' => $stockNuevo]);
                    }

                    $productoObj = Producto::find($item['producto_id']);

                    MovimientoInventario::create([
                        'sucursal_id' => $sucursalId,
                        'producto_id' => $item['producto_id'],
                        'tipo_movimiento' => 'VENTA',
                        'cantidad' => $item['cantidad'],
                        'precio_compra' => $productoObj->precio_compra ?? 0,
                        'precio_venta' => $item['precio_unitario'],
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockNuevo,
                        'motivo' => "Venta realizada N° {$receiptNumber}",
                        'referencia_tipo' => 'VENTA',
                        'referencia_id' => $venta->id,
                        'fecha_movimiento' => now(),
                    ]);
                }

                // 3. Create Pago
                Pago::create([
                    'venta_id' => $venta->id,
                    'caja_id' => $cajaActive->id,
                    'metodo_pago' => $this->metodoPago,
                    'monto' => $totalFinal,
                    'referencia_transaccion' => $this->referenciaTransaccion ? trim($this->referenciaTransaccion) : null,
                    'fecha_pago' => now(),
                ]);
            });

            $this->clearCart();
            $this->cliente_id = null;
            $this->montoPagado = null;
            $this->referenciaTransaccion = '';
            $this->observaciones = '';

            $pdfUrl = route('operacion.ventas.recibo', $createdVenta->id);

            $this->dispatch('swal:venta-success', [
                'title' => '¡Venta Registrada!',
                'text' => "La venta N° {$createdVenta->numero_recibo} ha sido procesada correctamente.",
                'pdf_url' => $pdfUrl,
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $sucursalId = $user->sucursal_id ?? 1;

        // Active Box Check
        $activeCaja = Caja::where('usuario_id', $user->id)
            ->where('estado', 'ABIERTA')
            ->first();

        // Clients List for Selector
        $clientes = Cliente::orderBy('nombre_razon_social')->get();

        // Product Live Search Query (matching name or SKU with available stock in current branch)
        $searchResults = [];
        if (trim($this->searchProducto) !== '') {
            $queryText = trim($this->searchProducto);

            $searchResults = DB::table('productos as p')
                ->leftJoin('producto_sucursal as ps', function ($join) use ($sucursalId) {
                    $join->on('p.id', '=', 'ps.producto_id')
                        ->where('ps.sucursal_id', '=', $sucursalId);
                })
                ->whereNull('p.deleted_at')
                ->where(function ($q) use ($queryText) {
                    $q->where('p.nombre', 'like', "%{$queryText}%")
                        ->orWhere('p.sku', 'like', "%{$queryText}%");
                })
                ->select(
                    'p.id',
                    'p.nombre',
                    'p.sku',
                    'p.precio_venta',
                    'p.precio_compra',
                    DB::raw('COALESCE(ps.stock_actual, 0) as stock_actual')
                )
                ->limit(10)
                ->get();
        }

        // Totals Calculations
        $cartSubtotalBruto = 0.00;
        $cartDescuentosItemsSum = 0.00;

        foreach ($this->cart as $item) {
            $cartSubtotalBruto += ($item['precio_unitario'] * $item['cantidad']);
            $cartDescuentosItemsSum += $item['descuento_unitario'];
        }

        $descuentoGenFinal = max(0, (float) $this->descuentoGeneral);
        $totalDescuentosAcumulado = $cartDescuentosItemsSum + $descuentoGenFinal;
        $totalFinalAPagar = max(0, $cartSubtotalBruto - $totalDescuentosAcumulado);
        $cambioEfectivo = $this->montoPagado !== null ? max(0, $this->montoPagado - $totalFinalAPagar) : 0.00;

        return view('livewire.operacion.ventas', [
            'activeCaja' => $activeCaja,
            'clientes' => $clientes,
            'searchResults' => $searchResults,
            'cartSubtotalBruto' => $cartSubtotalBruto,
            'cartDescuentosItemsSum' => $cartDescuentosItemsSum,
            'totalDescuentosAcumulado' => $totalDescuentosAcumulado,
            'totalFinalAPagar' => $totalFinalAPagar,
            'cambioEfectivo' => $cambioEfectivo,
        ]);
    }
}
