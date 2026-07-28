<?php

namespace App\Livewire\Operacion;

use App\Models\Caja;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Cajas extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    #[Url(history: true)]
    public string $estadoFilter = '';

    // Apertura Form
    public bool $isAperturaModalOpen = false;

    public ?float $monto_apertura = 0.00;

    public string $observaciones_apertura = '';

    // Cierre Form
    public bool $isCierreModalOpen = false;

    public ?int $cajaIdToClose = null;

    public ?float $monto_cierre = 0.00;

    public string $observaciones_cierre = '';

    public ?Caja $cajaSelectedForClose = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingEstadoFilter(): void
    {
        $this->resetPage();
    }

    public function openAperturaModal(): void
    {
        $user = Auth::user();

        // Check if user already has an active open box
        $hasOpenBox = Caja::where('usuario_id', $user->id)
            ->where('estado', 'ABIERTA')
            ->exists();

        if ($hasOpenBox) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Caja Abierta Existente',
                'text' => 'Ya posees una caja abierta en esta sucursal. Debes realizar el cierre antes de aperturar una nueva.',
            ]);

            return;
        }

        $this->monto_apertura = 0.00;
        $this->observaciones_apertura = '';
        $this->resetValidation();
        $this->isAperturaModalOpen = true;
    }

    public function closeAperturaModal(): void
    {
        $this->isAperturaModalOpen = false;
        $this->monto_apertura = 0.00;
        $this->observaciones_apertura = '';
        $this->resetValidation();
    }

    public function saveApertura(): void
    {
        $this->validate([
            'monto_apertura' => 'required|numeric|min:0',
            'observaciones_apertura' => 'nullable|string|max:500',
        ], [
            'monto_apertura.required' => 'El monto inicial de apertura es obligatorio.',
            'monto_apertura.numeric' => 'El monto debe ser un número válido.',
            'monto_apertura.min' => 'El monto inicial no puede ser negativo.',
        ]);

        try {
            $user = Auth::user();
            $sucursalId = $user->sucursal_id ?? 1;

            // Re-verify open box constraint
            $hasOpenBox = Caja::where('usuario_id', $user->id)
                ->where('estado', 'ABIERTA')
                ->exists();

            if ($hasOpenBox) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'Ya posees una caja abierta activa.',
                ]);

                return;
            }

            Caja::create([
                'sucursal_id' => $sucursalId,
                'usuario_id' => $user->id,
                'monto_apertura' => $this->monto_apertura ?? 0.00,
                'monto_cierre' => 0.00,
                'ventas_efectivo' => 0.00,
                'ventas_digital' => 0.00,
                'total_esperado' => $this->monto_apertura ?? 0.00,
                'diferencia' => 0.00,
                'estado' => 'ABIERTA',
                'fecha_apertura' => now(),
                'observaciones' => $this->observaciones_apertura ? trim($this->observaciones_apertura) : null,
            ]);

            $this->closeAperturaModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => '¡Caja Aperturada!',
                'text' => 'La apertura de caja ha sido registrada correctamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function openCierreModal(int $id): void
    {
        $caja = Caja::with(['sucursal', 'usuario'])->findOrFail($id);

        if ($caja->estado === 'CERRADA') {
            $this->dispatch('swal:modal', [
                'type' => 'info',
                'title' => 'Caja ya cerrada',
                'text' => 'Esta caja ya se encuentra en estado CERRADA.',
            ]);

            return;
        }

        $this->cajaIdToClose = $caja->id;
        $this->cajaSelectedForClose = $caja;
        $this->monto_cierre = 0.00;
        $this->observaciones_cierre = '';
        $this->resetValidation();
        $this->isCierreModalOpen = true;
    }

    public function closeCierreModal(): void
    {
        $this->isCierreModalOpen = false;
        $this->cajaIdToClose = null;
        $this->cajaSelectedForClose = null;
        $this->monto_cierre = 0.00;
        $this->observaciones_cierre = '';
        $this->resetValidation();
    }

    public function saveCierre(): void
    {
        $this->validate([
            'monto_cierre' => 'required|numeric|min:0',
            'observaciones_cierre' => 'nullable|string|max:500',
        ], [
            'monto_cierre.required' => 'El monto del conteo de cierre es obligatorio.',
            'monto_cierre.numeric' => 'El monto debe ser un número válido.',
            'monto_cierre.min' => 'El monto de cierre no puede ser negativo.',
        ]);

        try {
            if (! $this->cajaIdToClose) {
                return;
            }

            $caja = Caja::findOrFail($this->cajaIdToClose);

            if ($caja->estado === 'CERRADA') {
                $this->closeCierreModal();

                return;
            }

            // Calculate sales summary
            $ventasEfectivo = (float) Venta::where('caja_id', $caja->id)
                ->where('metodo_pago', 'EFECTIVO')
                ->sum('total');

            $ventasDigital = (float) Venta::where('caja_id', $caja->id)
                ->where('metodo_pago', '!=', 'EFECTIVO')
                ->sum('total');

            $montoApertura = (float) $caja->monto_apertura;
            $totalEsperado = $montoApertura + $ventasEfectivo + $ventasDigital;
            $montoCierreActual = (float) $this->monto_cierre;
            $diferencia = $montoCierreActual - $totalEsperado;

            $obs = $caja->observaciones ?? '';
            if ($this->observaciones_cierre) {
                $obs = $obs ? $obs.' | Cierre: '.trim($this->observaciones_cierre) : trim($this->observaciones_cierre);
            }

            $caja->update([
                'monto_cierre' => $montoCierreActual,
                'ventas_efectivo' => $ventasEfectivo,
                'ventas_digital' => $ventasDigital,
                'total_esperado' => $totalEsperado,
                'diferencia' => $diferencia,
                'estado' => 'CERRADA',
                'fecha_cierre' => now(),
                'observaciones' => $obs,
            ]);

            $this->closeCierreModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => '¡Arqueo y Cierre Exitoso!',
                'text' => 'La caja ha sido cerrada correctamente. Diferencia: Bs '.number_format($diferencia, 2),
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
            $caja = Caja::findOrFail($id);

            // Scoping check for non-admin
            if (! Auth::user()->esAdmin() && $caja->usuario_id !== Auth::id()) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No tienes permisos para eliminar este registro de caja.',
                ]);

                return;
            }

            $caja->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminada',
                'text' => 'El registro de caja ha sido eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar el registro de caja.',
            ]);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $sucursalId = $user->sucursal_id ?? 1;

        $cajasQuery = Caja::with(['sucursal', 'usuario']);

        // Scoping Rule:
        // Admin sees all cash registers of their assigned branch
        // Other users see ONLY their own cash registers
        if ($user->esAdmin()) {
            $cajasQuery->where('sucursal_id', $sucursalId);
        } else {
            $cajasQuery->where('usuario_id', $user->id);
        }

        // Apply Status Filter
        if ($this->estadoFilter) {
            $cajasQuery->where('estado', $this->estadoFilter);
        }

        // Apply Search Filter
        if ($this->search) {
            $cajasQuery->where(function ($q) {
                $q->whereHas('usuario', function ($u) {
                    $u->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('nombres', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })
                    ->orWhere('observaciones', 'like', '%'.$this->search.'%');
            });
        }

        $cajas = $cajasQuery->latest('id')->paginate($this->perPage);

        // Active box status for current user
        $userActiveBox = Caja::where('usuario_id', $user->id)
            ->where('estado', 'ABIERTA')
            ->first();

        return view('livewire.operacion.cajas', [
            'cajas' => $cajas,
            'userActiveBox' => $userActiveBox,
        ]);
    }
}
