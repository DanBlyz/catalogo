<?php

namespace App\Livewire\Administracion;

use App\Models\Rol;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Roles extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    public ?int $roleId = null;

    public string $nombre = '';

    public string $codigo = '';

    public string $descripcion = '';

    public bool $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:50|unique:roles,codigo,'.$this->roleId,
            'descripcion' => 'nullable|string|max:255',
        ];
    }

    protected array $messages = [
        'nombre.required' => 'El nombre del rol es obligatorio.',
        'codigo.required' => 'El código del rol es obligatorio.',
        'codigo.unique' => 'Este código de rol ya está registrado.',
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
        $this->roleId = null;
        $this->nombre = '';
        $this->codigo = '';
        $this->descripcion = '';
    }

    public function edit(int $id): void
    {
        $rol = Rol::findOrFail($id);
        $this->roleId = $rol->id;
        $this->nombre = $rol->nombre ?? '';
        $this->codigo = $rol->codigo ?? '';
        $this->descripcion = $rol->descripcion ?? '';

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->roleId) {
                $rol = Rol::findOrFail($this->roleId);
                $rol->update([
                    'nombre' => $this->nombre,
                    'codigo' => strtoupper(trim($this->codigo)),
                    'descripcion' => $this->descripcion,
                ]);
                $message = 'Rol actualizado correctamente.';
            } else {
                Rol::create([
                    'nombre' => $this->nombre,
                    'codigo' => strtoupper(trim($this->codigo)),
                    'descripcion' => $this->descripcion,
                ]);
                $message = 'Rol registrado correctamente.';
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
            $rol = Rol::findOrFail($id);

            // Prevent deleting the primary admin role
            if ($rol->id === 1 || $rol->codigo === 'ADMIN') {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'El rol de Administrador principal no puede ser eliminado.',
                ]);

                return;
            }

            $rol->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El rol ha sido eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar el rol.',
            ]);
        }
    }

    public function render()
    {
        $roles = Rol::where(function ($query) {
            $query->where('nombre', 'like', '%'.$this->search.'%')
                ->orWhere('codigo', 'like', '%'.$this->search.'%')
                ->orWhere('descripcion', 'like', '%'.$this->search.'%');
        })
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.administracion.roles', [
            'roles' => $roles,
        ]);
    }
}
