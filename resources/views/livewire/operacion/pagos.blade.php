<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-transfer text-brand-500"></i>
                    <span>Movimientos y Pagos de Caja</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Control de pagos por ventas, ingresos extraordinarios y egresos/gastos de caja
                </p>
            </div>
            
            <nav class="flex text-xs text-slate-400 font-medium">
                <a href="#" class="hover:text-brand-500 transition">Operaciones</a>
                <span class="mx-2">&sol;</span>
                <span class="text-slate-700 dark:text-slate-200">Pagos</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Active Box Prerequisite Warning Banner -->
        @if (!$activeCaja)
            <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-amber-700 dark:text-amber-300 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold shrink-0 text-base">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-sm">No posees una caja abierta en este momento</div>
                        <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5">
                            Para poder registrar un nuevo gasto, egreso o ingreso extra, debes tener una caja en estado ABIERTA.
                        </p>
                    </div>
                </div>
                <a href="{{ route('operacion.cajas') }}" 
                    class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl transition shadow-sm shrink-0 flex items-center gap-1.5">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Ir a Aperturar Caja</span>
                </a>
            </div>
        @endif

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Total Ingresos Ventas -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Ingresos por Ventas</div>
                    <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">
                        Bs {{ number_format($totalIngresosVentas, 2) }}
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Cobros completados</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                </div>
            </div>

            <!-- Total Ingresos Extras -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Ingresos Extras</div>
                    <div class="text-xl font-black text-cyan-600 dark:text-cyan-400 font-mono mt-0.5">
                        Bs {{ number_format($totalIngresosExtras, 2) }}
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Aportes a caja chica</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-500 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-circle-arrow-down"></i>
                </div>
            </div>

            <!-- Total Egresos / Gastos -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Egresos / Gastos Caja</div>
                    <div class="text-xl font-black text-rose-600 dark:text-rose-400 font-mono mt-0.5">
                        Bs {{ number_format($totalEgresosGastos, 2) }}
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Almuerzos, compras</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-circle-arrow-up"></i>
                </div>
            </div>

            <!-- Total Ingresos Efectivo -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Efectivo (💵)</div>
                    <div class="text-xl font-black text-green-600 dark:text-green-400 font-mono mt-0.5">
                        Bs {{ number_format($totalIngresosEfectivo, 2) }}
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Ingresos en físico</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-green-500/10 text-green-500 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>

            <!-- Total Ingresos Digitales -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Digital (QR/Transf.)</div>
                    <div class="text-xl font-black text-blue-600 dark:text-blue-400 font-mono mt-0.5">
                        Bs {{ number_format($totalIngresosDigital, 2) }}
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Pagos electrónicos</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
            </div>

        </div>


        <!-- Main Card Container -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Listado de Pagos y Movimientos</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Registros encontrados: {{ $pagos->total() }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <!-- Botón Ingreso Extra -->
                    <button 
                        wire:click="openPagoModal('INGRESO_EXTRA')" 
                        @if(!$activeCaja) disabled title="Requiere caja abierta" @endif
                        class="px-3.5 py-2 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Ingreso Extra</span>
                    </button>

                    <!-- Botón Egreso / Gasto -->
                    <button 
                        wire:click="openPagoModal('EGRESO_GASTO')" 
                        @if(!$activeCaja) disabled title="Requiere caja abierta" @endif
                        class="px-3.5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-minus-circle"></i>
                        <span>Nuevo Egreso / Gasto</span>
                    </button>
                </div>
            </div>

            <!-- Card Body & Controls -->
            <div class="p-6 space-y-4">
                
                <!-- Controls Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                    
                    <!-- Records Per Page -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Filas</label>
                        <select 
                            wire:model.live="perPage" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <!-- Fecha Inicio -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Fecha Desde</label>
                        <input 
                            type="date" 
                            wire:model.live="fechaInicio" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition font-mono"
                        >
                    </div>

                    <!-- Fecha Fin -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Fecha Hasta</label>
                        <input 
                            type="date" 
                            wire:model.live="fechaFin" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition font-mono"
                        >
                    </div>

                    <!-- Usuario Filter (ADMIN ONLY) -->
                    <div>
                        @if (auth()->user()->esAdmin())
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Cajero / Usuario</label>
                            <select 
                                wire:model.live="usuarioFilter" 
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                                <option value="">-- Todos los Cajeros --</option>
                                @foreach ($todosUsuarios as $u)
                                    <option value="{{ $u->id }}">{{ $u->nombre_completo ?? $u->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Usuario Filtrado</label>
                            <div class="px-3 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 truncate">
                                <i class="fa-solid fa-user text-brand-500 mr-1"></i> {{ auth()->user()->nombre_completo }}
                            </div>
                        @endif
                    </div>

                    <!-- Tipo Filter -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Tipo Movimiento</label>
                        <select 
                            wire:model.live="tipoFilter" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="">-- Todos --</option>
                            <option value="INGRESO_VENTA">🟢 VENTA (Ingreso)</option>
                            <option value="INGRESO_EXTRA">🔵 INGRESO EXTRA</option>
                            <option value="EGRESO_GASTO">🔴 EGRESO / GASTO</option>
                        </select>
                    </div>

                </div>

                <!-- Search Input Bar -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Buscar por concepto, referencia, N° recibo..." 
                        class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                    >
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Fecha / Hora</th>
                                <th class="py-3 px-4 text-center">Tipo</th>
                                <th class="py-3 px-4">Concepto / Referencia</th>
                                <th class="py-3 px-4 text-center">Método Pago</th>
                                <th class="py-3 px-4">Registrado Por</th>
                                <th class="py-3 px-4 text-right">Monto</th>
                                <th class="py-3 px-4 text-center w-20">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($pagos as $index => $p)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono font-medium">
                                        {{ $pagos->firstItem() + $index }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                        {{ $p->fecha_pago?->format('d/m/Y H:i') ?? $p->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($p->venta_id)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                <i class="fa-solid fa-cart-shopping text-[9px]"></i> VENTA
                                            </span>
                                        @elseif ($p->monto > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                                <i class="fa-solid fa-arrow-down text-[9px]"></i> INGRESO EXTRA
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                                <i class="fa-solid fa-arrow-up text-[9px]"></i> EGRESO / GASTO
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if ($p->venta)
                                            <div class="font-bold text-slate-900 dark:text-white">
                                                Venta N° <span class="font-mono text-brand-500">{{ $p->venta->numero_recibo }}</span>
                                            </div>
                                            <div class="text-[10px] text-slate-400">Cliente: {{ $p->venta->cliente?->nombre_razon_social ?? 'S/N' }}</div>
                                        @else
                                            <div class="font-semibold text-slate-900 dark:text-white">
                                                {{ $p->referencia_transaccion }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-700 dark:text-slate-300 border border-slate-500/20">
                                            {{ $p->metodo_pago }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                        {{ $p->usuario?->nombre_completo ?? $p->usuario?->name ?? 'Sistema' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-extrabold">
                                        @if ($p->monto >= 0)
                                            <span class="text-emerald-600 dark:text-emerald-400">+Bs {{ number_format($p->monto, 2) }}</span>
                                        @else
                                            <span class="text-rose-600 dark:text-rose-400">-Bs {{ number_format(abs($p->monto), 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if (auth()->user()->esAdmin() || $p->usuario_creador_id === auth()->id())
                                            <button 
                                                wire:click="delete({{ $p->id }})" 
                                                wire:confirm="¿Estás seguro de eliminar este registro de movimiento?"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition"
                                                title="Eliminar Movimiento">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="fa-solid fa-money-bill-wave text-3xl text-slate-300 dark:text-slate-700"></i>
                                            <p class="text-xs font-medium">No se encontraron movimientos registrados en las fechas seleccionadas</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="pt-2">
                    {{ $pagos->links() }}
                </div>

            </div>
        </div>

    </div>

    <!-- MODAL: REGISTRAR MOVIMIENTO EXTRA (INGRESO EXTRA / EGRESO GASTO) -->
    @if ($isPagoModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closePagoModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all space-y-4">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl {{ $tipoMovimiento === 'EGRESO_GASTO' ? 'bg-rose-500/10 text-rose-500' : 'bg-cyan-500/10 text-cyan-500' }} flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid {{ $tipoMovimiento === 'EGRESO_GASTO' ? 'fa-minus-circle' : 'fa-plus-circle' }}"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $tipoMovimiento === 'EGRESO_GASTO' ? 'Registrar Egreso / Gasto de Caja' : 'Registrar Ingreso Extra de Caja' }}
                            </h3>
                        </div>
                        <button wire:click="closePagoModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="savePago" class="space-y-4">
                        
                        <!-- Tipo Selector Buttons -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Tipo de Movimiento
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <button 
                                    type="button" 
                                    wire:click="$set('tipoMovimiento', 'EGRESO_GASTO')"
                                    class="py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 border {{ $tipoMovimiento === 'EGRESO_GASTO' ? 'bg-rose-600 text-white border-rose-600 shadow-md' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                    <i class="fa-solid fa-arrow-up"></i>
                                    <span>🔴 EGRESO / GASTO</span>
                                </button>

                                <button 
                                    type="button" 
                                    wire:click="$set('tipoMovimiento', 'INGRESO_EXTRA')"
                                    class="py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 border {{ $tipoMovimiento === 'INGRESO_EXTRA' ? 'bg-cyan-600 text-white border-cyan-600 shadow-md' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                    <i class="fa-solid fa-arrow-down"></i>
                                    <span>🔵 INGRESO EXTRA</span>
                                </button>
                            </div>
                        </div>

                        <!-- Monto (Bs) -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Monto (Bs) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                step="0.10" 
                                min="0.10"
                                wire:model="monto" 
                                placeholder="0.00" 
                                class="w-full px-3.5 py-2.5 text-sm font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                            @error('monto') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>


                        <!-- Método de Pago -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Método de Pago <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                wire:model="metodoPago" 
                                class="w-full px-3.5 py-2.5 text-xs font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                                <option value="EFECTIVO">💵 EFECTIVO</option>
                                <option value="QR">📱 PAGO QR</option>
                                <option value="TRANSFERENCIA">🏦 TRANSFERENCIA BANCARIA</option>
                            </select>
                            @error('metodoPago') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Concepto / Motivo -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Concepto / Motivo <span class="text-rose-500">*</span>
                            </label>
                            <textarea 
                                wire:model="concepto" 
                                rows="3" 
                                placeholder="{{ $tipoMovimiento === 'EGRESO_GASTO' ? 'Ej: Compra de almuerzo personal, material de escritorio, pago de pasajes...' : 'Ej: Reposición de dinero en caja chica, aporte adicional...' }}" 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            ></textarea>
                            @error('concepto') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Referencia Opcional -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                N° Comprobante / Referencia (Opcional)
                            </label>
                            <input 
                                type="text" 
                                wire:model="referenciaTransaccion" 
                                placeholder="Ej: Factura N° 1234, Recibo..." 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                            @error('referenciaTransaccion') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="closePagoModal" 
                                class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-xs font-bold text-white {{ $tipoMovimiento === 'EGRESO_GASTO' ? 'bg-rose-600 hover:bg-rose-500' : 'bg-cyan-600 hover:bg-cyan-500' }} rounded-xl shadow-md transition active:scale-[0.98]">
                                Guardar Movimiento
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
    </script>
</div>
