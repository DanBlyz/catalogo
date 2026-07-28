<?php

namespace App\Livewire\Operacion;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\ProductoSucursal;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Movimientos extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    #[Url(history: true)]
    public string $tipoFilter = '';

    #[Url(history: true)]
    public string $sucursalFilter = '';

    // Ingreso en Lote Modal
    public bool $isIngresoModalOpen = false;

    public ?int $selectedProductoIdForIngreso = null;

    public float $cantidadIngreso = 1.0;

    public float $precioCompraIngreso = 0.00;

    public float $precioVentaIngreso = 0.00;

    public string $observacionesIngreso = '';

    public array $ingresoItems = [];

    // Salida Directa Modal
    public bool $isSalidaModalOpen = false;

    public ?int $selectedProductoIdForSalida = null;

    public float $cantidadSalida = 1.0;

    public string $motivoSalida = '';

    public string $observacionesSalida = '';

    // Transferencia Modal
    public bool $isTransferenciaModalOpen = false;

    public ?int $selectedProductoIdForTransferencia = null;

    public ?int $sucursalDestinoId = null;

    public float $cantidadTransferencia = 1.0;

    public string $observacionesTransferencia = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingTipoFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSucursalFilter(): void
    {
        $this->resetPage();
    }

    // =========================================
    // INGRESO EN LOTE METHODS
    // =========================================
    public function openIngresoModal(): void
    {
        $this->resetIngresoForm();
        $this->isIngresoModalOpen = true;
    }

    public function closeIngresoModal(): void
    {
        $this->isIngresoModalOpen = false;
        $this->resetIngresoForm();
    }

    private function resetIngresoForm(): void
    {
        $this->selectedProductoIdForIngreso = null;
        $this->cantidadIngreso = 1.0;
        $this->precioCompraIngreso = 0.00;
        $this->precioVentaIngreso = 0.00;
        $this->observacionesIngreso = '';
        $this->ingresoItems = [];
        $this->resetValidation();
    }

    public function updatedSelectedProductoIdForIngreso($value): void
    {
        if ($value) {
            $producto = Producto::find($value);
            if ($producto) {
                $this->precioCompraIngreso = (float) $producto->precio_compra;
                $this->precioVentaIngreso = (float) $producto->precio_venta;
            }
        }
    }

    public function addItemToIngresoList(): void
    {
        $this->validate([
            'selectedProductoIdForIngreso' => 'required|exists:productos,id',
            'cantidadIngreso' => 'required|numeric|gt:0',
            'precioCompraIngreso' => 'required|numeric|gte:0',
            'precioVentaIngreso' => 'required|numeric|gte:0',
        ], [
            'selectedProductoIdForIngreso.required' => 'Debe seleccionar un producto.',
            'cantidadIngreso.gt' => 'La cantidad debe ser mayor a cero.',
            'precioCompraIngreso.gte' => 'El costo no puede ser negativo.',
            'precioVentaIngreso.gte' => 'El precio de venta no puede ser negativo.',
        ]);

        $producto = Producto::findOrFail($this->selectedProductoIdForIngreso);

        // Check if item already exists in list
        foreach ($this->ingresoItems as $index => $item) {
            if ($item['producto_id'] === $producto->id) {
                $this->ingresoItems[$index]['cantidad'] += (float) $this->cantidadIngreso;
                $this->ingresoItems[$index]['precio_compra'] = (float) $this->precioCompraIngreso;
                $this->ingresoItems[$index]['precio_venta'] = (float) $this->precioVentaIngreso;
                $this->ingresoItems[$index]['subtotal'] = $this->ingresoItems[$index]['cantidad'] * $this->ingresoItems[$index]['precio_compra'];
                $this->selectedProductoIdForIngreso = null;
                $this->cantidadIngreso = 1.0;

                return;
            }
        }

        $subtotal = (float) $this->cantidadIngreso * (float) $this->precioCompraIngreso;

        $this->ingresoItems[] = [
            'producto_id' => $producto->id,
            'nombre' => $producto->nombre,
            'sku' => $producto->sku,
            'cantidad' => (float) $this->cantidadIngreso,
            'precio_compra' => (float) $this->precioCompraIngreso,
            'precio_venta' => (float) $this->precioVentaIngreso,
            'subtotal' => $subtotal,
        ];

        $this->selectedProductoIdForIngreso = null;
        $this->cantidadIngreso = 1.0;
        $this->precioCompraIngreso = 0.00;
        $this->precioVentaIngreso = 0.00;
    }

    public function removeItemFromIngresoList(int $index): void
    {
        if (isset($this->ingresoItems[$index])) {
            array_splice($this->ingresoItems, $index, 1);
        }
    }

    public function saveIngresoLote(): void
    {
        if (empty($this->ingresoItems)) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Lista vacía',
                'text' => 'Agrega al menos un producto a la lista antes de procesar el ingreso.',
            ]);

            return;
        }

        try {
            DB::transaction(function () {
                $user = Auth::user();
                $sucursalId = $user->sucursal_id ?? 1;

                foreach ($this->ingresoItems as $item) {
                    // Update master product prices if adjusted during inflow batch
                    $producto = Producto::find($item['producto_id']);
                    if ($producto) {
                        $producto->update([
                            'precio_compra' => $item['precio_compra'],
                            'precio_venta' => $item['precio_venta'],
                        ]);
                    }

                    $ps = ProductoSucursal::firstOrCreate([
                        'producto_id' => $item['producto_id'],
                        'sucursal_id' => $sucursalId,
                    ], [
                        'stock_actual' => 0.00,
                    ]);

                    $stockAnterior = (float) $ps->stock_actual;
                    $stockNuevo = $stockAnterior + (float) $item['cantidad'];

                    $ps->update(['stock_actual' => $stockNuevo]);

                    MovimientoInventario::create([
                        'sucursal_id' => $sucursalId,
                        'producto_id' => $item['producto_id'],
                        'tipo_movimiento' => 'INGRESO',
                        'cantidad' => $item['cantidad'],
                        'precio_compra' => $item['precio_compra'],
                        'precio_venta' => $item['precio_venta'],
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockNuevo,
                        'motivo' => $this->observacionesIngreso ? trim($this->observacionesIngreso) : 'Ingreso de stock en lote',
                        'referencia_tipo' => 'INGRESO_LOTE',
                        'fecha_movimiento' => now(),
                    ]);
                }
            });

            $this->closeIngresoModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => '¡Ingreso Procesado!',
                'text' => 'Se ha registrado el ingreso de inventario exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => $e->getMessage(),
            ]);
        }
    }

    // =========================================
    // SALIDA DIRECTA METHODS
    // =========================================
    public function openSalidaModal(): void
    {
        $this->resetSalidaForm();
        $this->isSalidaModalOpen = true;
    }

    public function closeSalidaModal(): void
    {
        $this->isSalidaModalOpen = false;
        $this->resetSalidaForm();
    }

    private function resetSalidaForm(): void
    {
        $this->selectedProductoIdForSalida = null;
        $this->cantidadSalida = 1.0;
        $this->motivoSalida = 'PÉRDIDA / DAÑO';
        $this->observacionesSalida = '';
        $this->resetValidation();
    }

    public function saveSalida(): void
    {
        $this->validate([
            'selectedProductoIdForSalida' => 'required|exists:productos,id',
            'cantidadSalida' => 'required|numeric|gt:0',
            'motivoSalida' => 'required|string|max:100',
            'observacionesSalida' => 'nullable|string|max:255',
        ], [
            'selectedProductoIdForSalida.required' => 'Debe seleccionar un producto.',
            'cantidadSalida.gt' => 'La cantidad debe ser mayor a cero.',
            'motivoSalida.required' => 'Debe especificar el motivo de la salida.',
        ]);

        try {
            $user = Auth::user();
            $sucursalId = $user->sucursal_id ?? 1;

            $ps = ProductoSucursal::where('producto_id', $this->selectedProductoIdForSalida)
                ->where('sucursal_id', $sucursalId)
                ->first();

            $stockActual = $ps ? (float) $ps->stock_actual : 0.0;

            if ($this->cantidadSalida > $stockActual) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Stock Insuficiente',
                    'text' => "Stock disponible en esta sucursal: {$stockActual}. No se puede retirar {$this->cantidadSalida}.",
                ]);

                return;
            }

            DB::transaction(function () use ($ps, $stockActual, $sucursalId) {
                $stockAnterior = $stockActual;
                $stockNuevo = $stockAnterior - (float) $this->cantidadSalida;

                $ps->update(['stock_actual' => $stockNuevo]);

                $producto = Producto::find($this->selectedProductoIdForSalida);
                $motivoTexto = trim($this->motivoSalida).' - '.trim($this->observacionesSalida);

                MovimientoInventario::create([
                    'sucursal_id' => $sucursalId,
                    'producto_id' => $this->selectedProductoIdForSalida,
                    'tipo_movimiento' => 'SALIDA',
                    'cantidad' => $this->cantidadSalida,
                    'precio_compra' => $producto->precio_compra ?? 0,
                    'precio_venta' => $producto->precio_venta ?? 0,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'motivo' => trim($motivoTexto, ' -'),
                    'referencia_tipo' => 'SALIDA_DIRECTA',
                    'fecha_movimiento' => now(),
                ]);
            });

            $this->closeSalidaModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => '¡Salida Procesada!',
                'text' => 'Se ha registrado la salida de stock correctamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => $e->getMessage(),
            ]);
        }
    }

    // =========================================
    // TRANSFERENCIA ENTRE SUCURSALES METHODS
    // =========================================
    public function openTransferenciaModal(): void
    {
        $this->resetTransferenciaForm();
        $this->isTransferenciaModalOpen = true;
    }

    public function closeTransferenciaModal(): void
    {
        $this->isTransferenciaModalOpen = false;
        $this->resetTransferenciaForm();
    }

    private function resetTransferenciaForm(): void
    {
        $this->selectedProductoIdForTransferencia = null;
        $this->sucursalDestinoId = null;
        $this->cantidadTransferencia = 1.0;
        $this->observacionesTransferencia = '';
        $this->resetValidation();
    }

    public function saveTransferencia(): void
    {
        $user = Auth::user();
        $sucursalOrigenId = $user->sucursal_id ?? 1;

        $this->validate([
            'selectedProductoIdForTransferencia' => 'required|exists:productos,id',
            'sucursalDestinoId' => 'required|exists:sucursales,id|different:'.$sucursalOrigenId,
            'cantidadTransferencia' => 'required|numeric|gt:0',
            'observacionesTransferencia' => 'nullable|string|max:255',
        ], [
            'selectedProductoIdForTransferencia.required' => 'Debe seleccionar un producto.',
            'sucursalDestinoId.required' => 'Debe seleccionar la sucursal de destino.',
            'sucursalDestinoId.different' => 'La sucursal de destino debe ser diferente a la de origen.',
            'cantidadTransferencia.gt' => 'La cantidad a transferir debe ser mayor a cero.',
        ]);

        try {
            $originPs = ProductoSucursal::where('producto_id', $this->selectedProductoIdForTransferencia)
                ->where('sucursal_id', $sucursalOrigenId)
                ->first();

            $stockOrigenActual = $originPs ? (float) $originPs->stock_actual : 0.0;

            if ($this->cantidadTransferencia > $stockOrigenActual) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Stock Insuficiente',
                    'text' => "Stock disponible en la sucursal origen: {$stockOrigenActual}. No se puede transferir {$this->cantidadTransferencia}.",
                ]);

                return;
            }

            DB::transaction(function () use ($originPs, $stockOrigenActual, $sucursalOrigenId) {
                $producto = Producto::find($this->selectedProductoIdForTransferencia);
                $destSucursal = Sucursal::find($this->sucursalDestinoId);
                $originSucursal = Sucursal::find($sucursalOrigenId);

                // 1. Decrement Stock Origin
                $stockOrigenAnterior = $stockOrigenActual;
                $stockOrigenNuevo = $stockOrigenAnterior - (float) $this->cantidadTransferencia;
                $originPs->update(['stock_actual' => $stockOrigenNuevo]);

                // Record Origin Movement
                MovimientoInventario::create([
                    'sucursal_id' => $sucursalOrigenId,
                    'producto_id' => $this->selectedProductoIdForTransferencia,
                    'tipo_movimiento' => 'TRANSFERENCIA_SALIDA',
                    'cantidad' => $this->cantidadTransferencia,
                    'precio_compra' => $producto->precio_compra ?? 0,
                    'precio_venta' => $producto->precio_venta ?? 0,
                    'stock_anterior' => $stockOrigenAnterior,
                    'stock_nuevo' => $stockOrigenNuevo,
                    'motivo' => 'Transferido a sucursal: '.($destSucursal->nombre ?? '').($this->observacionesTransferencia ? ' | '.$this->observacionesTransferencia : ''),
                    'referencia_tipo' => 'TRANSFERENCIA',
                    'fecha_movimiento' => now(),
                ]);

                // 2. Increment Stock Destination
                $destPs = ProductoSucursal::firstOrCreate([
                    'producto_id' => $this->selectedProductoIdForTransferencia,
                    'sucursal_id' => $this->sucursalDestinoId,
                ], [
                    'stock_actual' => 0.00,
                ]);

                $stockDestAnterior = (float) $destPs->stock_actual;
                $stockDestNuevo = $stockDestAnterior + (float) $this->cantidadTransferencia;
                $destPs->update(['stock_actual' => $stockDestNuevo]);

                // Record Destination Movement
                MovimientoInventario::create([
                    'sucursal_id' => $this->sucursalDestinoId,
                    'producto_id' => $this->selectedProductoIdForTransferencia,
                    'tipo_movimiento' => 'TRANSFERENCIA_ENTRADA',
                    'cantidad' => $this->cantidadTransferencia,
                    'precio_compra' => $producto->precio_compra ?? 0,
                    'precio_venta' => $producto->precio_venta ?? 0,
                    'stock_anterior' => $stockDestAnterior,
                    'stock_nuevo' => $stockDestNuevo,
                    'motivo' => 'Recibido de sucursal: '.($originSucursal->nombre ?? '').($this->observacionesTransferencia ? ' | '.$this->observacionesTransferencia : ''),
                    'referencia_tipo' => 'TRANSFERENCIA',
                    'fecha_movimiento' => now(),
                ]);
            });

            $this->closeTransferenciaModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => '¡Transferencia Exitosa!',
                'text' => 'Se ha completado el traspaso de productos entre sucursales.',
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

        $movimientosQuery = MovimientoInventario::with(['producto', 'sucursal']);

        // Scope by branch if not admin or if sucursalFilter is explicitly selected
        if (! $user->esAdmin()) {
            $movimientosQuery->where('sucursal_id', $sucursalId);
        } elseif ($this->sucursalFilter) {
            $movimientosQuery->where('sucursal_id', $this->sucursalFilter);
        }

        if ($this->tipoFilter) {
            $movimientosQuery->where('tipo_movimiento', 'like', '%'.$this->tipoFilter.'%');
        }

        if ($this->search) {
            $movimientosQuery->where(function ($q) {
                $q->whereHas('producto', function ($p) {
                    $p->where('nombre', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
                })
                    ->orWhere('motivo', 'like', '%'.$this->search.'%')
                    ->orWhere('referencia_tipo', 'like', '%'.$this->search.'%');
            });
        }

        $movimientos = $movimientosQuery->latest('id')->paginate($this->perPage);

        // Fetch products available in current branch for forms
        $allProductos = Producto::orderBy('nombre')->get();
        $sucursalesDestino = Sucursal::where('id', '!=', $sucursalId)->orderBy('nombre')->get();
        $todasSucursales = Sucursal::orderBy('nombre')->get();

        // Calculate available stock for selected product in current branch
        $stockActualSeleccionadoSalida = 0.0;
        if ($this->selectedProductoIdForSalida) {
            $ps = ProductoSucursal::where('producto_id', $this->selectedProductoIdForSalida)
                ->where('sucursal_id', $sucursalId)->first();
            $stockActualSeleccionadoSalida = $ps ? (float) $ps->stock_actual : 0.0;
        }

        $stockActualSeleccionadoTransferencia = 0.0;
        if ($this->selectedProductoIdForTransferencia) {
            $ps = ProductoSucursal::where('producto_id', $this->selectedProductoIdForTransferencia)
                ->where('sucursal_id', $sucursalId)->first();
            $stockActualSeleccionadoTransferencia = $ps ? (float) $ps->stock_actual : 0.0;
        }

        return view('livewire.operacion.movimientos', [
            'movimientos' => $movimientos,
            'allProductos' => $allProductos,
            'sucursalesDestino' => $sucursalesDestino,
            'todasSucursales' => $todasSucursales,
            'stockActualSeleccionadoSalida' => $stockActualSeleccionadoSalida,
            'stockActualSeleccionadoTransferencia' => $stockActualSeleccionadoTransferencia,
        ]);
    }
}
