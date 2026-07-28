<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-brand-500"></i>
                    <span>Gestión de Cajas y Arqueos</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Control de aperturas, cierres y balance de caja por sucursal
                </p>
            </div>
            
            <nav class="flex text-xs text-slate-400 font-medium">
                <a href="#" class="hover:text-brand-500 transition">Operaciones</a>
                <span class="mx-2">&sol;</span>
                <span class="text-slate-700 dark:text-slate-200">Cajas</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Active User Box Banner (if open) -->
        @if ($userActiveBox)
            <div class="bg-gradient-to-r from-emerald-500/15 via-teal-500/10 to-brand-500/10 border border-emerald-500/30 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <div>
                        <div class="text-xs font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>CAJA ABIERTA ACTIVA</span>
                            <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                                {{ $userActiveBox->sucursal?->nombre }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-0.5">
                            Aperturada el {{ $userActiveBox->fecha_apertura?->format('d/m/Y H:i') }} | 
                            Monto Inicial: <strong class="font-mono text-emerald-600 dark:text-emerald-400">Bs {{ number_format($userActiveBox->monto_apertura, 2) }}</strong>
                        </p>
                    </div>
                </div>

                <button 
                    wire:click="openCierreModal({{ $userActiveBox->id }})" 
                    class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-md shadow-rose-600/20 active:scale-[0.98] transition flex items-center gap-2">
                    <i class="fa-solid fa-lock text-xs"></i>
                    <span>Realizar Cierre / Arqueo</span>
                </button>
            </div>
        @endif
        
        <!-- Main Card Container -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Historial de Cajas</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Total de registros: {{ $cajas->total() }}</p>
                    </div>
                </div>

                <!-- Action Button: Aperturar Caja -->
                <button 
                    wire:click="openAperturaModal" 
                    class="w-full sm:w-auto px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-xl shadow-md shadow-brand-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-key text-xs"></i>
                    <span>Aperturar Nueva Caja</span>
                </button>
            </div>

            <!-- Card Body -->
            <div class="p-6 space-y-4">
                
                <!-- Controls Bar: Per Page, Filter & Search -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
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
                            <span>filas</span>
                        </div>

                        <!-- Status Filter -->
                        <div class="w-full sm:w-auto">
                            <select 
                                wire:model.live="estadoFilter" 
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-1.5 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                                <option value="">-- Todos los Estados --</option>
                                <option value="ABIERTA">ABIERTA</option>
                                <option value="CERRADA">CERRADA</option>
                            </select>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Buscar por cajero u observaciones..." 
                            class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                        >
                    </div>

                </div>

                <!-- Cajas Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Cajero / Usuario</th>
                                <th class="py-3 px-4">Sucursal</th>
                                <th class="py-3 px-4 text-right">Apertura</th>
                                <th class="py-3 px-4 text-right">Efectivo</th>
                                <th class="py-3 px-4 text-right">Digital</th>
                                <th class="py-3 px-4 text-right">Esperado</th>
                                <th class="py-3 px-4 text-right">Cierre</th>
                                <th class="py-3 px-4 text-right">Diferencia</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4">Fecha Apertura</th>
                                <th class="py-3 px-4 text-center w-28">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($cajas as $index => $caja)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono font-medium">
                                        {{ $cajas->firstItem() + $index }}
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $caja->usuario?->nombre_completo ?? 'Usuario' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                            {{ $caja->sucursal?->nombre }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-700 dark:text-slate-300">
                                        Bs {{ number_format($caja->monto_apertura, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-600 dark:text-slate-400">
                                        Bs {{ number_format($caja->ventas_efectivo, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-600 dark:text-slate-400">
                                        Bs {{ number_format($caja->ventas_digital, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-slate-800 dark:text-slate-200">
                                        Bs {{ number_format($caja->total_esperado, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                        {{ $caja->estado === 'CERRADA' ? 'Bs '.number_format($caja->monto_cierre, 2) : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold">
                                        @if ($caja->estado === 'CERRADA')
                                            @if ($caja->diferencia == 0)
                                                <span class="text-emerald-600 dark:text-emerald-400">Bs 0.00</span>
                                            @elseif ($caja->diferencia < 0)
                                                <span class="text-rose-600 dark:text-rose-400" title="Faltante en caja">
                                                    Bs {{ number_format($caja->diferencia, 2) }}
                                                </span>
                                            @else
                                                <span class="text-blue-600 dark:text-blue-400" title="Sobrante en caja">
                                                    +Bs {{ number_format($caja->diferencia, 2) }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($caja->estado === 'ABIERTA')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                <i class="fa-solid fa-lock-open text-[9px]"></i> ABIERTA
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-500 dark:text-slate-400 border border-slate-500/20">
                                                <i class="fa-solid fa-lock text-[9px]"></i> CERRADA
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-400">
                                        <div>{{ $caja->fecha_apertura?->format('d/m/Y H:i') }}</div>
                                        @if ($caja->fecha_cierre)
                                            <div class="text-[10px] text-slate-500">Cierre: {{ $caja->fecha_cierre->format('H:i') }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            
                                            <!-- Cierre Button -->
                                            @if ($caja->estado === 'ABIERTA' && ($caja->usuario_id === auth()->id() || auth()->user()->esAdmin()))
                                                <button 
                                                    wire:click="openCierreModal({{ $caja->id }})" 
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition"
                                                    title="Realizar Cierre de Caja">
                                                    <i class="fa-solid fa-lock text-sm"></i>
                                                </button>
                                            @endif

                                            <!-- Eliminar Button -->
                                            <button 
                                                type="button"
                                                onclick="confirmDeleteCaja({{ $caja->id }})" 
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition"
                                                title="Eliminar Registro">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="fa-solid fa-cash-register text-3xl text-slate-300 dark:text-slate-700"></i>
                                            <p class="text-xs font-medium">No se encontraron registros de caja</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="pt-2">
                    {{ $cajas->links() }}
                </div>

            </div>
        </div>

    </div>

    <!-- Apertura Modal Component -->
    @if ($isAperturaModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeAperturaModal"></div>

            <!-- Modal Content Card -->
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                Apertura de Caja
                            </h3>
                        </div>
                        <button wire:click="closeAperturaModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body Form -->
                    <form wire:submit.prevent="saveApertura" class="space-y-4">
                        
                        <!-- Sucursal info badge -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Sucursal Asignada:</span>
                            <span class="font-bold text-brand-600 dark:text-brand-400">{{ auth()->user()->sucursal?->nombre ?? 'Sucursal Central' }}</span>
                        </div>

                        <!-- Monto Inicial -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Monto Inicial de Apertura (Bs) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model="monto_apertura" 
                                placeholder="0.00" 
                                class="w-full px-3.5 py-2.5 text-xs font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                            @error('monto_apertura') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Observaciones de Apertura
                            </label>
                            <textarea 
                                wire:model="observaciones_apertura" 
                                rows="3" 
                                placeholder="Notas opcionales al abrir caja..." 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            ></textarea>
                            @error('observaciones_apertura') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="closeAperturaModal" 
                                class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-xl shadow-md transition active:scale-[0.98]">
                                Confirmar Apertura
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endif

    <!-- Cierre Modal Component -->
    @if ($isCierreModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeCierreModal"></div>

            <!-- Modal Content Card -->
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                Arqueo y Cierre de Caja
                            </h3>
                        </div>
                        <button wire:click="closeCierreModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body Form -->
                    <form wire:submit.prevent="saveCierre" class="space-y-4">
                        
                        <!-- Info Box -->
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-xl space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Monto Inicial Apertura:</span>
                                <span class="font-mono font-semibold">Bs {{ number_format($cajaSelectedForClose?->monto_apertura ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Cajero Responsable:</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $cajaSelectedForClose?->usuario?->nombre_completo }}</span>
                            </div>
                        </div>

                        <!-- Monto Cierre Físico -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Monto Conteo Físico Real (Bs) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model="monto_cierre" 
                                placeholder="0.00" 
                                class="w-full px-3.5 py-2.5 text-xs font-mono font-extrabold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition text-base"
                            >
                            @error('monto_cierre') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Observaciones Cierre -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Observaciones de Cierre
                            </label>
                            <textarea 
                                wire:model="observaciones_cierre" 
                                rows="3" 
                                placeholder="Justificación de faltantes/sobrantes u observaciones..." 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            ></textarea>
                            @error('observaciones_cierre') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="closeCierreModal" 
                                class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 rounded-xl shadow-md transition active:scale-[0.98]">
                                Confirmar Arqueo y Cierre
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

        function confirmDeleteCaja(id) {
            Swal.fire({
                title: '¿Eliminar Registro de Caja?',
                text: '¿Estás seguro de eliminar este registro de caja?',
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
