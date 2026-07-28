<?php

namespace App\Livewire\Persona;

use App\Models\Proveedor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Proveedores extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    public ?int $proveedorId = null;

    public string $nombre_empresa = '';

    public string $contacto_nombre = '';

    public string $nit_ruc = '';

    public string $telefono = '';

    public string $email = '';

    public string $direccion = '';

    public bool $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'nombre_empresa' => 'required|string|max:150',
            'contacto_nombre' => 'nullable|string|max:100',
            'nit_ruc' => 'nullable|string|max:50|unique:proveedores,nit_ruc,'.$this->proveedorId,
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ];
    }

    protected array $messages = [
        'nombre_empresa.required' => 'El nombre de la empresa es obligatorio.',
        'nit_ruc.unique' => 'Este número de NIT/RUC ya se encuentra registrado.',
        'email.email' => 'Ingrese un correo electrónico válido.',
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
        $this->proveedorId = null;
        $this->nombre_empresa = '';
        $this->contacto_nombre = '';
        $this->nit_ruc = '';
        $this->telefono = '';
        $this->email = '';
        $this->direccion = '';
    }

    public function edit(int $id): void
    {
        $proveedor = Proveedor::findOrFail($id);
        $this->proveedorId = $proveedor->id;
        $this->nombre_empresa = $proveedor->nombre_empresa ?? '';
        $this->contacto_nombre = $proveedor->contacto_nombre ?? '';
        $this->nit_ruc = $proveedor->nit_ruc ?? '';
        $this->telefono = $proveedor->telefono ?? '';
        $this->email = $proveedor->email ?? '';
        $this->direccion = $proveedor->direccion ?? '';

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'nombre_empresa' => trim($this->nombre_empresa),
                'contacto_nombre' => $this->contacto_nombre ? trim($this->contacto_nombre) : null,
                'nit_ruc' => $this->nit_ruc ? trim($this->nit_ruc) : null,
                'telefono' => $this->telefono ? trim($this->telefono) : null,
                'email' => $this->email ? trim($this->email) : null,
                'direccion' => $this->direccion ? trim($this->direccion) : null,
            ];

            if ($this->proveedorId) {
                $proveedor = Proveedor::findOrFail($this->proveedorId);
                $proveedor->update($data);
                $message = 'Proveedor actualizado correctamente.';
            } else {
                Proveedor::create($data);
                $message = 'Proveedor registrado correctamente.';
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
            $proveedor = Proveedor::findOrFail($id);

            if ($proveedor->productos()->exists()) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No se puede eliminar el proveedor porque tiene productos asociados.',
                ]);

                return;
            }

            $proveedor->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El proveedor ha sido eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar el proveedor.',
            ]);
        }
    }

    public function render()
    {
        $proveedores = Proveedor::where(function ($query) {
            $query->where('nombre_empresa', 'like', '%'.$this->search.'%')
                ->orWhere('contacto_nombre', 'like', '%'.$this->search.'%')
                ->orWhere('nit_ruc', 'like', '%'.$this->search.'%')
                ->orWhere('telefono', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->orWhere('direccion', 'like', '%'.$this->search.'%');
        })
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.persona.proveedores', [
            'proveedores' => $proveedores,
        ]);
    }
}
