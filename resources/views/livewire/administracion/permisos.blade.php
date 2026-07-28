<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-key text-brand-500"></i>
                    <span>Gestión de Permisos</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Diccionario de permisos y acciones del sistema
                </p>
            </div>
            
            <nav class="flex text-xs text-slate-400 font-medium">
                <a href="#" class="hover:text-brand-500 transition">Sistema</a>
                <span class="mx-2">&sol;</span>
                <span class="text-slate-700 dark:text-slate-200">Permisos</span>
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
                        <i class="fa-solid fa-user-lock"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Listado de Permisos</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Total de registros: {{ $permisos->total() }}</p>
                    </div>
                </div>

                <!-- Action Button: Nuevo Permiso -->
                <button 
                    wire:click="openModal" 
                    class="w-full sm:w-auto px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-xl shadow-md shadow-brand-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Nuevo Permiso</span>
                </button>
            </div>

            <!-- Card Body -->
            <div class="p-6 space-y-4">
                
                <!-- Controls Bar: Per Page & Search -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <!-- Records Per Page Select -->
                    <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 w-full sm:w-auto">
                        <span>Mostrar</span>
                        <select 
                            wire:model.live="perPage" 
                            class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-1.5 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>registros</span>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Buscar permiso por nombre, código o módulo..." 
                            class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                        >
                    </div>
                </div>

                <!-- Permisos Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Nombre del Permiso</th>
                                <th class="py-3 px-4">Código / Clave</th>
                                <th class="py-3 px-4">Módulo</th>
                                <th class="py-3 px-4">Descripción</th>
                                <th class="py-3 px-4">Fecha Registro</th>
                                <th class="py-3 px-4 text-center w-28">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($permisos as $index => $permiso)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono font-medium">
                                        {{ $permisos->firstItem() + $index }}
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $permiso->nombre }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[11px] text-slate-600 dark:text-slate-300">
                                        {{ $permiso->codigo }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-500/10 text-brand-600 dark:text-brand-400 border border-brand-500/20">
                                            {{ $permiso->modulo ?? 'GENERAL' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                        {{ $permiso->descripcion ?? 'Sin descripción' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-400">
                                        {{ $permiso->created_at ? $permiso->created_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Editar Button -->
                                            <button 
                                                wire:click="edit({{ $permiso->id }})" 
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/50 transition"
                                                title="Editar Permiso">
                                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                                            </button>

                                            <!-- Eliminar Button -->
                                            <button 
                                                type="button"
                                                onclick="confirmDeletePermiso({{ $permiso->id }}, '{{ addslashes($permiso->nombre) }}')" 
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition"
                                                title="Eliminar Permiso">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="fa-solid fa-key text-3xl text-slate-300 dark:text-slate-700"></i>
                                            <p class="text-xs font-medium">No se encontraron permisos registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="pt-2">
                    {{ $permisos->links() }}
                </div>

            </div>
        </div>

    </div>

    <!-- Modal Form (Nuevo / Editar Permiso) -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            <!-- Modal Content Card -->
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid {{ $permisoId ? 'fa-pen-to-square' : 'fa-plus' }}"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $permisoId ? 'Editar Permiso' : 'Nuevo Permiso' }}
                            </h3>
                        </div>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body Form -->
                    <form wire:submit.prevent="save" class="space-y-4">
                        
                        <!-- Nombre del Permiso -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Nombre del Permiso <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="nombre" 
                                placeholder="Ej: Crear Productos, Anular Ventas" 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                            @error('nombre') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Código del Permiso -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Código / Clave <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="codigo" 
                                placeholder="Ej: productos.crear, ventas.anular" 
                                class="w-full px-3.5 py-2.5 text-xs font-mono bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                            @error('codigo') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Módulo -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Módulo Asignado <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="modulo" 
                                list="modulos-list"
                                placeholder="Ej: PRODUCTOS, VENTAS, CAJAS, USUARIOS" 
                                class="w-full px-3.5 py-2.5 text-xs uppercase bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                            <datalist id="modulos-list">
                                <option value="PRODUCTOS">
                                <option value="VENTAS">
                                <option value="CAJAS">
                                <option value="CLIENTES">
                                <option value="PROVEEDORES">
                                <option value="USUARIOS">
                                <option value="PERMISOS">
                                <option value="REPORTES">
                                <option value="SISTEMA">
                            </datalist>
                            @error('modulo') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Descripción
                            </label>
                            <textarea 
                                wire:model="descripcion" 
                                rows="3" 
                                placeholder="Breve descripción del alcance de este permiso..." 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            ></textarea>
                            @error('descripcion') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
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
                                {{ $permisoId ? 'Actualizar Permiso' : 'Guardar Permiso' }}
                            </button>
                        </div>

                    </form>

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

        function confirmDeletePermiso(id, name) {
            Swal.fire({
                title: '¿Eliminar Permiso?',
                text: `¿Estás seguro de eliminar el permiso "${name}"?`,
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
