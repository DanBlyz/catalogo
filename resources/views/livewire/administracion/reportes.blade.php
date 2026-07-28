<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-brand-500"></i>
                    <span>Módulo de Reportes & Estadísticas</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Generación de informes de stock, ventas, ingresos, egresos, utilidades y exportación a PDF
                </p>
            </div>
            
            <nav class="flex text-xs text-slate-400 font-medium">
                <a href="#" class="hover:text-brand-500 transition">Administración</a>
                <span class="mx-2">&sol;</span>
                <span class="text-slate-700 dark:text-slate-200">Reportes</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Report Type Selection Cards (Tabs) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            
            <!-- Stock por Sucursal -->
            <button 
                type="button" 
                wire:click="$set('tipoReporte', 'stock_sucursal')"
                class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-2 {{ $tipoReporte === 'stock_sucursal' ? 'bg-brand-600 text-white border-brand-600 shadow-md shadow-brand-600/30' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-brand-500' }}">
                <div class="w-8 h-8 rounded-xl {{ $tipoReporte === 'stock_sucursal' ? 'bg-white/20 text-white' : 'bg-brand-500/10 text-brand-500' }} flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <div class="text-xs font-bold truncate">Stock por Sucursal</div>
                    <div class="text-[10px] {{ $tipoReporte === 'stock_sucursal' ? 'text-brand-100' : 'text-slate-400' }} truncate">Inventario y costo</div>
                </div>
            </button>

            <!-- Ventas por Sucursal -->
            <button 
                type="button" 
                wire:click="$set('tipoReporte', 'ventas_sucursal')"
                class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-2 {{ $tipoReporte === 'ventas_sucursal' ? 'bg-brand-600 text-white border-brand-600 shadow-md shadow-brand-600/30' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-brand-500' }}">
                <div class="w-8 h-8 rounded-xl {{ $tipoReporte === 'ventas_sucursal' ? 'bg-white/20 text-white' : 'bg-teal-500/10 text-teal-500' }} flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div>
                    <div class="text-xs font-bold truncate">Ventas por Sucursal</div>
                    <div class="text-[10px] {{ $tipoReporte === 'ventas_sucursal' ? 'text-brand-100' : 'text-slate-400' }} truncate">Recaudación y recibos</div>
                </div>
            </button>

            <!-- Ingresos y Egresos -->
            <button 
                type="button" 
                wire:click="$set('tipoReporte', 'ingresos_egresos')"
                class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-2 {{ $tipoReporte === 'ingresos_egresos' ? 'bg-brand-600 text-white border-brand-600 shadow-md shadow-brand-600/30' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-brand-500' }}">
                <div class="w-8 h-8 rounded-xl {{ $tipoReporte === 'ingresos_egresos' ? 'bg-white/20 text-white' : 'bg-emerald-500/10 text-emerald-500' }} flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <div>
                    <div class="text-xs font-bold truncate">Ingresos y Egresos</div>
                    <div class="text-[10px] {{ $tipoReporte === 'ingresos_egresos' ? 'text-brand-100' : 'text-slate-400' }} truncate">Movimientos caja</div>
                </div>
            </button>

            <!-- Utilidades -->
            <button 
                type="button" 
                wire:click="$set('tipoReporte', 'utilidades')"
                class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-2 {{ $tipoReporte === 'utilidades' ? 'bg-brand-600 text-white border-brand-600 shadow-md shadow-brand-600/30' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-brand-500' }}">
                <div class="w-8 h-8 rounded-xl {{ $tipoReporte === 'utilidades' ? 'bg-white/20 text-white' : 'bg-amber-500/10 text-amber-500' }} flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <div class="text-xs font-bold truncate">Utilidades y Margen</div>
                    <div class="text-[10px] {{ $tipoReporte === 'utilidades' ? 'text-brand-100' : 'text-slate-400' }} truncate">Ganancia neta real</div>
                </div>
            </button>

            <!-- Top Productos -->
            <button 
                type="button" 
                wire:click="$set('tipoReporte', 'productos_top')"
                class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-2 {{ $tipoReporte === 'productos_top' ? 'bg-brand-600 text-white border-brand-600 shadow-md shadow-brand-600/30' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-brand-500' }}">
                <div class="w-8 h-8 rounded-xl {{ $tipoReporte === 'productos_top' ? 'bg-white/20 text-white' : 'bg-purple-500/10 text-purple-500' }} flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <div>
                    <div class="text-xs font-bold truncate">Ranking de Productos</div>
                    <div class="text-[10px] {{ $tipoReporte === 'productos_top' ? 'text-brand-100' : 'text-slate-400' }} truncate">Más vendidos</div>
                </div>
            </button>

            <!-- Arqueo Cajas -->
            <button 
                type="button" 
                wire:click="$set('tipoReporte', 'arqueo_cajas')"
                class="p-3.5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-2 {{ $tipoReporte === 'arqueo_cajas' ? 'bg-brand-600 text-white border-brand-600 shadow-md shadow-brand-600/30' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-brand-500' }}">
                <div class="w-8 h-8 rounded-xl {{ $tipoReporte === 'arqueo_cajas' ? 'bg-white/20 text-white' : 'bg-cyan-500/10 text-cyan-500' }} flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
                <div>
                    <div class="text-xs font-bold truncate">Arqueo de Cajas</div>
                    <div class="text-[10px] {{ $tipoReporte === 'arqueo_cajas' ? 'text-brand-100' : 'text-slate-400' }} truncate">Auditoría cierres</div>
                </div>
            </button>

        </div>

        <!-- Filter Controls Container -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
            <div class="text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-white flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-filter text-brand-500"></i>
                    <span>Parámetros y Filtros de Búsqueda</span>
                </div>
                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        wire:click="resetFilters" 
                        class="px-3 py-1.5 text-[11px] font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Restablecer
                    </button>
                    
                    <button 
                        type="button" 
                        wire:click="exportPdf" 
                        class="px-4 py-2 text-xs font-bold text-white bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 rounded-xl shadow-md transition flex items-center gap-1.5 active:scale-[0.98]">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Exportar PDF</span>
                    </button>
                </div>
            </div>

            <!-- Controls Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                
                <!-- Sucursal Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Sucursal</label>
                    <select 
                        wire:model.live="sucursalId" 
                        @if (!auth()->user()->esAdmin()) disabled @endif
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                        <option value="">-- Todas las Sucursales --</option>
                        @foreach ($todasSucursales as $s)
                            <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Usuario Filter -->
                @if (in_array($tipoReporte, ['ventas_sucursal', 'ingresos_egresos', 'arqueo_cajas']))
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Cajero / Usuario</label>
                        <select 
                            wire:model.live="usuarioId" 
                            @if (!auth()->user()->esAdmin()) disabled @endif
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="">-- Todos los Usuarios --</option>
                            @foreach ($todosUsuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->nombre_completo ?? $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Categoría & Marca Filters (for Stock) -->
                @if ($tipoReporte === 'stock_sucursal')
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Categoría</label>
                        <select 
                            wire:model.live="categoriaId" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="">-- Todas --</option>
                            @foreach ($todasCategorias as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Marca</label>
                        <select 
                            wire:model.live="marcaId" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="">-- Todas --</option>
                            @foreach ($todasMarcas as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Date Range Filters -->
                @if (in_array($tipoReporte, ['stock_sucursal', 'ventas_sucursal', 'ingresos_egresos', 'utilidades', 'productos_top', 'arqueo_cajas']))
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Fecha Desde</label>
                        <input 
                            type="date" 
                            wire:model.live="fechaInicio" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition font-mono"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Fecha Hasta</label>
                        <input 
                            type="date" 
                            wire:model.live="fechaFin" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition font-mono"
                        >
                    </div>
                @endif

            </div>
        </div>

        <!-- Summary KPI Metrics Cards -->
        @if (!empty($resumenMetrics))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($resumenMetrics as $lbl => $val)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm">
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">{{ $lbl }}</div>
                        <div class="text-xl font-black text-slate-900 dark:text-white font-mono mt-1">{{ $val }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Live Preview Data Table Container -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-table text-sm"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Vista Previa de Datos</h2>
                </div>
            </div>

            <div class="p-6 overflow-x-auto">
                
                @if ($tipoReporte === 'stock_sucursal')
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Producto</th>
                                <th class="py-3 px-4 font-mono">SKU</th>
                                <th class="py-3 px-4">Categoría / Marca</th>
                                <th class="py-3 px-4">Sucursal</th>
                                <th class="py-3 px-4 text-center">Stock Inicial</th>
                                <th class="py-3 px-4 text-center">Stock Actual</th>
                                <th class="py-3 px-4 text-right">P. Compra</th>
                                <th class="py-3 px-4 text-right">P. Venta</th>
                                <th class="py-3 px-4 text-right">Valor Costo Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($reporteData as $idx => $r)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-4 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-4 font-bold text-slate-900 dark:text-white">{{ $r->producto?->nombre }}</td>
                                    <td class="py-2.5 px-4 font-mono text-slate-500">{{ $r->producto?->sku }}</td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-400">{{ $r->producto?->categoria?->nombre }} / {{ $r->producto?->marca?->nombre }}</td>
                                    <td class="py-2.5 px-4 text-cyan-600 dark:text-cyan-400 font-semibold">{{ $r->sucursal?->nombre }}</td>
                                    <td class="py-2.5 px-4 text-center font-mono font-bold text-slate-600 dark:text-slate-400">
                                        {{ number_format($r->stock_inicial ?? 0, 0) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-center font-mono font-extrabold {{ $r->stock_actual <= 0 ? 'text-rose-500' : 'text-emerald-600 dark:text-emerald-400' }}">
                                        {{ number_format($r->stock_actual, 0) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono">Bs {{ number_format($r->producto?->precio_compra, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono">Bs {{ number_format($r->producto?->precio_venta, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                        Bs {{ number_format($r->stock_actual * ($r->producto?->precio_compra ?? 0), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="py-8 text-center text-slate-400">No se encontraron productos en el inventario.</td></tr>
                            @endforelse
                        </tbody>
                    </table>


                @elseif ($tipoReporte === 'ventas_sucursal')
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Fecha / Hora</th>
                                <th class="py-3 px-4 font-mono">N° Recibo</th>
                                <th class="py-3 px-4">Cliente</th>
                                <th class="py-3 px-4">Sucursal</th>
                                <th class="py-3 px-4">Cajero</th>
                                <th class="py-3 px-4 text-center">Pago</th>
                                <th class="py-3 px-4 text-right">Total</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($reporteData as $idx => $v)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-4 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-400">{{ $v->fecha_venta?->format('d/m/Y H:i') }}</td>
                                    <td class="py-2.5 px-4 font-mono font-bold text-slate-900 dark:text-white">{{ $v->numero_recibo }}</td>
                                    <td class="py-2.5 px-4 font-medium">{{ $v->cliente?->nombre_razon_social ?? 'Cliente S/N' }}</td>
                                    <td class="py-2.5 px-4 text-cyan-600 dark:text-cyan-400 font-semibold">{{ $v->sucursal?->nombre }}</td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-300">{{ $v->usuario?->nombre_completo }}</td>
                                    <td class="py-2.5 px-4 text-center font-bold text-slate-600 dark:text-slate-400">{{ $v->metodo_pago_principal }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">Bs {{ number_format($v->total, 2) }}</td>
                                    <td class="py-2.5 px-4 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $v->estado === 'COMPLETADA' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400' }}">
                                            {{ $v->estado }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="py-8 text-center text-slate-400">No se encontraron ventas registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                @elseif ($tipoReporte === 'ingresos_egresos')
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Fecha / Hora</th>
                                <th class="py-3 px-4 text-center">Tipo</th>
                                <th class="py-3 px-4">Concepto / Referencia</th>
                                <th class="py-3 px-4">Registrado Por</th>
                                <th class="py-3 px-4 text-center">Método Pago</th>
                                <th class="py-3 px-4 text-right">Monto (Bs)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($reporteData as $idx => $p)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-4 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-400">{{ $p->fecha_pago?->format('d/m/Y H:i') }}</td>
                                    <td class="py-2.5 px-4 text-center">
                                        @if ($p->venta_id)
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">VENTA</span>
                                        @elseif ($p->monto > 0)
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400">EXTRA</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400">GASTO</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 font-medium">
                                        @if ($p->venta)
                                            Venta N° {{ $p->venta->numero_recibo }} ({{ $p->venta->cliente?->nombre_razon_social }})
                                        @else
                                            {{ $p->referencia_transaccion }}
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-300">{{ $p->usuario?->nombre_completo ?? $p->usuario?->name }}</td>
                                    <td class="py-2.5 px-4 text-center font-bold text-slate-600 dark:text-slate-400">{{ $p->metodo_pago }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold {{ $p->monto >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $p->monto >= 0 ? '+Bs '.number_format($p->monto, 2) : '-Bs '.number_format(abs($p->monto), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-8 text-center text-slate-400">No se encontraron movimientos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                @elseif ($tipoReporte === 'utilidades')
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4 font-mono">N° Recibo</th>
                                <th class="py-3 px-4">Fecha</th>
                                <th class="py-3 px-4">Sucursal</th>
                                <th class="py-3 px-4 text-right">Venta Bruta</th>
                                <th class="py-3 px-4 text-right">Costo Histórico</th>
                                <th class="py-3 px-4 text-right">Descuentos</th>
                                <th class="py-3 px-4 text-right">Utilidad Neta Real</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($reporteData as $idx => $v)
                                @php
                                    $costoProd = 0.00;
                                    foreach ($v->detalles as $d) {
                                        $mov = $v->movimientos->firstWhere('producto_id', $d->producto_id);
                                        $costoUnitarioHistorico = $mov ? (float) $mov->precio_compra : (float) ($d->producto->precio_compra ?? 0);
                                        $costoProd += ($costoUnitarioHistorico * $d->cantidad);
                                    }
                                    $utilidadRow = $v->total - $costoProd;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-4 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-4 font-mono font-bold text-slate-900 dark:text-white">{{ $v->numero_recibo }}</td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-400">{{ $v->fecha_venta?->format('d/m/Y H:i') }}</td>
                                    <td class="py-2.5 px-4 text-cyan-600 font-semibold">{{ $v->sucursal?->nombre }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono">Bs {{ number_format($v->subtotal, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-500">Bs {{ number_format($costoProd, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono text-rose-500">-Bs {{ number_format($v->descuento_general, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                        Bs {{ number_format($utilidadRow, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="py-8 text-center text-slate-400">No hay ventas registradas en el período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>


                @elseif ($tipoReporte === 'productos_top')
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">Ranking</th>
                                <th class="py-3 px-4">Producto</th>
                                <th class="py-3 px-4 font-mono">SKU</th>
                                <th class="py-3 px-4">Categoría</th>
                                <th class="py-3 px-4 text-center">Unidades Vendidas</th>
                                <th class="py-3 px-4 text-right">Monto Generado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($reporteData as $idx => $r)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-4 text-center font-mono font-extrabold text-brand-500 text-sm">{{ $idx + 1 }}°</td>
                                    <td class="py-2.5 px-4 font-bold text-slate-900 dark:text-white">{{ $r->producto?->nombre }}</td>
                                    <td class="py-2.5 px-4 font-mono text-slate-500">{{ $r->producto?->sku }}</td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-400">{{ $r->producto?->categoria?->nombre }}</td>
                                    <td class="py-2.5 px-4 text-center font-mono font-black text-brand-600 dark:text-brand-400 text-sm">
                                        {{ number_format($r->total_cant, 0) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                        Bs {{ number_format($r->total_monto, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-8 text-center text-slate-400">No hay registros de productos vendidos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                @elseif ($tipoReporte === 'arqueo_cajas')
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Apertura</th>
                                <th class="py-3 px-4">Sucursal</th>
                                <th class="py-3 px-4">Cajero</th>
                                <th class="py-3 px-4 text-right">Monto Inicial</th>
                                <th class="py-3 px-4 text-right">Ventas Efectivo</th>
                                <th class="py-3 px-4 text-right">Ventas QR / Transf.</th>
                                <th class="py-3 px-4 text-right">Gastos</th>
                                <th class="py-3 px-4 text-right">Monto Final</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($reporteData as $idx => $c)
                                @php
                                    $vEfectivo = $c->pagos->where('metodo_pago', 'EFECTIVO')->where('monto', '>', 0)->sum('monto');
                                    $vDigital = $c->pagos->whereIn('metodo_pago', ['QR', 'TRANSFERENCIA'])->where('monto', '>', 0)->sum('monto');
                                    $eGastos = abs($c->pagos->where('monto', '<', 0)->sum('monto'));
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 px-4 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-400">{{ $c->fecha_apertura?->format('d/m/Y H:i') }}</td>
                                    <td class="py-2.5 px-4 text-cyan-600 font-semibold">{{ $c->sucursal?->nombre }}</td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-300">{{ $c->usuario?->nombre_completo }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono">Bs {{ number_format($c->monto_apertura, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400">Bs {{ number_format($vEfectivo, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono text-blue-600 dark:text-blue-400">Bs {{ number_format($vDigital, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono text-rose-600 dark:text-rose-400">Bs {{ number_format($eGastos, 2) }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                        Bs {{ number_format($c->monto_cierre ?? ($c->monto_apertura + $vEfectivo - $eGastos), 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $c->estado === 'ABIERTA' ? 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }}">
                                            {{ $c->estado }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="py-8 text-center text-slate-400">No se encontraron sesiones de caja.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                @endif

            </div>
        </div>

    </div>

    <!-- Script to open PDF stream in a new tab/window -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-pdf-window', data => {
                const payload = Array.isArray(data) ? data[0] : data;
                if (payload && payload.url) {
                    window.open(payload.url, '_blank');
                }
            });
        });
    </script>
</div>
