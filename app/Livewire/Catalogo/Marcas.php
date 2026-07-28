<?php

namespace App\Livewire\Catalogo;

use App\Models\Marca;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Marcas extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    public ?int $marcaId = null;

    public string $nombre = '';

    public string $descripcion = '';

    public bool $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100|unique:marcas,nombre,'.$this->marcaId,
            'descripcion' => 'nullable|string|max:255',
        ];
    }

    protected array $messages = [
        'nombre.required' => 'El nombre de la marca es obligatorio.',
        'nombre.unique' => 'Esta marca ya se encuentra registrada.',
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
        $this->marcaId = null;
        $this->nombre = '';
        $this->descripcion = '';
    }

    public function edit(int $id): void
    {
        $marca = Marca::findOrFail($id);
        $this->marcaId = $marca->id;
        $this->nombre = $marca->nombre ?? '';
        $this->descripcion = $marca->descripcion ?? '';

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->marcaId) {
                $marca = Marca::findOrFail($this->marcaId);
                $marca->update([
                    'nombre' => trim($this->nombre),
                    'descripcion' => $this->descripcion,
                ]);
                $message = 'Marca actualizada correctamente.';
            } else {
                Marca::create([
                    'nombre' => trim($this->nombre),
                    'descripcion' => $this->descripcion,
                ]);
                $message = 'Marca registrada correctamente.';
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
            $marca = Marca::findOrFail($id);

            if ($marca->productos()->exists()) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No se puede eliminar esta marca porque contiene productos asociados.',
                ]);

                return;
            }

            $marca->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminada',
                'text' => 'La marca ha sido eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar la marca.',
            ]);
        }
    }

    public function render()
    {
        $marcas = Marca::withCount('productos')
            ->where(function ($query) {
                $query->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('descripcion', 'like', '%'.$this->search.'%');
            })
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.catalogo.marcas', [
            'marcas' => $marcas,
        ]);
    }
}
