<?php

namespace App\Livewire\Operacion;

use App\Models\Caja;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Pagos extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    #[Url(history: true)]
    public string $usuarioFilter = '';

    #[Url(history: true)]
    public string $tipoFilter = '';

    #[Url(history: true)]
    public string $fechaInicio = '';

    #[Url(history: true)]
    public string $fechaFin = '';

    // Form Modal for Extra Movements (Ingresos extras / Egresos / Gastos)
    public bool $isPagoModalOpen = false;

    public string $tipoMovimiento = 'EGRESO_GASTO'; // 'INGRESO_EXTRA' or 'EGRESO_GASTO'

    public ?float $monto = null;

    public string $metodoPago = 'EFECTIVO';

    public string $concepto = '';

    public string $referenciaTransaccion = '';

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

    public function updatingUsuarioFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTipoFilter(): void
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

    public function openPagoModal(string $tipo = 'EGRESO_GASTO'): void
    {
        $user = Auth::user();

        // Check if user has an active open cash register
        $activeCaja = Caja::where('usuario_id', $user->id)
            ->where('estado', 'ABIERTA')
            ->first();

        if (! $activeCaja) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Caja no Aperturada',
                'text' => 'Debes aperturar una caja activa antes de realizar egresos o ingresos extras.',
            ]);

            return;
        }

        $this->tipoMovimiento = in_array($tipo, ['INGRESO_EXTRA', 'EGRESO_GASTO']) ? $tipo : 'EGRESO_GASTO';
        $this->monto = null;
        $this->metodoPago = 'EFECTIVO';
        $this->concepto = '';
        $this->referenciaTransaccion = '';
        $this->resetValidation();
        $this->isPagoModalOpen = true;
    }

    public function closePagoModal(): void
    {
        $this->isPagoModalOpen = false;
        $this->monto = null;
        $this->concepto = '';
        $this->referenciaTransaccion = '';
        $this->resetValidation();
    }

    public function savePago(): void
    {
        $this->validate([
            'monto' => 'required|numeric|gt:0',
            'concepto' => 'required|string|min:3|max:255',
            'metodoPago' => 'required|in:EFECTIVO,QR,TRANSFERENCIA',
            'referenciaTransaccion' => 'nullable|string|max:100',
        ], [
            'monto.required' => 'El monto del movimiento es obligatorio.',
            'monto.gt' => 'El monto debe ser mayor a cero.',
            'concepto.required' => 'Debe ingresar el concepto o motivo del movimiento.',
            'concepto.min' => 'El concepto debe tener al menos 3 caracteres.',
        ]);

        try {
            $user = Auth::user();

            $activeCaja = Caja::where('usuario_id', $user->id)
                ->where('estado', 'ABIERTA')
                ->first();

            if (! $activeCaja) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Caja no Aperturada',
                    'text' => 'No posees una caja abierta activa.',
                ]);

                return;
            }

            // Negative for Outflows/Gastos, Positive for Extra Inflows
            $montoFinal = $this->tipoMovimiento === 'EGRESO_GASTO' ? -abs($this->monto) : abs($this->monto);
            $tag = $this->tipoMovimiento === 'EGRESO_GASTO' ? '[EGRESO_GASTO]' : '[INGRESO_EXTRA]';
            $refText = trim($tag.' '.trim($this->concepto).($this->referenciaTransaccion ? ' | Ref: '.trim($this->referenciaTransaccion) : ''));

            Pago::create([
                'venta_id' => null,
                'caja_id' => $activeCaja->id,
                'metodo_pago' => $this->metodoPago,
                'monto' => $montoFinal,
                'referencia_transaccion' => $refText,
                'fecha_pago' => now(),
            ]);

            $this->closePagoModal();

            $titulo = $this->tipoMovimiento === 'EGRESO_GASTO' ? '¡Egreso Registrado!' : '¡Ingreso Extra Registrado!';

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => $titulo,
                'text' => 'El movimiento de caja ha sido registrado exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function delete(int $id): void
    {
        try {
            $pago = Pago::findOrFail($id);
            $user = Auth::user();

            if (! $user->esAdmin() && $pago->usuario_creador_id !== $user->id) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No estás autorizado para eliminar registros de otros usuarios.',
                ]);

                return;
            }

            $pago->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El registro ha sido eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar el registro.',
            ]);
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Active Box Check
        $activeCaja = Caja::where('usuario_id', $user->id)
            ->where('estado', 'ABIERTA')
            ->first();

        $pagosQuery = Pago::with(['venta.cliente', 'caja', 'usuario']);

        // Scoping Rule:
        // Admin can filter by user or view all
        // Regular user can ONLY view their own created payments
        if ($user->esAdmin()) {
            if ($this->usuarioFilter) {
                $pagosQuery->where('usuario_creador_id', $this->usuarioFilter);
            }
        } else {
            $pagosQuery->where('usuario_creador_id', $user->id);
        }

        // Date Range Filter
        if ($this->fechaInicio) {
            $pagosQuery->whereDate('fecha_pago', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $pagosQuery->whereDate('fecha_pago', '<=', $this->fechaFin);
        }

        // Type Filter
        if ($this->tipoFilter === 'INGRESO_VENTA') {
            $pagosQuery->whereNotNull('venta_id');
        } elseif ($this->tipoFilter === 'INGRESO_EXTRA') {
            $pagosQuery->whereNull('venta_id')->where('monto', '>', 0);
        } elseif ($this->tipoFilter === 'EGRESO_GASTO') {
            $pagosQuery->whereNull('venta_id')->where('monto', '<', 0);
        }

        // Search Query
        if ($this->search) {
            $pagosQuery->where(function ($q) {
                $q->where('referencia_transaccion', 'like', '%'.$this->search.'%')
                    ->orWhere('metodo_pago', 'like', '%'.$this->search.'%')
                    ->orWhereHas('venta', function ($v) {
                        $v->where('numero_recibo', 'like', '%'.$this->search.'%');
                    })
                    ->orWhereHas('usuario', function ($u) {
                        $u->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    });
            });
        }

        $pagos = $pagosQuery->latest('fecha_pago')->paginate($this->perPage);

        // Calculate KPI Summaries for current view query
        $totalIngresosVentas = (float) (clone $pagosQuery)->whereNotNull('venta_id')->sum('monto');
        $totalIngresosExtras = (float) (clone $pagosQuery)->whereNull('venta_id')->where('monto', '>', 0)->sum('monto');
        $totalEgresosGastos = (float) (clone $pagosQuery)->whereNull('venta_id')->where('monto', '<', 0)->sum('monto');

        // Income Breakdown by Payment Method (Efectivo vs Digital)
        $totalIngresosEfectivo = (float) (clone $pagosQuery)->where('monto', '>', 0)->where('metodo_pago', 'EFECTIVO')->sum('monto');
        $totalIngresosDigital = (float) (clone $pagosQuery)->where('monto', '>', 0)->whereIn('metodo_pago', ['QR', 'TRANSFERENCIA'])->sum('monto');

        $todosUsuarios = $user->esAdmin() ? User::orderBy('name')->get() : collect();

        return view('livewire.operacion.pagos', [
            'pagos' => $pagos,
            'activeCaja' => $activeCaja,
            'todosUsuarios' => $todosUsuarios,
            'totalIngresosVentas' => $totalIngresosVentas,
            'totalIngresosExtras' => $totalIngresosExtras,
            'totalEgresosGastos' => abs($totalEgresosGastos),
            'totalIngresosEfectivo' => $totalIngresosEfectivo,
            'totalIngresosDigital' => $totalIngresosDigital,
        ]);
    }
}
