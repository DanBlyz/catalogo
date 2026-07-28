<?php

namespace App\Livewire\Catalogo;

use App\Models\Categoria;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Categorias extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    public ?int $categoriaId = null;

    public string $nombre = '';

    public string $descripcion = '';

    public bool $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100|unique:categorias,nombre,'.$this->categoriaId,
            'descripcion' => 'nullable|string|max:255',
        ];
    }

    protected array $messages = [
        'nombre.required' => 'El nombre de la categoría es obligatorio.',
        'nombre.unique' => 'Esta categoría ya se encuentra registrada.',
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
        $this->categoriaId = null;
        $this->nombre = '';
        $this->descripcion = '';
    }

    public function edit(int $id): void
    {
        $categoria = Categoria::findOrFail($id);
        $this->categoriaId = $categoria->id;
        $this->nombre = $categoria->nombre ?? '';
        $this->descripcion = $categoria->descripcion ?? '';

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->categoriaId) {
                $categoria = Categoria::findOrFail($this->categoriaId);
                $categoria->update([
                    'nombre' => trim($this->nombre),
                    'descripcion' => $this->descripcion,
                ]);
                $message = 'Categoría actualizada correctamente.';
            } else {
                Categoria::create([
                    'nombre' => trim($this->nombre),
                    'descripcion' => $this->descripcion,
                ]);
                $message = 'Categoría registrada correctamente.';
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
            $categoria = Categoria::findOrFail($id);

            if ($categoria->productos()->exists()) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No se puede eliminar esta categoría porque contiene productos asociados.',
                ]);

                return;
            }

            $categoria->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminada',
                'text' => 'La categoría ha sido eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar la categoría.',
            ]);
        }
    }

    public function render()
    {
        $categorias = Categoria::withCount('productos')
            ->where(function ($query) {
                $query->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('descripcion', 'like', '%'.$this->search.'%');
            })
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.catalogo.categorias', [
            'categorias' => $categorias,
        ]);
    }
}
