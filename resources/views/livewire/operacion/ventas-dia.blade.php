<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-brand-500"></i>
                    <span>Ventas del Día e Historial</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Consulta de comprobantes, reimpresión de tickets, detalles y devoluciones
                </p>
            </div>
            
            <nav class="flex text-xs text-slate-400 font-medium">
                <a href="#" class="hover:text-brand-500 transition">Operaciones</a>
                <span class="mx-2">&sol;</span>
                <span class="text-slate-700 dark:text-slate-200">Ventas del Día</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Total Ventas Cantidad -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Ventas Realizadas</div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalVentasCantidad) }}</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Ventas completadas en rango seleccionado</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>

            <!-- Total Recaudado Monto -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Ingresos por Ventas</div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1">Bs {{ number_format($totalVentasMonto, 2) }}</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Recaudación acumulada libre de anulaciones</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
        </div>

        <!-- Main Card Container -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Listado de Comprobantes</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Registros encontrados: {{ $ventas->total() }}</p>
                    </div>
                </div>
            </div>

            <!-- Card Body & Controls -->
            <div class="p-6 space-y-4">
                
                <!-- Controls Bar: PerPage, Date Range, Sucursal (Admin), Status & Search -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 items-end">
                    
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

                    <!-- Sucursal Filter (ADMIN ONLY) -->
                    <div>
                        @if (auth()->user()->esAdmin())
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Sucursal</label>
                            <select 
                                wire:model.live="sucursalFilter" 
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                                <option value="">-- Todas --</option>
                                @foreach ($todasSucursales as $suc)
                                    <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                                @endforeach
                            </select>
                        @else
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Sucursal Asignada</label>
                            <div class="px-3 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 truncate">
                                <i class="fa-solid fa-store text-brand-500 mr-1"></i> {{ auth()->user()->sucursal?->nombre }}
                            </div>
                        @endif
                    </div>

                    <!-- Estado Filter -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Estado</label>
                        <select 
                            wire:model.live="estadoFilter" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="">-- Todos --</option>
                            <option value="COMPLETADA">COMPLETADA</option>
                            <option value="ANULADA">ANULADA</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Buscar Recibo/Cliente</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="REC-..., Cliente..." 
                                class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                        </div>
                    </div>

                </div>

                <!-- Ventas Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Fecha / Hora</th>
                                <th class="py-3 px-4 font-mono">N° Recibo</th>
                                <th class="py-3 px-4">Cliente</th>
                                <th class="py-3 px-4">Sucursal</th>
                                <th class="py-3 px-4">Cajero</th>
                                <th class="py-3 px-4 text-center">Forma Pago</th>
                                <th class="py-3 px-4 text-right">Total</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-center w-36">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($ventas as $index => $v)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono font-medium">
                                        {{ $ventas->firstItem() + $index }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                        {{ $v->fecha_venta?->format('d/m/Y H:i') ?? $v->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                        {{ $v->numero_recibo }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-900 dark:text-white">
                                            {{ $v->cliente?->nombre_razon_social ?? 'Cliente S/N' }}
                                        </div>
                                        @if ($v->cliente?->cedula_nit_ruc && $v->cliente->cedula_nit_ruc !== '0')
                                            <div class="text-[10px] font-mono text-slate-400">NIT/CI: {{ $v->cliente->cedula_nit_ruc }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                            {{ $v->sucursal?->nombre }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                        {{ $v->usuario?->nombre_completo ?? 'Usuario' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-700 dark:text-slate-300 border border-slate-500/20">
                                            {{ $v->metodo_pago_principal }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-extrabold text-slate-900 dark:text-white">
                                        Bs {{ number_format($v->total, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($v->estado === 'COMPLETADA')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i> COMPLETADA
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20" title="{{ $v->observaciones }}">
                                                <i class="fa-solid fa-ban text-[9px]"></i> ANULADA
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            
                                            <!-- Reimprimir Ticket PDF -->
                                            <button 
                                                type="button"
                                                onclick="reimprimirRecibo('{{ route('operacion.ventas.recibo', $v->id) }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/50 transition"
                                                title="Reimprimir Ticket PDF">
                                                <i class="fa-solid fa-print text-sm"></i>
                                            </button>

                                            <!-- Ver Detalles -->
                                            <button 
                                                wire:click="openDetailModal({{ $v->id }})" 
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 transition"
                                                title="Ver Detalle de Venta">
                                                <i class="fa-solid fa-eye text-sm"></i>
                                            </button>

                                            <!-- Anular Venta / Devolución -->
                                            @if ($v->estado === 'COMPLETADA' && (auth()->user()->esAdmin() || $v->usuario_id === auth()->id()))
                                                <button 
                                                    wire:click="openAnularModal({{ $v->id }})" 
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition"
                                                    title="Anular Venta / Devolución de Stock">
                                                    <i class="fa-solid fa-ban text-sm"></i>
                                                </button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="fa-solid fa-receipt text-3xl text-slate-300 dark:text-slate-700"></i>
                                            <p class="text-xs font-medium">No se encontraron ventas en el rango de fechas seleccionado</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="pt-2">
                    {{ $ventas->links() }}
                </div>

            </div>
        </div>

    </div>

    <!-- MODAL 1: DETALLE DE VENTA -->
    @if ($isDetailModalOpen && $selectedVenta)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeDetailModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all space-y-4">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    Detalle de Venta: <span class="font-mono text-brand-500">{{ $selectedVenta->numero_recibo }}</span>
                                </h3>
                                <p class="text-[11px] text-slate-400">
                                    Fecha: {{ $selectedVenta->fecha_venta?->format('d/m/Y H:i') }} | Sucursal: {{ $selectedVenta->sucursal?->nombre }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeDetailModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Meta Data Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Cliente</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $selectedVenta->cliente?->nombre_razon_social ?? 'Cliente S/N' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Cajero</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $selectedVenta->usuario?->nombre_completo }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Forma de Pago</span>
                            <span class="font-bold text-brand-600 dark:text-brand-400">{{ $selectedVenta->metodo_pago_principal }}</span>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[10px] font-bold uppercase tracking-wider">
                                    <th class="py-2.5 px-3 w-10 text-center">#</th>
                                    <th class="py-2.5 px-3">Producto</th>
                                    <th class="py-2.5 px-3 text-right">Cant.</th>
                                    <th class="py-2.5 px-3 text-right">P. Unit</th>
                                    <th class="py-2.5 px-3 text-right">Desc. Item</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                                @foreach ($selectedVenta->detalles as $idx => $det)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                        <td class="py-2 px-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                        <td class="py-2 px-3">
                                            <div class="font-semibold text-slate-900 dark:text-white">{{ $det->producto?->nombre ?? 'Producto' }}</div>
                                            <div class="text-[10px] font-mono text-slate-400">SKU: {{ $det->producto?->sku }}</div>
                                        </td>
                                        <td class="py-2 px-3 text-right font-mono font-bold">{{ number_format($det->cantidad, 0) }}</td>
                                        <td class="py-2 px-3 text-right font-mono">Bs {{ number_format($det->precio_unitario, 2) }}</td>
                                        <td class="py-2 px-3 text-right font-mono text-rose-500">
                                            {{ $det->descuento_unitario > 0 ? '-Bs '.number_format($det->descuento_unitario, 2) : '-' }}
                                        </td>
                                        <td class="py-2 px-3 text-right font-mono font-bold text-slate-900 dark:text-white">Bs {{ number_format($det->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals Breakdown -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal Bruto:</span>
                            <span class="font-mono font-semibold">Bs {{ number_format($selectedVenta->subtotal, 2) }}</span>
                        </div>
                        @if ($selectedVenta->descuento_general > 0)
                            <div class="flex justify-between text-rose-500">
                                <span>Descuento General:</span>
                                <span class="font-mono font-semibold">-Bs {{ number_format($selectedVenta->descuento_general, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center text-sm font-extrabold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700">
                            <span>TOTAL FINAL:</span>
                            <span class="font-mono text-emerald-600 dark:text-emerald-400 text-base">Bs {{ number_format($selectedVenta->total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-2">
                        <button 
                            type="button" 
                            onclick="reimprimirRecibo('{{ route('operacion.ventas.recibo', $selectedVenta->id) }}')" 
                            class="px-4 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-print"></i>
                            <span>Imprimir Ticket PDF</span>
                        </button>
                        <button 
                            type="button" 
                            wire:click="closeDetailModal" 
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Cerrar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: ANULACIÓN / DEVOLUCIÓN DE VENTA -->
    @if ($isAnularModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeAnularModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all space-y-4">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-ban"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Anulación y Devolución de Venta</h3>
                        </div>
                        <button wire:click="closeAnularModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs text-amber-700 dark:text-amber-300 space-y-1">
                        <div class="font-bold flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>Atención: Restauración de Stock</span>
                        </div>
                        <p class="text-[11px]">
                            Al anular esta venta, el estado del recibo cambiará a ANULADA y todos los productos incluidos serán devueltos automáticamente al stock de la sucursal.
                        </p>
                    </div>

                    <form wire:submit.prevent="anularVenta" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Motivo de la Anulación / Devolución <span class="text-rose-500">*</span>
                            </label>
                            <textarea 
                                wire:model="motivoAnulacion" 
                                rows="3" 
                                placeholder="Ej: Error en facturación, devolución solicitada por cliente..." 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-rose-500 focus:outline-none transition"
                            ></textarea>
                            @error('motivoAnulacion') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="closeAnularModal" 
                                class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 rounded-xl shadow-md transition active:scale-[0.98]">
                                Confirmar Anulación
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

    <!-- SweetAlert2 Listener & Print Script -->
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

        function reimprimirRecibo(url) {
            const printWin = window.open(
                url, 
                'ReciboTicket_' + Date.now(), 
                'width=450,height=650,top=100,left=100,scrollbars=yes,resizable=yes'
            );
            if (printWin) {
                printWin.focus();
            }
        }
    </script>
</div>
