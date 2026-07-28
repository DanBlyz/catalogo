<?php

namespace App\Livewire\Administracion;

use App\Models\Permiso;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Permisos extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    public ?int $permisoId = null;

    public string $nombre = '';

    public string $codigo = '';

    public string $modulo = 'GENERAL';

    public string $descripcion = '';

    public bool $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:100|unique:permisos,codigo,'.$this->permisoId,
            'modulo' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
        ];
    }

    protected array $messages = [
        'nombre.required' => 'El nombre del permiso es obligatorio.',
        'codigo.required' => 'El código del permiso es obligatorio.',
        'codigo.unique' => 'Este código de permiso ya está registrado.',
        'modulo.required' => 'El módulo asignado es obligatorio.',
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
        $this->permisoId = null;
        $this->nombre = '';
        $this->codigo = '';
        $this->modulo = 'GENERAL';
        $this->descripcion = '';
    }

    public function edit(int $id): void
    {
        $permiso = Permiso::findOrFail($id);
        $this->permisoId = $permiso->id;
        $this->nombre = $permiso->nombre ?? '';
        $this->codigo = $permiso->codigo ?? '';
        $this->modulo = $permiso->modulo ?? 'GENERAL';
        $this->descripcion = $permiso->descripcion ?? '';

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->permisoId) {
                $permiso = Permiso::findOrFail($this->permisoId);
                $permiso->update([
                    'nombre' => $this->nombre,
                    'codigo' => strtolower(trim($this->codigo)),
                    'modulo' => strtoupper(trim($this->modulo)),
                    'descripcion' => $this->descripcion,
                ]);
                $message = 'Permiso actualizado correctamente.';
            } else {
                Permiso::create([
                    'nombre' => $this->nombre,
                    'codigo' => strtolower(trim($this->codigo)),
                    'modulo' => strtoupper(trim($this->modulo)),
                    'descripcion' => $this->descripcion,
                ]);
                $message = 'Permiso registrado correctamente.';
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
            $permiso = Permiso::findOrFail($id);
            $permiso->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El permiso ha sido eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar el permiso.',
            ]);
        }
    }

    public function render()
    {
        $permisos = Permiso::where(function ($query) {
            $query->where('nombre', 'like', '%'.$this->search.'%')
                ->orWhere('codigo', 'like', '%'.$this->search.'%')
                ->orWhere('modulo', 'like', '%'.$this->search.'%')
                ->orWhere('descripcion', 'like', '%'.$this->search.'%');
        })
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.administracion.permisos', [
            'permisos' => $permisos,
        ]);
    }
}
