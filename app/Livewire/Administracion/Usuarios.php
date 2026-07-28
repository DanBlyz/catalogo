<?php

namespace App\Livewire\Administracion;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Usuarios extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public int $perPage = 10;

    #[Url(history: true)]
    public string $rolFilter = '';

    #[Url(history: true)]
    public string $sucursalFilter = '';

    // Form fields
    public ?int $userId = null;

    public string $name = '';

    public string $apellido_paterno = '';

    public string $apellido_materno = '';

    public string $cedula = '';

    public string $telefono = '';

    public string $email = '';

    public string $password = '';

    public string $direccion = '';

    public ?int $rol_id = null;

    public ?int $sucursal_id = null;

    public bool $estado = true;

    public bool $isModalOpen = false;

    // Permissions modal fields
    public bool $isPermissionsModalOpen = false;

    public ?int $permissionUserId = null;

    public array $userPermisos = [];

    public string $permissionSearch = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'cedula' => 'nullable|string|max:50|unique:users,cedula,'.$this->userId,
            'email' => 'required|email|max:150|unique:users,email,'.$this->userId,
            'password' => $this->userId ? 'nullable|string|min:8' : 'required|string|min:8',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'rol_id' => 'required|exists:roles,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'estado' => 'boolean',
        ];
    }

    protected array $messages = [
        'name.required' => 'El nombre del usuario es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        'password.required' => 'La contraseña es obligatoria para nuevos usuarios.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'rol_id.required' => 'Debe seleccionar un rol para el usuario.',
        'sucursal_id.required' => 'Debe asignar una sucursal al usuario.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingRolFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSucursalFilter(): void
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
        $this->userId = null;
        $this->name = '';
        $this->apellido_paterno = '';
        $this->apellido_materno = '';
        $this->cedula = '';
        $this->telefono = '';
        $this->email = '';
        $this->password = '';
        $this->direccion = '';
        $this->rol_id = null;
        $this->sucursal_id = null;
        $this->estado = true;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name ?? $user->nombres ?? '';
        $this->apellido_paterno = $user->apellido_paterno ?? '';
        $this->apellido_materno = $user->apellido_materno ?? '';
        $this->cedula = $user->cedula ?? '';
        $this->telefono = $user->telefono ?? '';
        $this->email = $user->email ?? '';
        $this->password = '';
        $this->direccion = $user->direccion ?? '';
        $this->rol_id = $user->rol_id;
        $this->sucursal_id = $user->sucursal_id;
        $this->estado = (bool) $user->estado;

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'name' => trim($this->name),
                'nombres' => trim($this->name),
                'apellido_paterno' => $this->apellido_paterno ? trim($this->apellido_paterno) : null,
                'apellido_materno' => $this->apellido_materno ? trim($this->apellido_materno) : null,
                'cedula' => $this->cedula ? trim($this->cedula) : null,
                'telefono' => $this->telefono ? trim($this->telefono) : null,
                'email' => strtolower(trim($this->email)),
                'direccion' => $this->direccion ? trim($this->direccion) : null,
                'rol_id' => $this->rol_id,
                'sucursal_id' => $this->sucursal_id,
                'estado' => $this->estado,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            if ($this->userId) {
                $user = User::findOrFail($this->userId);
                $user->update($data);
                $message = 'Usuario actualizado correctamente.';
            } else {
                User::create($data);
                $message = 'Usuario registrado correctamente.';
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
            $user = User::findOrFail($id);

            if ($user->id === Auth::id()) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'No puedes eliminar tu propia cuenta de usuario.',
                ]);

                return;
            }

            if ($user->id === 1) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Acción denegada',
                    'text' => 'El usuario Administrador principal no puede ser eliminado.',
                ]);

                return;
            }

            $user->delete();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El usuario ha sido eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => 'No se pudo eliminar el usuario.',
            ]);
        }
    }

    public function openPermissionsModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->permissionUserId = $user->id;
        $this->userPermisos = $user->permisos()->pluck('permisos.id')->toArray();
        $this->permissionSearch = '';
        $this->isPermissionsModalOpen = true;
    }

    public function closePermissionsModal(): void
    {
        $this->isPermissionsModalOpen = false;
        $this->permissionUserId = null;
        $this->userPermisos = [];
    }

    public function savePermissions(): void
    {
        try {
            if (! $this->permissionUserId) {
                return;
            }

            $user = User::findOrFail($this->permissionUserId);
            $user->permisos()->sync($this->userPermisos);

            $this->closePermissionsModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => '¡Permisos Actualizados!',
                'text' => 'Los permisos especiales del usuario han sido asignados correctamente.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Ocurrió un error',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $usuarios = User::with(['rol', 'sucursal', 'permisos'])
            ->when($this->rolFilter, fn ($q) => $q->where('rol_id', $this->rolFilter))
            ->when($this->sucursalFilter, fn ($q) => $q->where('sucursal_id', $this->sucursalFilter))
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('nombres', 'like', '%'.$this->search.'%')
                    ->orWhere('apellido_paterno', 'like', '%'.$this->search.'%')
                    ->orWhere('apellido_materno', 'like', '%'.$this->search.'%')
                    ->orWhere('cedula', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('telefono', 'like', '%'.$this->search.'%');
            })
            ->latest('id')
            ->paginate($this->perPage);

        $roles = Rol::orderBy('nombre')->get();
        $sucursales = Sucursal::orderBy('nombre')->get();

        $allPermisosGrouped = [];
        if ($this->isPermissionsModalOpen) {
            $permisosQuery = Permiso::query();
            if ($this->permissionSearch) {
                $permisosQuery->where(function ($q) {
                    $q->where('nombre', 'like', '%'.$this->permissionSearch.'%')
                        ->orWhere('codigo', 'like', '%'.$this->permissionSearch.'%')
                        ->orWhere('modulo', 'like', '%'.$this->permissionSearch.'%');
                });
            }
            $allPermisosGrouped = $permisosQuery->orderBy('modulo')->orderBy('nombre')->get()->groupBy('modulo');
        }

        return view('livewire.administracion.usuarios', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'sucursales' => $sucursales,
            'allPermisosGrouped' => $allPermisosGrouped,
            'targetUser' => $this->permissionUserId ? User::find($this->permissionUserId) : null,
        ]);
    }
}
