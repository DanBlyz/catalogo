<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-arrow-right-arrow-left text-brand-500"></i>
                    <span>Movimientos de Inventario (Kardex)</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Historial de entradas, salidas y transferencias entre sucursales
                </p>
            </div>
            
            <nav class="flex text-xs text-slate-400 font-medium">
                <a href="#" class="hover:text-brand-500 transition">Operaciones</a>
                <span class="mx-2">&sol;</span>
                <span class="text-slate-700 dark:text-slate-200">Movimientos</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">
        
        <!-- Main Card Container -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            
            <!-- Card Header & Action Buttons -->
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Kardex de Movimientos</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Total de registros: {{ $movimientos->total() }}</p>
                    </div>
                </div>

                <!-- Action Buttons: Ingreso, Salida, Transferencia -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <!-- Ingreso (Stock) -->
                    <button 
                        wire:click="openIngresoModal" 
                        class="flex-1 md:flex-none px-3.5 py-2 text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-plus text-xs"></i>
                        <span>Ingreso Stock</span>
                    </button>

                    <!-- Salida (Ajuste) -->
                    <button 
                        wire:click="openSalidaModal" 
                        class="flex-1 md:flex-none px-3.5 py-2 text-xs font-semibold text-white bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 rounded-xl shadow-md shadow-rose-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-minus text-xs"></i>
                        <span>Salida / Ajuste</span>
                    </button>

                    <!-- Transferencia -->
                    <button 
                        wire:click="openTransferenciaModal" 
                        class="flex-1 md:flex-none px-3.5 py-2 text-xs font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 rounded-xl shadow-md shadow-blue-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-right-left text-xs"></i>
                        <span>Transferencia</span>
                    </button>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6 space-y-4">
                
                <!-- Controls Bar: Per Page, Filters & Search -->
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

                    <!-- Tipo Filter Select -->
                    <div>
                        <select 
                            wire:model.live="tipoFilter" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="">-- Todos los Tipos --</option>
                            <option value="INGRESO">INGRESO</option>
                            <option value="SALIDA">SALIDA</option>
                            <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                        </select>
                    </div>

                    <!-- Sucursal Filter (Admin only) -->
                    <div>
                        @if (auth()->user()->esAdmin())
                            <select 
                                wire:model.live="sucursalFilter" 
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                                <option value="">-- Todas las Sucursales --</option>
                                @foreach ($todasSucursales as $suc)
                                    <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="px-3 py-2 text-xs font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                                <i class="fa-solid fa-store text-brand-500 mr-1"></i> {{ auth()->user()->sucursal?->nombre }}
                            </div>
                        @endif
                    </div>

                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Buscar por producto, SKU o motivo..." 
                            class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                        >
                    </div>

                </div>

                <!-- Movimientos Table -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[11px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Fecha / Hora</th>
                                <th class="py-3 px-4">Producto</th>
                                <th class="py-3 px-4">Sucursal</th>
                                <th class="py-3 px-4 text-center">Tipo</th>
                                <th class="py-3 px-4 text-right">Cantidad</th>
                                <th class="py-3 px-4 text-center">Stock (Ant &rarr; Nuevo)</th>
                                <th class="py-3 px-4">Motivo / Observación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                            @forelse ($movimientos as $index => $mov)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono font-medium">
                                        {{ $movimientos->firstItem() + $index }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                                        {{ $mov->fecha_movimiento ? $mov->fecha_movimiento->format('d/m/Y H:i') : $mov->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $mov->producto?->nombre ?? 'Producto Eliminado' }}</div>
                                        <div class="text-[10px] font-mono text-slate-400">{{ $mov->producto?->sku }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                                            {{ $mov->sucursal?->nombre }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if (str_contains($mov->tipo_movimiento, 'INGRESO'))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                <i class="fa-solid fa-arrow-down text-[9px]"></i> {{ $mov->tipo_movimiento }}
                                            </span>
                                        @elseif (str_contains($mov->tipo_movimiento, 'SALIDA'))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                                <i class="fa-solid fa-arrow-up text-[9px]"></i> {{ $mov->tipo_movimiento }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                                <i class="fa-solid fa-right-left text-[9px]"></i> {{ $mov->tipo_movimiento }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-extrabold text-slate-900 dark:text-white">
                                        {{ number_format($mov->cantidad, 0) }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono text-[11px]">
                                        <span class="text-slate-400">{{ number_format($mov->stock_anterior, 0) }}</span>
                                        <span class="text-slate-400 mx-1">&rarr;</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($mov->stock_nuevo, 0) }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                        {{ $mov->motivo ?? 'Sin motivo' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="fa-solid fa-arrow-right-arrow-left text-3xl text-slate-300 dark:text-slate-700"></i>
                                            <p class="text-xs font-medium">No se encontraron movimientos registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="pt-2">
                    {{ $movimientos->links() }}
                </div>

            </div>
        </div>

    </div>

    <!-- MODAL 1: INGRESO DE PRODUCTOS EN LOTE -->
    @if ($isIngresoModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeIngresoModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-3xl transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all space-y-4">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-circle-plus"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Ingreso de Stock (Recepción en Lote)</h3>
                                <p class="text-[11px] text-slate-400">Sucursal Destino: <strong class="text-brand-500">{{ auth()->user()->sucursal?->nombre }}</strong></p>
                            </div>
                        </div>
                        <button wire:click="closeIngresoModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Item Form -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700/80 space-y-3">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Agregar Producto a la Lista de Ingreso
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-end">
                            <!-- Producto Selector -->
                            <div class="sm:col-span-4">
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Producto</label>
                                <select 
                                    wire:model.live="selectedProductoIdForIngreso" 
                                    class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                                    <option value="">-- Seleccionar Producto --</option>
                                    @foreach ($allProductos as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->sku }})</option>
                                    @endforeach
                                </select>
                                @error('selectedProductoIdForIngreso') <span class="text-rose-500 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                            </div>

                            <!-- Cantidad -->
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Cantidad</label>
                                <input 
                                    type="number" 
                                    step="1"
                                    wire:model="cantidadIngreso" 
                                    placeholder="1" 
                                    class="w-full px-3 py-2 text-xs font-mono font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                                >
                                @error('cantidadIngreso') <span class="text-rose-500 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                            </div>

                            <!-- Costo Unitario -->
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Costo Unit. (Bs)</label>
                                <input 
                                    type="number" 
                                    step="0.01"
                                    wire:model="precioCompraIngreso" 
                                    placeholder="0.00" 
                                    class="w-full px-3 py-2 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                                >
                                @error('precioCompraIngreso') <span class="text-rose-500 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                            </div>

                            <!-- Precio Venta -->
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">P. Venta (Bs)</label>
                                <input 
                                    type="number" 
                                    step="0.01"
                                    wire:model="precioVentaIngreso" 
                                    placeholder="0.00" 
                                    class="w-full px-3 py-2 text-xs font-mono font-semibold text-emerald-600 dark:text-emerald-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                                >
                                @error('precioVentaIngreso') <span class="text-rose-500 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                            </div>

                            <!-- Add Button -->
                            <div class="sm:col-span-2">
                                <button 
                                    type="button" 
                                    wire:click="addItemToIngresoList" 
                                    class="w-full py-2 px-3 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition active:scale-[0.98] shadow-md flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>Agregar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Preview -->
                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[10px] font-bold uppercase tracking-wider">
                                    <th class="py-2.5 px-3 w-10 text-center">#</th>
                                    <th class="py-2.5 px-3">Producto</th>
                                    <th class="py-2.5 px-3 text-right">Cantidad</th>
                                    <th class="py-2.5 px-3 text-right">Costo Unit.</th>
                                    <th class="py-2.5 px-3 text-right">P. Venta</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                    <th class="py-2.5 px-3 text-center w-12">Quitar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                                @php $totalIngresoSum = 0; @endphp
                                @forelse ($ingresoItems as $idx => $item)
                                    @php $totalIngresoSum += $item['subtotal']; @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                        <td class="py-2 px-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                        <td class="py-2 px-3">
                                            <div class="font-semibold text-slate-900 dark:text-white">{{ $item['nombre'] }}</div>
                                            <div class="text-[10px] font-mono text-slate-400">{{ $item['sku'] }}</div>
                                        </td>
                                        <td class="py-2 px-3 text-right font-mono font-bold">{{ number_format($item['cantidad'], 0) }}</td>
                                        <td class="py-2 px-3 text-right font-mono">Bs {{ number_format($item['precio_compra'], 2) }}</td>
                                        <td class="py-2 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">Bs {{ number_format($item['precio_venta'], 2) }}</td>
                                        <td class="py-2 px-3 text-right font-mono font-bold text-slate-900 dark:text-white">Bs {{ number_format($item['subtotal'], 2) }}</td>
                                        <td class="py-2 px-3 text-center">
                                            <button 
                                                wire:click="removeItemFromIngresoList({{ $idx }})" 
                                                class="text-rose-500 hover:text-rose-700 transition p-1">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-slate-400 text-xs">
                                            No hay productos agregados a la lista de ingreso todavía.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (!empty($ingresoItems))
                                <tfoot>
                                    <tr class="bg-slate-50 dark:bg-slate-800/80 font-bold text-xs">
                                        <td colspan="5" class="py-2.5 px-3 text-right uppercase text-slate-600 dark:text-slate-300">Total Ingreso:</td>
                                        <td class="py-2.5 px-3 text-right font-mono text-emerald-600 dark:text-emerald-400">Bs {{ number_format($totalIngresoSum, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>


                    <!-- Observaciones -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Observaciones de Ingreso / Nota de Compra
                        </label>
                        <input 
                            type="text" 
                            wire:model="observacionesIngreso" 
                            placeholder="Ej: Factura #1234, Recepción de proveedor..." 
                            class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"
                        >
                    </div>

                    <!-- Footer Actions -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button 
                            type="button" 
                            wire:click="closeIngresoModal" 
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Cancelar
                        </button>
                        <button 
                            type="button" 
                            wire:click="saveIngresoLote" 
                            class="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl shadow-md transition active:scale-[0.98]">
                            Procesar Ingresos
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: SALIDA DIRECTA DE PRODUCTO -->
    @if ($isSalidaModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeSalidaModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all space-y-4">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-circle-minus"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Salida / Ajuste de Stock</h3>
                                <p class="text-[11px] text-slate-400">Sucursal: <strong class="text-brand-500">{{ auth()->user()->sucursal?->nombre }}</strong></p>
                            </div>
                        </div>
                        <button wire:click="closeSalidaModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form wire:submit.prevent="saveSalida" class="space-y-4">
                        
                        <!-- Producto Selector -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Seleccionar Producto <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                wire:model.live="selectedProductoIdForSalida" 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                <option value="">-- Seleccionar Producto --</option>
                                @foreach ($allProductos as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->sku }})</option>
                                @endforeach
                            </select>
                            @error('selectedProductoIdForSalida') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Available Stock Indicator -->
                        @if ($selectedProductoIdForSalida)
                            <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs flex items-center justify-between">
                                <span class="text-amber-700 dark:text-amber-300 font-semibold">Stock Actual Disponible:</span>
                                <span class="font-mono font-bold text-amber-700 dark:text-amber-300 text-sm">
                                    {{ number_format($stockActualSeleccionadoSalida, 0) }}
                                </span>
                            </div>
                        @endif

                        <!-- Cantidad Salida -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Cantidad a Retirar <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                step="1"
                                wire:model="cantidadSalida" 
                                placeholder="1" 
                                class="w-full px-3.5 py-2.5 text-xs font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition"
                            >
                            @error('cantidadSalida') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Motivo -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Motivo de Salida <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                wire:model="motivoSalida" 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                                <option value="PÉRDIDA / DAÑO">PÉRDIDA / DAÑO</option>
                                <option value="PRODUCTO VENCIDO">PRODUCTO VENCIDO</option>
                                <option value="ROBO / FALTANTE">ROBO / FALTANTE</option>
                                <option value="USO INTERNO">USO INTERNO</option>
                                <option value="AJUSTE DE INVENTARIO">AJUSTE DE INVENTARIO</option>
                            </select>
                            @error('motivoSalida') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Observaciones Adicionales -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Detalles / Observaciones
                            </label>
                            <textarea 
                                wire:model="observacionesSalida" 
                                rows="2" 
                                placeholder="Detalles de la baja de stock..." 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-rose-500 focus:outline-none transition"
                            ></textarea>
                            @error('observacionesSalida') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="closeSalidaModal" 
                                class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 rounded-xl shadow-md transition active:scale-[0.98]">
                                Confirmar Salida
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: TRANSFERENCIA ENTRE SUCURSALES -->
    @if ($isTransferenciaModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeTransferenciaModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all space-y-4">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-right-left"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Transferencia de Productos</h3>
                                <p class="text-[11px] text-slate-400">Origen: <strong class="text-brand-500">{{ auth()->user()->sucursal?->nombre }}</strong></p>
                            </div>
                        </div>
                        <button wire:click="closeTransferenciaModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form wire:submit.prevent="saveTransferencia" class="space-y-4">
                        
                        <!-- Producto Selector -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Seleccionar Producto <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                wire:model.live="selectedProductoIdForTransferencia" 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                                <option value="">-- Seleccionar Producto --</option>
                                @foreach ($allProductos as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->sku }})</option>
                                @endforeach
                            </select>
                            @error('selectedProductoIdForTransferencia') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Available Stock Indicator -->
                        @if ($selectedProductoIdForTransferencia)
                            <div class="p-3 bg-blue-500/10 border border-blue-500/20 rounded-xl text-xs flex items-center justify-between">
                                <span class="text-blue-700 dark:text-blue-300 font-semibold">Stock en Origen Disponible:</span>
                                <span class="font-mono font-bold text-blue-700 dark:text-blue-300 text-sm">
                                    {{ number_format($stockActualSeleccionadoTransferencia, 0) }}
                                </span>
                            </div>
                        @endif

                        <!-- Sucursal Destino -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Sucursal Destino <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                wire:model="sucursalDestinoId" 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                                <option value="">-- Seleccionar Sucursal Destino --</option>
                                @foreach ($sucursalesDestino as $suc)
                                    <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                                @endforeach
                            </select>
                            @error('sucursalDestinoId') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Cantidad a Transferir -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Cantidad a Transferir <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                step="1"
                                wire:model="cantidadTransferencia" 
                                placeholder="1" 
                                class="w-full px-3.5 py-2.5 text-xs font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                            >
                            @error('cantidadTransferencia') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Observaciones de Traspaso
                            </label>
                            <textarea 
                                wire:model="observacionesTransferencia" 
                                rows="2" 
                                placeholder="Motivo o nota de la transferencia..." 
                                class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                            ></textarea>
                            @error('observacionesTransferencia') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="closeTransferenciaModal" 
                                class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 rounded-xl shadow-md transition active:scale-[0.98]">
                                Confirmar Transferencia
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
