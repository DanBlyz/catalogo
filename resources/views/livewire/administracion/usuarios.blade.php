<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-brand-500"></i>
                    <span>Gestión de Usuarios</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Administración de personal, asignación de roles, sucursales y permisos
                </p>
            </div>
            
            <nav class="flex text-xs text-slate-400 font-medium">
                <a href="#" class="hover:text-brand-500 transition">Sistema</a>
                <span class="mx-2">&sol;</span>
                <span class="text-slate-700 dark:text-slate-200">Usuarios</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        
        <!-- Main Card Container -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Listado de Usuarios</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Total de registros: {{ $usuarios->total() }}</p>
                    </div>
                </div>

                <!-- Action Button: Nuevo Usuario -->
                <button 
                    wire:click="openModal" 
                    class="w-full sm:w-auto px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-xl shadow-md shadow-brand-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Nuevo Usuario</span>
                </button>
            </div>

            <!-- Card Body -->
            <div class="p-6 space-y-4">
                
                <!-- Controls Bar: Filters & Search -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-center">
                    
                    <!-- Records Per Page Select -->
                    <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                        <span>Mostrar</span>
                        <select 
                            wire:model.live="perPage" 
                            class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-1.5 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>filas</span>
                    </div>

                    <!-- Rol Filter Select -->
                    <div>
                        <select 
                            wire:model.live="rolFilter" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="">-- Todos los Roles --</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sucursal Filter Select -->
                    <div>
                        <select 
                            wire:model.live="sucursalFilter" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="">-- Todas las Sucursales --</option>
                            @foreach ($sucursales as $suc)
                                <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Buscar por nombre, CI, email..." 
                            class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                        >
                    </div>

                </div>

                <!-- Usuarios Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Usuario / Nombre</th>
                                <th class="py-3 px-4">Cédula</th>
                                <th class="py-3 px-4">Rol</th>
                                <th class="py-3 px-4">Sucursal</th>
                                <th class="py-3 px-4">Email / Teléfono</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-center w-36">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($usuarios as $index => $user)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono font-medium">
                                        {{ $usuarios->firstItem() + $index }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-900 dark:text-white">
                                            {{ $user->nombre_completo }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-300">
                                        {{ $user->cedula ?? 'Sin CI' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            <i class="fa-solid fa-user-shield text-[9px]"></i> {{ $user->rol?->nombre ?? 'Sin Rol' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                            <i class="fa-solid fa-store text-[9px]"></i> {{ $user->sucursal?->nombre ?? 'Sin Sucursal' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                        <div>{{ $user->email }}</div>
                                        @if ($user->telefono)
                                            <div class="text-[10px] text-slate-400"><i class="fa-solid fa-phone text-[9px]"></i> {{ $user->telefono }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($user->estado)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-500 dark:text-rose-400 border border-rose-500/20">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-1">
                                            
                                            <!-- Permisos Special Access Button -->
                                            <button 
                                                wire:click="openPermissionsModal({{ $user->id }})" 
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 transition"
                                                title="Asignar / Gestionar Permisos">
                                                <i class="fa-solid fa-key text-sm"></i>
                                            </button>

                                            <!-- Editar Button -->
                                            <button 
                                                wire:click="edit({{ $user->id }})" 
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/50 transition"
                                                title="Editar Usuario">
                                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                                            </button>

                                            <!-- Eliminar Button -->
                                            @if ($user->id !== 1 && $user->id !== auth()->id())
                                                <button 
                                                    type="button"
                                                    onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->nombre_completo) }}')" 
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition"
                                                    title="Eliminar Usuario">
                                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="fa-solid fa-users-slash text-3xl text-slate-300 dark:text-slate-700"></i>
                                            <p class="text-xs font-medium">No se encontraron usuarios registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="pt-2">
                    {{ $usuarios->links() }}
                </div>

            </div>
        </div>

    </div>

    <!-- Modal Form (Nuevo / Editar Usuario) -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            <!-- Modal Content Card -->
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid {{ $userId ? 'fa-pen-to-square' : 'fa-plus' }}"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $userId ? 'Editar Usuario' : 'Nuevo Usuario' }}
                            </h3>
                        </div>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body Form Grid -->
                    <form wire:submit.prevent="save" class="space-y-4">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            
                            <!-- Nombres / Nombre principal -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Nombres del Usuario <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="name" 
                                    placeholder="Ej: Juan Carlos" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('name') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Apellido Paterno -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Apellido Paterno
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="apellido_paterno" 
                                    placeholder="Ej: Pérez" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('apellido_paterno') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Apellido Materno -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Apellido Materno
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="apellido_materno" 
                                    placeholder="Ej: Gómez" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('apellido_materno') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Cédula de Identidad -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Cédula de Identidad (CI)
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="cedula" 
                                    placeholder="Ej: 1234567" 
                                    class="w-full px-3.5 py-2.5 text-xs font-mono bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('cedula') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Teléfono / Celular
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="telefono" 
                                    placeholder="Ej: 71234567" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('telefono') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Correo Electrónico -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Correo Electrónico <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    wire:model="email" 
                                    placeholder="Ej: usuario@tienda.com" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('email') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Contraseña -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Contraseña {!! $userId ? '<span class="text-slate-400 font-normal">(opcional)</span>' : '<span class="text-rose-500">*</span>' !!}
                                </label>
                                <input 
                                    type="password" 
                                    wire:model="password" 
                                    placeholder="{{ $userId ? 'Dejar en blanco para mantener actual' : 'Mínimo 8 caracteres' }}" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('password') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Rol -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Rol de Usuario <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model="rol_id" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                                    <option value="">-- Seleccionar Rol --</option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('rol_id') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Sucursal Asignada -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Sucursal Asignada <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model="sucursal_id" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                                    <option value="">-- Seleccionar Sucursal --</option>
                                    @foreach ($sucursales as $suc)
                                        <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('sucursal_id') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Dirección de Domicilio
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="direccion" 
                                    placeholder="Ej: Av. América #1234, Z. Queru Queru" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('direccion') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Estado Checkbox -->
                            <div class="sm:col-span-2 pt-1">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:model="estado" 
                                        class="rounded border-slate-300 dark:border-slate-700 text-brand-600 shadow-sm focus:ring-brand-500 dark:bg-slate-800 dark:checked:bg-brand-600 transition"
                                    >
                                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Usuario Habilitado / Activo</span>
                                </label>
                            </div>

                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="closeModal" 
                                class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-xl shadow-md transition active:scale-[0.98]">
                                {{ $userId ? 'Actualizar Usuario' : 'Guardar Usuario' }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endif

    <!-- Permissions Modal Component -->
    @if ($isPermissionsModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closePermissionsModal"></div>

            <!-- Modal Card -->
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-3xl transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all space-y-4">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    Asignar Permisos a: <span class="text-brand-500">{{ $targetUser?->nombre_completo }}</span>
                                </h3>
                                <p class="text-[11px] text-slate-400">Rol asignado: {{ $targetUser?->rol?->nombre }}</p>
                            </div>
                        </div>
                        <button wire:click="closePermissionsModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Search within permissions -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.200ms="permissionSearch" 
                            placeholder="Filtrar permisos..." 
                            class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                        >
                    </div>

                    <!-- Permissions Grid by Module -->
                    <div class="max-h-96 overflow-y-auto pr-2 space-y-4">
                        @forelse ($allPermisosGrouped as $modulo => $permisosGroup)
                            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-3 bg-slate-50/40 dark:bg-slate-800/30">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-brand-600 dark:text-brand-400 mb-2 flex items-center gap-1.5">
                                    <i class="fa-solid fa-folder-open text-[11px]"></i>
                                    <span>Módulo: {{ $modulo }}</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach ($permisosGroup as $permiso)
                                        <label class="flex items-start gap-2.5 p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80 hover:border-brand-500/50 cursor-pointer transition">
                                            <input 
                                                type="checkbox" 
                                                wire:model="userPermisos" 
                                                value="{{ $permiso->id }}"
                                                class="mt-0.5 rounded border-slate-300 dark:border-slate-700 text-brand-600 shadow-sm focus:ring-brand-500 dark:bg-slate-800 dark:checked:bg-brand-600 transition"
                                            >
                                            <div>
                                                <div class="text-xs font-semibold text-slate-900 dark:text-white">{{ $permiso->nombre }}</div>
                                                <div class="text-[10px] font-mono text-slate-400">{{ $permiso->codigo }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs">
                                No se encontraron permisos con el filtro aplicado.
                            </div>
                        @endforelse
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button 
                            type="button" 
                            wire:click="closePermissionsModal" 
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Cancelar
                        </button>
                        <button 
                            type="button" 
                            wire:click="savePermissions" 
                            class="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-xl shadow-md transition active:scale-[0.98]">
                            Guardar Permisos
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <!-- SweetAlert2 Listener -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal:modal', data => {
                const payload = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: payload.type || 'info',
                    title: payload.title || '',
                    text: payload.text || '',
                    confirmButtonColor: '#0275c5',
                    timer: payload.type === 'success' ? 2500 : null,
                    timerProgressBar: payload.type === 'success'
                });
            });
        });

        function confirmDeleteUser(id, name) {
            Swal.fire({
                title: '¿Eliminar Usuario?',
                text: `¿Estás seguro de eliminar al usuario "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', id);
                }
            });
        }
    </script>
</div>
