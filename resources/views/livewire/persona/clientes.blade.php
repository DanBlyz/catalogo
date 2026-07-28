<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-users text-brand-500"></i>
                    <span>Gestión de Clientes</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Directorio y registro de compradores del sistema
                </p>
            </div>
            
            <nav class="flex text-xs text-slate-400 font-medium">
                <a href="#" class="hover:text-brand-500 transition">Personas</a>
                <span class="mx-2">&sol;</span>
                <span class="text-slate-700 dark:text-slate-200">Clientes</span>
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
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Listado de Clientes</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Total de registros: {{ $clientes->total() }}</p>
                    </div>
                </div>

                <!-- Action Button: Nuevo Cliente -->
                <button 
                    wire:click="openModal" 
                    class="w-full sm:w-auto px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-xl shadow-md shadow-brand-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Nuevo Cliente</span>
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
                            placeholder="Buscar por nombre, NIT/CI, teléfono..." 
                            class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                        >
                    </div>
                </div>

                <!-- Clientes Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Nombre / Razón Social</th>
                                <th class="py-3 px-4">Cédula / NIT / RUC</th>
                                <th class="py-3 px-4">Teléfono</th>
                                <th class="py-3 px-4">Correo Electrónico</th>
                                <th class="py-3 px-4">Dirección</th>
                                <th class="py-3 px-4">Fecha Registro</th>
                                <th class="py-3 px-4 text-center w-28">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($clientes as $index => $cliente)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono font-medium">
                                        {{ $clientes->firstItem() + $index }}
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $cliente->nombre_razon_social }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-300">
                                        {{ $cliente->cedula_nit_ruc ?? 'Sin documento' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                        {{ $cliente->telefono ?? 'Sin teléfono' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                        {{ $cliente->email ?? 'Sin correo' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                        {{ $cliente->direccion ?? 'Sin dirección' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-400">
                                        {{ $cliente->created_at ? $cliente->created_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Editar Button -->
                                            <button 
                                                wire:click="edit({{ $cliente->id }})" 
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/50 transition"
                                                title="Editar Cliente">
                                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                                            </button>

                                            <!-- Eliminar Button -->
                                            <button 
                                                type="button"
                                                onclick="confirmDeleteCliente({{ $cliente->id }}, '{{ addslashes($cliente->nombre_razon_social) }}')" 
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition"
                                                title="Eliminar Cliente">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="fa-solid fa-users-slash text-3xl text-slate-300 dark:text-slate-700"></i>
                                            <p class="text-xs font-medium">No se encontraron clientes registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="pt-2">
                    {{ $clientes->links() }}
                </div>

            </div>
        </div>

    </div>

    <!-- Modal Form (Nuevo / Editar Cliente) -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            <!-- Modal Content Card -->
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-lg transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid {{ $clienteId ? 'fa-pen-to-square' : 'fa-plus' }}"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $clienteId ? 'Editar Cliente' : 'Nuevo Cliente' }}
                            </h3>
                        </div>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body Form Grid -->
                    <form wire:submit.prevent="save" class="space-y-4">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            
                            <!-- Nombre / Razón Social -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Nombre / Razón Social <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="nombre_razon_social" 
                                    placeholder="Ej: Juan Pérez / Comercial SRL" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('nombre_razon_social') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Cédula / NIT / RUC -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Cédula / NIT / RUC
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="cedula_nit_ruc" 
                                    placeholder="Ej: 12345678, 10203040" 
                                    class="w-full px-3.5 py-2.5 text-xs font-mono bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('cedula_nit_ruc') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
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
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Correo Electrónico
                                </label>
                                <input 
                                    type="email" 
                                    wire:model="email" 
                                    placeholder="Ej: cliente@correo.com" 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                >
                                @error('email') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Dirección Domiciliaria o Comercial
                                </label>
                                <textarea 
                                    wire:model="direccion" 
                                    rows="2" 
                                    placeholder="Ej: Calle Los Olivos #450, Zona Norte..." 
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                                ></textarea>
                                @error('direccion') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
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
                                {{ $clienteId ? 'Actualizar Cliente' : 'Guardar Cliente' }}
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

        function confirmDeleteCliente(id, name) {
            Swal.fire({
                title: '¿Eliminar Cliente?',
                text: `¿Estás seguro de eliminar al cliente "${name}"?`,
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
