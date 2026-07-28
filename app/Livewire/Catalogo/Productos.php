<?php

namespace App\Livewire\Catalogo;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Productos extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    #[Url(history: true)]
    public string $categoriaFilter = '';

    #[Url(history: true)]
    public string $marcaFilter = '';

    public ?int $productoId = null;

    public string $nombre = '';

    public string $sku = '';

    public string $codigo_barras = '';

    public string $descripcion = '';

    public ?float $precio_compra = 0.00;

    public ?float $precio_venta = 0.00;

    public ?float $stock_minimo = 5.00;

    public string $unidad_medida = 'UNIDAD';

    public ?int $categoria_id = null;

    public ?int $marca_id = null;

    public bool $estado = true;

    public bool $isModalOpen = false;

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'sku' => 'required|string|max:100|unique:productos,sku,'.$this->productoId,
            'codigo_barras' => 'nullable|string|max:100|unique:productos,codigo_barras,'.$this->productoId,
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'nullable|numeric|min:0',
            'unidad_medida' => 'nullable|string|max:30',
            'categoria_id' => 'nullable|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'boolean',
        ];
    }

    protected array $messages = [
        'nombre.required' => 'El nombre del producto es obligatorio.',
        'sku.required' => 'El código SKU es obligatorio.',
        'sku.unique' => 'Este código SKU ya está registrado.',
        'codigo_barras.unique' => 'Este código de barras ya está registrado.',
        'precio_venta.required' => 'El precio de venta es obligatorio.',
        'precio_venta.numeric' => 'El precio de venta debe ser un número válido.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingCategoriaFilter(): void
    {
        $this->resetPage();
    }

    public function updatingMarcaFilter(): void
    {
        $this->resetPage();
    }

    public function openModal(): void
    {
        $this->resetInputFields();
        $this->generateDefaultSku();
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
        $this->productoId = null;
        $this->nombre = '';
        $this->sku = '';
        $this->codigo_barras = '';
        $this->descripcion = '';
        $this->precio_compra = 0.00;
        $this->precio_venta = 0.00;
        $this->stock_minimo = 5.00;
        $this->unidad_medida = 'UNIDAD';
        $this->categoria_id = null;
        $this->marca_id = null;
        $this->estado = true;
    }

    public function generateDefaultSku(): void
    {
        if (empty($this->sku) && ! $this->productoId) {
            $this->sku = 'PROD-'.strtoupper(substr(uniqid(), -6));
        }
    }

    public function edit(int $id): void
    {
        $producto = Producto::findOrFail($id);
        $this->productoId = $producto->id;
        $this->nombre = $producto->nombre ?? '';
        $this->sku = $producto->sku ?? '';
        $this->codigo_barras = $producto->codigo_barras ?? '';
        $this->descripcion = $producto->descripcion ?? '';
        $this->precio_compra = (float) $producto->precio_compra;
        $this->precio_venta = (float) $producto->precio_venta;
        $this->stock_minimo = (float) $producto->stock_minimo;
        $this->unidad_medida = $producto->unidad_medida ?? 'UNIDAD';
        $this->categoria_id = $producto->categoria_id;
        $this->marca_id = $producto->marca_id;
        $this->estado = (bool) $producto->estado;

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'nombre' => trim($this->nombre),
                'sku' => strtoupper(trim($this->sku)),
                'codigo_barras' => $this->codigo_barras ? trim($this->codigo_barras) : null,
                'descripcion' => $this->descripcion ? trim($this->descripcion) : null,
                'precio_compra' => $this->precio_compra ?? 0.00,
                'precio_venta' => $this->precio_venta ?? 0.00,
                'stock_minimo' => $this->stock_minimo ?? 0.00,
                'unidad_medida' => $this->unidad_medida ? strtoupper(trim($this->unidad_medida)) : 'UNIDAD',
                'categoria_id' => $this->categoria_id ?: null,
                'marca_id' => $this->marca_id ?: null,
                'estado' => $this->estado,
            ];

            if ($this->productoId) {
                $producto = Producto::findOrFail($this->productoId);
                $producto->update($data);
                $message = 'Producto actualizado correctamente.';
            } else {
                Producto::create($data);
                $message = 'Producto registrado correctamente.';
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
            $producto = Producto::findOrFail($id);

            if ($producto->detallesVentas()->exists()) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No se puede eliminar el producto porque tiene ventas registradas.',
                ]);

                return;
            }

            $producto->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El producto ha sido eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar el producto.',
            ]);
        }
    }

    public function render()
    {
        $productos = Producto::with(['categoria', 'marca', 'productoSucursales'])
            ->when($this->categoriaFilter, fn ($q) => $q->where('categoria_id', $this->categoriaFilter))
            ->when($this->marcaFilter, fn ($q) => $q->where('marca_id', $this->marcaFilter))
            ->where(function ($query) {
                $query->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%')
                    ->orWhere('codigo_barras', 'like', '%'.$this->search.'%')
                    ->orWhere('descripcion', 'like', '%'.$this->search.'%');
            })
            ->latest('id')
            ->paginate($this->perPage);

        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();

        return view('livewire.catalogo.productos', [
            'productos' => $productos,
            'categorias' => $categorias,
            'marcas' => $marcas,
        ]);
    }
}
