<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-gauge-high text-brand-500"></i>
                    <span>Panel de Control Principal</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Bienvenido, <span class="font-bold text-slate-700 dark:text-slate-300">{{ Auth::user()->name }}</span>. Resumen en tiempo real de operaciones, ventas e inventario.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Branch Selector for Admin -->
                @if (Auth::user()->esAdmin())
                    <div class="relative min-w-[180px]">
                        <select 
                            wire:model.live="sucursalId" 
                            class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-1.5 px-3 text-xs font-semibold text-slate-800 dark:text-slate-200 shadow-sm focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="">-- Todas las Sucursales --</option>
                            @foreach ($todasSucursales as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Quick POS Button -->
                <a href="{{ route('operacion.ventas') }}" class="px-3.5 py-2 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl shadow-md transition flex items-center gap-1.5 active:scale-[0.98]">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Punto de Venta (POS)</span>
                </a>

                <!-- Quick Cash Register Button -->
                <a href="{{ route('operacion.cajas') }}" class="px-3.5 py-2 text-xs font-bold text-white bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 rounded-xl shadow-md transition flex items-center gap-1.5 active:scale-[0.98]">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Caja</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- 1. KPI Metric Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Ventas de Hoy -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Ventas Realizadas Hoy</p>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            Bs {{ number_format($ventasHoyMonto, 2) }}
                        </h3>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                            <i class="fa-solid fa-receipt"></i> {{ $ventasHoyCantidad }} {{ $ventasHoyCantidad === 1 ? 'comprobante' : 'comprobantes' }}
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white flex items-center justify-center text-xl shrink-0 transition-all duration-200">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Recaudación Digital vs Efectivo -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Desglose Métodos de Pago</p>
                        <div class="flex items-center gap-2 mt-1 font-mono text-xs font-bold">
                            <span class="text-emerald-600 dark:text-emerald-400"><i class="fa-solid fa-money-bill"></i> Bs {{ number_format($ingresosEfectivoHoy, 2) }}</span>
                            <span class="text-slate-300 dark:text-slate-700">|</span>
                            <span class="text-blue-600 dark:text-blue-400"><i class="fa-solid fa-qrcode"></i> Bs {{ number_format($ingresosDigitalHoy, 2) }}</span>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 mt-1.5">
                            Efectivo / QR y Transferencia
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 group-hover:bg-blue-500 group-hover:text-white flex items-center justify-center text-xl shrink-0 transition-all duration-200">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Stock Crítico / Alerta -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Stock Crítico (≤ 5 u.)</p>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                            {{ $totalStockCriticoCount }} {{ $totalStockCriticoCount === 1 ? 'producto' : 'productos' }}
                        </h3>
                        <a href="{{ route('operacion.movimientos') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500 hover:text-rose-600 transition mt-1">
                            <i class="fa-solid fa-circle-exclamation"></i> Reabastecer inventario &rarr;
                        </a>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-500 group-hover:bg-rose-500 group-hover:text-white flex items-center justify-center text-xl shrink-0 transition-all duration-200">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>

            <!-- Card 4: Estado de Caja Activa -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Estado de Tu Caja</p>
                        @if ($cajaActiva)
                            <h3 class="text-lg font-black text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                ABIERTA
                            </h3>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5 font-mono">
                                Inicial: Bs {{ number_format($cajaActiva->monto_apertura, 2) }}
                            </span>
                        @else
                            <h3 class="text-lg font-black text-rose-500 mt-1 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                CERRADA
                            </h3>
                            <a href="{{ route('operacion.cajas') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-500 hover:underline mt-0.5">
                                Abrir caja ahora &rarr;
                            </a>
                        @endif
                    </div>
                    <div class="w-12 h-12 rounded-2xl {{ $cajaActiva ? 'bg-cyan-500/10 text-cyan-500 group-hover:bg-cyan-500' : 'bg-slate-500/10 text-slate-400 group-hover:bg-slate-600' }} group-hover:text-white flex items-center justify-center text-xl shrink-0 transition-all duration-200">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- 2. Charts & Top Products Row -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left: Ventas de los Últimos 7 Días (Gráfico) -->
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-chart-column text-brand-500"></i>
                            <span>Ventas de los Últimos 7 Días</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Evolución de ingresos y volumen diario de operaciones</p>
                    </div>
                </div>

                <!-- CSS Bar Chart Container -->
                <div class="pt-8 pb-4 px-2">
                    <div class="h-48 flex items-end justify-between gap-2 sm:gap-4 border-b border-slate-200 dark:border-slate-700 pb-2">
                        @foreach ($ventasUltimosDias as $vd)
                            @php
                                $heightPct = $maxMontoGrafica > 0 ? min(100, max(8, ($vd['monto'] / $maxMontoGrafica) * 100)) : 8;
                            @endphp
                            <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                                
                                <!-- Tooltip on Hover -->
                                <div class="absolute -top-10 hidden group-hover:flex flex-col items-center z-20 pointer-events-none">
                                    <div class="bg-slate-900 text-white text-[10px] font-bold py-1 px-2.5 rounded-lg shadow-lg font-mono whitespace-nowrap">
                                        Bs {{ number_format($vd['monto'], 2) }} ({{ $vd['count'] }} vtas)
                                    </div>
                                    <div class="w-2 h-2 bg-slate-900 rotate-45 -mt-1"></div>
                                </div>

                                <!-- Dynamic Bar -->
                                <div 
                                    style="height: {{ $heightPct }}%;" 
                                    class="w-full max-w-[36px] bg-gradient-to-t from-brand-600 to-indigo-500 group-hover:from-brand-500 group-hover:to-indigo-400 rounded-t-xl transition-all duration-300 shadow-sm relative">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- X-Axis Labels -->
                    <div class="flex justify-between gap-2 sm:gap-4 mt-2">
                        @foreach ($ventasUltimosDias as $vd)
                            <div class="flex-1 text-center text-[11px] font-bold text-slate-500 dark:text-slate-400 capitalize truncate">
                                {{ $vd['dia'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Top 5 Productos Más Vendidos del Mes -->
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between">
                <div class="pb-4 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-fire text-amber-500"></i>
                        <span>Top 5 Productos del Mes</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Productos con mayor volumen de unidades vendidas</p>
                </div>

                <div class="space-y-4 my-2">
                    @forelse ($topProductos as $idx => $tp)
                        @php
                            $pctTop = $maxCantTop > 0 ? min(100, max(10, ($tp->total_cant / $maxCantTop) * 100)) : 10;
                        @endphp
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-5 h-5 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-[10px] flex items-center justify-center shrink-0">
                                        {{ $idx + 1 }}°
                                    </span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $tp->producto?->nombre }}</span>
                                </div>
                                <span class="font-mono font-bold text-slate-900 dark:text-white shrink-0 ml-2">
                                    {{ number_format($tp->total_cant, 0) }} u.
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                <div 
                                    style="width: {{ $pctTop }}%;" 
                                    class="bg-gradient-to-r from-amber-500 to-orange-500 h-full rounded-full transition-all duration-500">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400">
                            <i class="fa-solid fa-box-open text-2xl mb-1 block"></i>
                            No hay registros de ventas en el mes.
                        </div>
                    @endforelse
                </div>

                <div class="pt-2 text-right">
                    <a href="{{ route('administracion.reportes') }}" class="text-xs font-bold text-brand-500 hover:underline">
                        Ver ranking completo en reportes &rarr;
                    </a>
                </div>
            </div>

        </div>

        <!-- 3. Low Stock Alerts & Recent Sales Row -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left: Alerta de Inventario Crítico -->
            <div class="lg:col-span-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                            <span>Stock Crítico o Agotado</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Productos con 5 o menos unidades disponibles</p>
                    </div>
                    <a href="{{ route('operacion.movimientos') }}" class="px-2.5 py-1 text-[11px] font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-lg shadow-sm transition">
                        + Ingresar Stock
                    </a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 mt-2">
                    @forelse ($productosStockBajo as $pb)
                        <div class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div class="truncate">
                                <div class="font-bold text-slate-900 dark:text-white truncate">{{ $pb->producto?->nombre }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">SKU: {{ $pb->producto?->sku }} &bull; {{ $pb->sucursal?->nombre }}</div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <span class="px-2.5 py-1 rounded-full font-mono font-extrabold text-[11px] {{ $pb->stock_actual <= 0 ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400' }}">
                                    {{ number_format($pb->stock_actual, 0) }} u.
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400">
                            <i class="fa-solid fa-circle-check text-2xl text-emerald-500 mb-1 block"></i>
                            ¡Excelente! No hay productos con stock crítico.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Últimas Ventas Realizadas -->
            <div class="lg:col-span-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-brand-500"></i>
                            <span>Últimas Ventas Emitidas</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Comprobantes más recientes registrados</p>
                    </div>
                    <a href="{{ route('operacion.ventas-dia') }}" class="text-xs font-bold text-brand-500 hover:underline">
                        Ver todas &rarr;
                    </a>

                </div>

                <div class="overflow-x-auto mt-2">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-400 uppercase border-b border-slate-100 dark:border-slate-800">
                                <th class="pb-2">Recibo</th>
                                <th class="pb-2">Cliente</th>
                                <th class="pb-2 text-center">Pago</th>
                                <th class="pb-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                            @forelse ($ventasRecientes as $vr)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="py-2.5 font-mono font-bold text-slate-900 dark:text-white">
                                        {{ $vr->numero_recibo }}
                                    </td>
                                    <td class="py-2.5 font-medium truncate max-w-[120px]">
                                        {{ $vr->cliente?->nombre_razon_social ?? 'Cliente S/N' }}
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $vr->metodo_pago_principal === 'EFECTIVO' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400' }}">
                                            {{ $vr->metodo_pago_principal }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-right font-mono font-bold text-slate-900 dark:text-white">
                                        Bs {{ number_format($vr->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-8 text-center text-slate-400">No hay ventas recientes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>
