<?php

namespace App\Livewire\Persona;

use App\Models\Cliente;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Clientes extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    public ?int $clienteId = null;

    public string $nombre_razon_social = '';

    public string $cedula_nit_ruc = '';

    public string $telefono = '';

    public string $email = '';

    public string $direccion = '';

    public bool $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'nombre_razon_social' => 'required|string|max:150',
            'cedula_nit_ruc' => 'nullable|string|max:50|unique:clientes,cedula_nit_ruc,'.$this->clienteId,
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ];
    }

    protected array $messages = [
        'nombre_razon_social.required' => 'El nombre o razón social es obligatorio.',
        'cedula_nit_ruc.unique' => 'Este número de Cédula/NIT/RUC ya se encuentra registrado.',
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
        $this->clienteId = null;
        $this->nombre_razon_social = '';
        $this->cedula_nit_ruc = '';
        $this->telefono = '';
        $this->email = '';
        $this->direccion = '';
    }

    public function edit(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $this->clienteId = $cliente->id;
        $this->nombre_razon_social = $cliente->nombre_razon_social ?? '';
        $this->cedula_nit_ruc = $cliente->cedula_nit_ruc ?? '';
        $this->telefono = $cliente->telefono ?? '';
        $this->email = $cliente->email ?? '';
        $this->direccion = $cliente->direccion ?? '';

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'nombre_razon_social' => trim($this->nombre_razon_social),
                'cedula_nit_ruc' => $this->cedula_nit_ruc ? trim($this->cedula_nit_ruc) : null,
                'telefono' => $this->telefono ? trim($this->telefono) : null,
                'email' => $this->email ? trim($this->email) : null,
                'direccion' => $this->direccion ? trim($this->direccion) : null,
            ];

            if ($this->clienteId) {
                $cliente = Cliente::findOrFail($this->clienteId);
                $cliente->update($data);
                $message = 'Cliente actualizado correctamente.';
            } else {
                Cliente::create($data);
                $message = 'Cliente registrado correctamente.';
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
            $cliente = Cliente::findOrFail($id);

            if ($cliente->ventas()->exists()) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No se puede eliminar el cliente porque tiene ventas asociadas.',
                ]);

                return;
            }

            $cliente->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El cliente ha sido eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar el cliente.',
            ]);
        }
    }

    public function render()
    {
        $clientes = Cliente::where(function ($query) {
            $query->where('nombre_razon_social', 'like', '%'.$this->search.'%')
                ->orWhere('cedula_nit_ruc', 'like', '%'.$this->search.'%')
                ->orWhere('telefono', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->orWhere('direccion', 'like', '%'.$this->search.'%');
        })
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.persona.clientes', [
            'clientes' => $clientes,
        ]);
    }
}
