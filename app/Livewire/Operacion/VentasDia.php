<?php

namespace App\Livewire\Operacion;

use App\Models\MovimientoInventario;
use App\Models\ProductoSucursal;
use App\Models\Sucursal;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class VentasDia extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    #[Url(history: true)]
    public string $sucursalFilter = '';

    #[Url(history: true)]
    public string $estadoFilter = '';

    #[Url(history: true)]
    public string $fechaInicio = '';

    #[Url(history: true)]
    public string $fechaFin = '';

    // Details Modal
    public bool $isDetailModalOpen = false;

    public ?Venta $selectedVenta = null;

    // Cancellation / Return Modal
    public bool $isAnularModalOpen = false;

    public ?int $ventaIdToAnular = null;

    public string $motivoAnulacion = '';

    public function mount(): void
    {
        if (empty($this->fechaInicio)) {
            $this->fechaInicio = date('Y-m-d');
        }
        if (empty($this->fechaFin)) {
            $this->fechaFin = date('Y-m-d');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingSucursalFilter(): void
    {
        $this->resetPage();
    }

    public function updatingEstadoFilter(): void
    {
        $this->resetPage();
    }

    public function updatingFechaInicio(): void
    {
        $this->resetPage();
    }

    public function updatingFechaFin(): void
    {
        $this->resetPage();
    }

    public function openDetailModal(int $id): void
    {
        $this->selectedVenta = Venta::with(['sucursal', 'usuario', 'cliente', 'detalles.producto', 'pagos'])->findOrFail($id);
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal(): void
    {
        $this->isDetailModalOpen = false;
        $this->selectedVenta = null;
    }

    public function openAnularModal(int $id): void
    {
        $venta = Venta::findOrFail($id);

        if ($venta->estado === 'ANULADA') {
            $this->dispatch('swal:modal', [
                'type' => 'info',
                'title' => 'Venta ya Anulada',
                'text' => 'Esta venta ya se encuentra anulada.',
            ]);

            return;
        }

        $user = Auth::user();
        if (! $user->esAdmin() && $venta->usuario_id !== $user->id) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Acción denegada',
                'text' => 'No estás autorizado para anular ventas de otros usuarios.',
            ]);

            return;
        }

        $this->ventaIdToAnular = $venta->id;
        $this->motivoAnulacion = '';
        $this->resetValidation();
        $this->isAnularModalOpen = true;
    }

    public function closeAnularModal(): void
    {
        $this->isAnularModalOpen = false;
        $this->ventaIdToAnular = null;
        $this->motivoAnulacion = '';
        $this->resetValidation();
    }

    public function anularVenta(): void
    {
        $this->validate([
            'motivoAnulacion' => 'required|string|min:5|max:255',
        ], [
            'motivoAnulacion.required' => 'Debe ingresar el motivo de la anulación.',
            'motivoAnulacion.min' => 'El motivo debe tener al menos 5 caracteres.',
        ]);

        try {
            if (! $this->ventaIdToAnular) {
                return;
            }

            $user = Auth::user();
            $venta = Venta::with('detalles.producto')->findOrFail($this->ventaIdToAnular);

            if ($venta->estado === 'ANULADA') {
                $this->closeAnularModal();

                return;
            }

            if (! $user->esAdmin() && $venta->usuario_id !== $user->id) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No estás autorizado para anular esta venta.',
                ]);

                return;
            }

            DB::transaction(function () use ($venta) {
                // 1. Update Venta status
                $oldObs = $venta->observaciones ?? '';
                $newObs = $oldObs ? $oldObs.' | Anulada: '.trim($this->motivoAnulacion) : 'Anulada: '.trim($this->motivoAnulacion);

                $venta->update([
                    'estado' => 'ANULADA',
                    'observaciones' => $newObs,
                ]);

                // 2. Restore Stock & Record Movimientos
                foreach ($venta->detalles as $det) {
                    $ps = ProductoSucursal::firstOrCreate([
                        'producto_id' => $det->producto_id,
                        'sucursal_id' => $venta->sucursal_id,
                    ], [
                        'stock_actual' => 0.00,
                    ]);

                    $stockAnterior = (float) $ps->stock_actual;
                    $stockNuevo = $stockAnterior + (float) $det->cantidad;

                    $ps->update(['stock_actual' => $stockNuevo]);

                    MovimientoInventario::create([
                        'sucursal_id' => $venta->sucursal_id,
                        'producto_id' => $det->producto_id,
                        'tipo_movimiento' => 'DEVOLUCION_VENTA',
                        'cantidad' => $det->cantidad,
                        'precio_compra' => $det->producto->precio_compra ?? 0,
                        'precio_venta' => $det->precio_unitario,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockNuevo,
                        'motivo' => "Anulación / Devolución de venta N° {$venta->numero_recibo} - Motivo: ".trim($this->motivoAnulacion),
                        'referencia_tipo' => 'VENTA',
                        'referencia_id' => $venta->id,
                        'fecha_movimiento' => now(),
                    ]);
                }
            });

            $this->closeAnularModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => '¡Venta Anulada!',
                'text' => "La venta N° {$venta->numero_recibo} ha sido anulada y el stock ha sido devuelto al inventario.",
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

        $ventasQuery = Venta::with(['sucursal', 'usuario', 'cliente', 'detalles']);

        // Scoping Rule:
        // Admin sees all sales of their branch or filtered branch
        // Regular user sees ONLY their own sales
        if ($user->esAdmin()) {
            if ($this->sucursalFilter) {
                $ventasQuery->where('sucursal_id', $this->sucursalFilter);
            } else {
                $ventasQuery->where('sucursal_id', $sucursalId);
            }
        } else {
            $ventasQuery->where('usuario_id', $user->id)
                ->where('sucursal_id', $sucursalId);
        }

        // Date Range Filter
        if ($this->fechaInicio) {
            $ventasQuery->whereDate('fecha_venta', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $ventasQuery->whereDate('fecha_venta', '<=', $this->fechaFin);
        }

        // Status Filter
        if ($this->estadoFilter) {
            $ventasQuery->where('estado', $this->estadoFilter);
        }

        // Search Query (recibo number, client name, nit/ci)
        if ($this->search) {
            $ventasQuery->where(function ($q) {
                $q->where('numero_recibo', 'like', '%'.$this->search.'%')
                    ->orWhereHas('cliente', function ($c) {
                        $c->where('nombre_razon_social', 'like', '%'.$this->search.'%')
                            ->orWhere('cedula_nit_ruc', 'like', '%'.$this->search.'%');
                    });
            });
        }

        $ventas = $ventasQuery->latest('fecha_venta')->paginate($this->perPage);

        // Daily Summary Stats
        $totalVentasMonto = (float) (clone $ventasQuery)->where('estado', 'COMPLETADA')->sum('total');
        $totalVentasCantidad = (clone $ventasQuery)->where('estado', 'COMPLETADA')->count();

        $todasSucursales = $user->esAdmin() ? Sucursal::orderBy('nombre')->get() : collect();

        return view('livewire.operacion.ventas-dia', [
            'ventas' => $ventas,
            'todasSucursales' => $todasSucursales,
            'totalVentasMonto' => $totalVentasMonto,
            'totalVentasCantidad' => $totalVentasCantidad,
        ]);
    }
}
