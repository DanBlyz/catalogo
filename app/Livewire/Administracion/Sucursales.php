<?php

namespace App\Livewire\Administracion;

use App\Models\Sucursal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Sucursales extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    public ?int $sucursalId = null;

    public string $nombre = '';

    public string $direccion = '';

    public string $telefono = '';

    public bool $es_principal = false;

    public bool $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'es_principal' => 'boolean',
        ];
    }

    protected array $messages = [
        'nombre.required' => 'El nombre de la sucursal es obligatorio.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function openModal(): void
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields(): void
    {
        $this->sucursalId = null;
        $this->nombre = '';
        $this->direccion = '';
        $this->telefono = '';
        $this->es_principal = false;
    }

    public function edit(int $id): void
    {
        $sucursal = Sucursal::findOrFail($id);
        $this->sucursalId = $sucursal->id;
        $this->nombre = $sucursal->nombre ?? '';
        $this->direccion = $sucursal->direccion ?? '';
        $this->telefono = $sucursal->telefono ?? '';
        $this->es_principal = (bool) $sucursal->es_principal;

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->es_principal) {
                Sucursal::where('es_principal', true)
                    ->when($this->sucursalId, fn ($q) => $q->where('id', '!=', $this->sucursalId))
                    ->update(['es_principal' => false]);
            }

            if ($this->sucursalId) {
                $sucursal = Sucursal::findOrFail($this->sucursalId);
                $sucursal->update([
                    'nombre' => $this->nombre,
                    'direccion' => $this->direccion,
                    'telefono' => $this->telefono,
                    'es_principal' => $this->es_principal,
                ]);
                $message = 'Sucursal actualizada correctamente.';
            } else {
                Sucursal::create([
                    'nombre' => $this->nombre,
                    'direccion' => $this->direccion,
                    'telefono' => $this->telefono,
                    'es_principal' => $this->es_principal,
                ]);
                $message = 'Sucursal registrada correctamente.';
            }

            $this->closeModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => $message,
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
            $sucursal = Sucursal::findOrFail($id);

            if ($sucursal->id === 1 || $sucursal->es_principal) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'La sucursal principal no puede ser eliminada.',
                ]);

                return;
            }

            $sucursal->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminada',
                'text' => 'La sucursal ha sido eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar la sucursal.',
            ]);
        }
    }

    public function render()
    {
        $sucursales = Sucursal::where(function ($query) {
            $query->where('nombre', 'like', '%'.$this->search.'%')
                ->orWhere('direccion', 'like', '%'.$this->search.'%')
                ->orWhere('telefono', 'like', '%'.$this->search.'%');
        })
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.administracion.sucursales', [
            'sucursales' => $sucursales,
        ]);
    }
}
